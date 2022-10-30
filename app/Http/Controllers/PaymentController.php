<?php

namespace App\Http\Controllers;

use App\Mail\MaitToSubcontractor;
use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Trade;
use App\Models\Document;
use App\Models\Proposal;
use App\Models\Payment;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\DocumentType;
use App\Models\DocumentFile;
use App\Models\Subcontractor;
use App\Models\FFETrade;
use App\Models\FFECategory;
use Gate;
use Carbon\Carbon;

use PDF;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $id)
    {
        if(Gate::denies('add')) {
               return abort('401');
         }

        $project  = Project::find($id);  
         
        if(!$project){
            return redirect()->back();
        }

        $proposalsQry = $project->proposals()->IsAwarded();

        $proposals = $proposalsQry->get();

        ($request->filled('trade')) ? $proposalsQry->where('trade_id', $request->trade) : '';

        $proposal = $proposalsQry->first();

        $vendors = Vendor::orderBy('name')->get();

        $trades = $proposals->map(function($prpsl){
             return $prpsl->trade;
        });

        //if(!@$project->proposals()->exists()){
             $allTrades = Trade::orderBy('name')->get();
       //  }

        $totalAmount = $this->proposalTotalAmount($proposal);
        $dueAmount = $this->proposalDueTotalAmount($proposal);

        return view('projects.includes.payments-create',compact('id','proposal','vendors','trades','allTrades','totalAmount','dueAmount'));
    }  



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
          if(Gate::denies('add')) {
               return abort('401');
        } 

        
        $proposal  = Proposal::find($id);  
         
        if(!$proposal &&  $id == null){
            return redirect('/');
        }

        $totalDueMount =  $this->proposalDueTotalAmount($proposal);

        $data = $request->except('_token');

       $type = ($request->filled('type')) ?  $request->type : Payment::VENDOR;

        if($id == 0){
             
             $request->validate([
                   $type.'_trade_id' => 'required|exists:trades,id',
                   'vendor_id' => 'required|exists:vendors,id',
                   'payment_amount' => ['required',
                        function ($attribute, $value, $fail) use ($totalDueMount){
                          if (!request()->filled('vendor_id') && $value > $totalDueMount ) {
                              $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                          }
                      }
                    ]
                  // 'status' => 'required'
              ]
          );

          $data['trade_id'] = $data[$type.'_trade_id'];

        }else{

            $request->validate([
                  'subcontractor_id' => ['required',
                  'exists:subcontractors,id'],
                   $type.'_trade_id' => 'required|exists:trades,id',
                   'vendor_id' => function ($attribute, $value, $fail){
                        if (!$value &&  request()->type  == Payment::VENDOR) {
                            $fail('Vendor Id is required');
                        }
                    },
                   'payment_amount' => ['required',
                        function ($attribute, $value, $fail) use ($totalDueMount){
                          if (request()->type  != Payment::VENDOR && !request()->filled('vendor_id') && $value > $totalDueMount ) {
                              $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                          }
                      }
                    ]
                  // 'status' => 'required'
              ]
          );
       
         $data['trade_id'] = $data[$type.'_trade_id'];
        }
        

        $project_id = @$proposal->project->id;
        $project_id = (!$project_id) ? $request->project_id : $project_id;

        $data['project_id']  = (int) $project_id;
        $data['proposal_id'] = ($id > 0) ? $id : null ;

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d');
        
        $data['total_amount'] = @$this->proposalTotalAmount(@$proposal);

        $project = Project::find($project_id);

        $project_slug = \Str::slug($project->name);

        $trade = Trade::find(@$data['trade_id']);

        $trade_slug = @$trade->slug;

        $subcontractor = Subcontractor::find(@$request->subcontractor_id);

        $subcontractor_slug = @$subcontractor->slug;

        if(@!$proposal){
             $vendor  = Vendor::find($request->vendor_id);
             $subcontractor_slug = @$vendor->slug;
        }

        $public_path = public_path().'/';

        $folderPath = Document::INVOICES."/";

        $folderPath .= $project_slug.'/'.$trade_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        $folderPath2 = Document::LIEN_RELEASES."/";

        $folderPath2 .= $project_slug.'/'.$trade_slug;

        \File::makeDirectory($public_path.$folderPath2, $mode = 0777, true, true);

        $data['file'] = '';

        $payment = Payment::create($data);
       
        $document_type = DocumentType::where('name', DocumentType::INVOICE)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$proposal->subcontractor->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['payment_id' => $payment->id,
                    'document_type_id' => $document_type->id
                     ],
                     ['name' => $name, 'slug' => $slug,
                     'payment_id'       => $payment->id,
                     'proposal_id'      => $id,
                     'document_type_id' => $document_type->id,
                     'subcontractor_id' => @$proposal->subcontractor->id
                     ]
                 );


        if($request->hasFile('file')){

              $file = $request->file('file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $subcontractor_slug.'-'.time().'.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

            $payment->update(['file' => $fileName]);

            $document->files()->create($fileArr);
        }

        if($request->hasFile('unconditional_lien_release_file')){
              
              $document_type = DocumentType::where('name', DocumentType::LIEN_RELEASE)
                         ->first();

              $name = @$project->name.' Unconditional '.@$document_type->name.' '.@$proposal->subcontractor->name;                
              $slug = @\Str::slug($name);              

              $document = $project->documents()
                         ->firstOrCreate(['payment_id' => $payment->id,
                          'document_type_id' => $document_type->id],
                           ['name' => $name, 'slug' => $slug,
                           'payment_id'       => $payment->id,
                           'proposal_id'      => $id,
                           'document_type_id' => $document_type->id,
                           'subcontractor_id' => @$proposal->subcontractor->id
                           ]
                       );

              $file = $request->file('unconditional_lien_release_file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $subcontractor_slug.'-'.time().'1.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath2, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

            $payment->update(['unconditional_lien_release_file' => $fileName]);

            $document->files()->create($fileArr);
        }

        if($request->hasFile('conditional_lien_release_file')){
              
              $document_type = DocumentType::where('name', DocumentType::LIEN_RELEASE)
                         ->first();

              $name = @$project->name.' Conditional '.@$document_type->name.' '.@$proposal->subcontractor->name;                
              $slug = @\Str::slug($name);                

              $document = $project->documents()
                         ->firstOrCreate(['payment_id' => $payment->id,
                          'document_type_id' => $document_type->id],
                           ['name' => $name, 'slug' => $slug,
                           'payment_id'       => $payment->id,
                           'proposal_id'      => $id,
                           'document_type_id' => $document_type->id,
                           'subcontractor_id' => @$proposal->subcontractor->id
                           ]
                       );

              $file = $request->file('conditional_lien_release_file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $subcontractor_slug.'-'.time().'2.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath2, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

            $payment->update(['conditional_lien_release_file' => $fileName]);

            $document->files()->create($fileArr);
        }

        

        return redirect(route('projects.show',['project' => $project_id]).'#payments')->with('message', 'Payment Created Successfully!');
    }
     
    public function proposalTotalAmount($proposal){

       $total =  (float) @$proposal->material + (float) @$proposal->labour_cost + (float) @$proposal->subcontractor_price;  

         if(!@$proposal->changeOrders)
         {
             return $total;
         }

         foreach(@$proposal->changeOrders as $k => $order){
           if($order->type == \App\Models\ChangeOrder::ADD ){
             $total += $order->subcontractor_price;
           }
           else{
             $total -= $order->subcontractor_price;
           }
         }

         return $total;
    } 

    public function proposalDueTotalAmount($proposal){

         $total =  $this->proposalTotalAmount($proposal);  
     
         $payments = Payment::whereProposalId(@$proposal->id)
                    ->whereNull('vendor_id')->sum('payment_amount');

         $due = (float) $total - (float) $payments;

        return round($due,2);
    } 

    public function proposalDueAmount($proposal,$payment_id){

         $total =  $this->proposalTotalAmount($proposal);  

         $payments = Payment::whereProposalId(@$proposal->id)
         ->whereNull('vendor_id')                   
         ->where('id','<=', $payment_id)->sum('payment_amount');

         $due = (float) $total - (float) $payments;

         return round($due,2);
    } 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
         if(Gate::denies('edit')) {
           return abort('401');
        } 
        $payment = Payment::find($id);  
        $subcontractor = @$payment->subcontractor;

        $project = @$payment->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @\Str::slug($payment->trade->name);

        $project_type_slug = @$project->project_type->slug;

        $folderPath = Document::INVOICES."/";

        $folderPath .= "$project_slug/$trade_slug/";

        $folderPath2 = Document::LIEN_RELEASES."/";

        $folderPath2 .= "$project_slug/$trade_slug/";

        
        $payment->file = @($payment->file) ? $folderPath.$payment->file : '';
        $payment->unconditional_lien_release_file = @($payment->unconditional_lien_release_file) ? $folderPath2.$payment->unconditional_lien_release_file : '';
        $payment->conditional_lien_release_file = @($payment->conditional_lien_release_file) ? $folderPath2.$payment->conditional_lien_release_file : '';

        $payment->date = @($payment->date) ? Carbon::parse($payment->date)->format('m-d-Y') : '' ;

        $vendors = Vendor::orderBy('name')->get(); 

        $totalAmount = $this->proposalTotalAmount($payment->proposal);
        $dueAmount = $this->proposalDueTotalAmount($payment->proposal);
       
        //dd($totalAmount);

        $proposalsQry = $project->proposals()->IsAwarded();

        $proposals = $proposalsQry->get();

        ($request->filled('trade')) ? $proposalsQry->where('trade_id', $request->trade) : '';

        $trades = $proposals->map(function($prpsl){
             return $prpsl->trade;
        });

        $allTrades = Trade::orderBy('name')->get();
         
         session()->flash('url', route('projects.show',['project' => $payment->project_id]).'?#payments'); 


         // dd($payment);

        return view('projects.includes.payments-edit',compact('subcontractor','payment','vendors','totalAmount','dueAmount','trades','allTrades'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     jmmmmmjjjjjjj* Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Gate::denies('update')) {
               return abort('401');
        } 

        $data = $request->except('_token');

        $payment = Payment::find($id);

        $totalDueMount =  $this->proposalDueTotalAmount($payment->proposal);
     
        $data = $request->except('_token');

         $type = ($request->filled('type')) ?  $request->type : Payment::VENDOR;

         if($payment->proposal){

           $request->validate([
                // 'subcontractor_id' => ['required',
                // 'exists:subcontractors,id'],
                 $type.'_trade_id'  => 'required|exists:trades,id',
                 'vendor_id' => function ($attribute, $value, $fail){
                      if (!$value &&  request()->type  == Payment::VENDOR) {
                          $fail('Vendor Id is required');
                      }
                  },
                'payment_amount' => ['required',
                      function ($attribute, $value, $fail) use ($totalDueMount,$payment){
                        if (request()->type  != Payment::VENDOR &&  $value > ((float) $totalDueMount + $payment->payment_amount) ) {
                            $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                        }
                    }
                  ]
                //'payment_amount' => 'required|lte:'.((int) $totalDueMount + $payment->payment_amount),
                // 'status' => 'required'
            // ],[
            //   'payment_amount.lte' => 'The payment amount must be less than or equal '.$totalDueMount.'.'
            ]
        );
          
          $data['trade_id'] = $data[$type.'_trade_id'];
          $data['vendor_id'] = (request()->type  == Payment::SUBCONTRACTOR) 
                               ? null :  $data['vendor_id'] ;
    
        // dd($data);

        }else{

            $request->validate([
                    $type.'_trade_id'  => 'required|exists:trades,id',
                   'vendor_id' => 'required|exists:vendors,id',
                   'payment_amount' => ['required',
                        function ($attribute, $value, $fail) use ($totalDueMount){
                          if ( !request()->filled('vendor_id') && $value > $totalDueMount ) {
                              $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                          }
                      }
                    ]
                  // 'status' => 'required'
              ]
          );  

          $data['trade_id'] = $data[$type.'_trade_id'];

        }

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d');


        $project = @$payment->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @$payment->trade->slug;

        $subcontractor_slug = @$payment->subcontractor->slug;

         if(@!$payment->proposal){
             $vendor  = Vendor::find($request->vendor_id);
             $subcontractor_slug  =  @$vendor->slug;
        }

        $public_path = public_path().'/';

        $folderPath = Document::INVOICES."/";

        $folderPath .= $project_slug.'/'.$trade_slug;
        
        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        $folderPath2 = Document::LIEN_RELEASES."/";

        $folderPath2 .= $project_slug.'/'.$trade_slug;
        
        \File::makeDirectory($public_path.$folderPath2, $mode = 0777, true, true);
        
        $document_type = DocumentType::where('name', DocumentType::INVOICE)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$payment->subcontractor->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['payment_id' => $payment->id,
                          'document_type_id' => $document_type->id],
                     ['name' => $name, 'slug' => $slug,
                     'payment_id'       => $payment->id,
                     // 'proposal_id'      => $id,
                     'document_type_id' => $document_type->id,
                     'subcontractor_id' => @$proposal->subcontractor->id
                     ]
                 );


        if($request->hasFile('file')){
              @unlink($folderPath.'/'.$payment->file);
              $file = $request->file('file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $subcontractor_slug.'-'.time().'.'. $file->getClientOriginalExtension();

             $file->storeAs($folderPath, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                          'name' => $name,
                          'date' => $date,
                          'month' => $month,
                          'year' => $year
                          ];

           @$document->files()->delete();             
            $document->files()->create($fileArr);
            $data['file'] = $fileName;
        }

         if($request->hasFile('unconditional_lien_release_file')){
              
              $document_type = DocumentType::where('name', DocumentType::LIEN_RELEASE)
                         ->first();

              $name = @$project->name.' Unconditional '.@$document_type->name.' '.@$proposal->subcontractor->name;                
              $slug = @\Str::slug($name);                

              $document = $project->documents()
                         ->firstOrCreate(['payment_id' => $payment->id,
                          'document_type_id' => $document_type->id],
                           ['name' => $name, 'slug' => $slug,
                           'payment_id'       => $payment->id,
                           // 'proposal_id'      => $id,
                           'document_type_id' => $document_type->id,
                           'subcontractor_id' => @$proposal->subcontractor->id
                           ]
                       );

              $file = $request->file('unconditional_lien_release_file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $subcontractor_slug.'-'.time().'1.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath2, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];


            $document->files()->create($fileArr);
            $data['unconditional_lien_release_file'] = $fileName;
        }

        if($request->hasFile('conditional_lien_release_file')){
              
              $document_type = DocumentType::where('name', DocumentType::LIEN_RELEASE)
                         ->first();

              $name = @$project->name.' Conditional '.@$document_type->name.' '.@$proposal->subcontractor->name;                
              $slug = @\Str::slug($name);                

              $document = $project->documents()
                         ->firstOrCreate(['payment_id' => $payment->id,
                          'document_type_id' => $document_type->id],
                           ['name' => $name, 'slug' => $slug,
                           'payment_id'       => $payment->id,
                           // 'proposal_id'      => $id,
                           'document_type_id' => $document_type->id,
                           'subcontractor_id' => @$proposal->subcontractor->id
                           ]
                       );

              $file = $request->file('conditional_lien_release_file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $subcontractor_slug.'-'.time().'2.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath2, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

            $document->files()->create($fileArr);
             $data['conditional_lien_release_file'] = $fileName;

        }


        $payment->update($data);

        
        return redirect(route('projects.show',['project' => $payment->project_id]).'?#payments')->with('message', 'Payment Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

         $payment = Payment::find($id);

         $project = @$payment->project;

         $project_slug = \Str::slug($project->name);

         $trade_slug = @\Str::slug($payment->trade->name);

         $public_path = public_path().'/';

         $folderPath = Document::INVOICES."/";

         $folderPath .= "$project_slug/$trade_slug/";

         $folderPath2 = Document::LIEN_RELEASES."/";

         $folderPath2 .= "$project_slug/$trade_slug/";

         $path = @public_path().'/'.$folderPath;
         $path2 = @public_path().'/'.$folderPath2;

         $file = @$payment->file;
         $unconditional_lien_release_file = @$payment->unconditional_lien_release_file;
         $conditional_lien_release_file = @$payment->conditional_lien_release_file;
         
         $aPath = public_path().'/'. Document::INVOICES."/".Document::ARCHIEVED; 
         \File::makeDirectory($aPath, $mode = 0777, true, true);
         $aPath2 = public_path().'/'. Document::LIEN_RELEASES."/".Document::ARCHIEVED; 
         \File::makeDirectory($aPath2, $mode = 0777, true, true);

        @\File::copy($path.$file, $aPath.'/'.$file);
        @\File::copy($path2.$conditional_lien_release_file, $aPath2.'/'.$conditional_lien_release_file);
        @\File::copy($path2.$unconditional_lien_release_file, $aPath2.'/'
          .$unconditional_lien_release_file);
        @unlink($path.$file);
        @unlink($path2.$conditional_lien_release_file);
        @unlink($path2.$unconditional_lien_release_file);

         $project->documents()
                    ->where(['payment_id' => $id])->delete();

         $payment->delete();

        return redirect()->back()->with('message', 'Payment Delete Successfully!');
    }


     public function destroyFile($id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

          $path = request()->path;

          $payment = Payment::find($id);

          $file = @end(explode('/', $path));

          $publicPath = public_path().'/';

          $folder = Document::INVOICES;
          if (str_contains($path, Document::LIEN_RELEASES)) { 
             $folder = Document::LIEN_RELEASES;
          }

          $aPath = $publicPath.$folder."/".Document::ARCHIEVED;

          @\File::makeDirectory($aPath, $mode = 0777, true, true);

           @\File::copy($publicPath.$path, $aPath.'/'.$file);

          $docFile  = DocumentFile::whereFile($file)->firstOrFail();

          $coulumn = 'file';

          $coulumn = ( $file == @$payment->conditional_lien_release_file ) ? 'conditional_lien_release_file' : ( $file == @$payment->unconditional_lien_release_file ? 'unconditional_lien_release_file' : $coulumn);  
          
          @$docFile->delete();  

          $payment->update([$coulumn => '']);

          @unlink($path);

         return redirect()->back()->with('message', 'File Delete Successfully!');
    }

    public function downloadPDF($id,$view = false){

        $project = Project::find($id); 
        $trades = $project->trades()->get();
        $catids = @($trades->pluck('category_id'))->unique();
        $categories = Category::whereIn('id',$catids)->get(); 
        $pTrades = [];

        $trade_ids = @$project->payments->whereNotNull('trade_id')
                       ->pluck('trade_id');  
        $pTrades = Trade::whereIn('id',$trade_ids)->get();  

        if($categories->count() == 0){                 
              $catids = @($pTrades->pluck('category_id'))->unique();
              $categories = Category::whereIn('id',$catids)->get(); 
         }
         if($pTrades){
            $trades = $trades->merge($pTrades);
         }

        $pdf = PDF::loadView('projects.includes.budget-pdf',
          ['paymentCategories' => $categories,
          'trades' => $trades,'project' => $project]
        );

        $slug = \Str::slug($project->name);

        if($view){
         // return $pdf->stream('project_'.$slug .'_budget.pdf');
         return $pdf->setPaper('a4')->output();
        }

        return $pdf->download($slug.'-budget.pdf');

    }

    public function totalDownloadPDF($id,$view = false){

        $project = Project::find($id); 
        $trades = $project->trades()->get();
        $catids = @($trades->pluck('category_id'))->unique();
        $categories = Category::whereIn('id',$catids)->get(); 
        $pTrades = [];

        $trade_ids = @$project->payments->whereNotNull('trade_id')
                       ->pluck('trade_id');  
        $pTrades = Trade::whereIn('id',$trade_ids)->get();  

        if($categories->count() == 0){                 
              $catids = @($pTrades->pluck('category_id'))->unique();
              $categories = Category::whereIn('id',$catids)->get(); 
         }
         if($pTrades){
            $trades = $trades->merge($pTrades);
         }

        $ffe_trades = $project->ffe_trades()->get();

        $ffe_catids = @($ffe_trades->pluck('category_id'))->unique();
        $ffe_categories = FFECategory::whereIn('id',$ffe_catids)->get(); 
        $ffe_pTrades = [];

        $ffe_trade_ids = @$project->ffe_payments->whereNotNull('f_f_e_trade_id')
       ->pluck('f_f_e_trade_id'); 

        $ffe_pTrades = FFETrade::whereIn('id',$ffe_trade_ids)->get();  

        if($ffe_categories->count() == 0){                 
              $catids = @($ffe_pTrades->pluck('category_id'))->unique();
              $ffe_categories = FFECategory::whereIn('id',$catids)->get(); 
         }

         if($ffe_pTrades){
            $ffe_trades = $ffe_trades->merge($ffe_pTrades);
         }

        $t = ( request()->t == 1 ) ? 'details' : 'summary'; 

        $pdf = PDF::loadView('projects.includes.construction-cost-'.$t.'-pdf',
          ['paymentCategories' => $categories,
          'ffePaymentCategories' => $ffe_categories,
          'ffe_pTrades' => $ffe_pTrades,'trades' => $trades,
          'pTrades' => $pTrades,
          'project' => $project]
        );

        //$view = true;

        $slug = \Str::slug($project->name); 

        if($view){
         //return $pdf->stream('project_'.$slug .'_budget.pdf');
         return $pdf->setPaper('a4')->output();
        }

        return $pdf->download($slug.'-'.$t.'.pdf');

    }

    public function sendMail(Request $request, $id){

       set_time_limit(0);
        $project = Project::find($id); 
         $slug = \Str::slug($project->name);
        $data = [
          'heading' => '',
          'plans' => '',
          'file' => '',
          'subject' => $request->subject,
          'content' => $request->message,
        ];
       
        $pdffile = $this->downloadPDF($id,true);

        $ccUsers = ($request->filled('cc')) ? explode(',',$request->cc) : [];
        $bccUsers = ($request->filled('cc')) ? explode(',',$request->bcc) : [];



        $data['pdffile'] = $pdffile;
        $data['fileName'] = $slug.'-budget.pdf';

        dispatch(
           function() use ($request, $data, $ccUsers, $bccUsers){
           $mail = \Mail::to($request->recipient);
             if(array_filter($ccUsers)  &&  count($ccUsers) > 0){
              $mail->cc($ccUsers);
             }
             if(array_filter($bccUsers)  && count($bccUsers) > 0){
              $mail->bcc($bccUsers);
             }
             $mail->send(new MaitToSubcontractor($data));
          }

        )->afterResponse();

      return response()->json(
           [
            'status' => 200,
            'message' => 'Sent Successfully!'
           ]
       );

    } 

    public function totalSendMail(Request $request, $id){

       set_time_limit(0);
        $project = Project::find($id); 
         $slug = \Str::slug($project->name);
        $data = [
          'heading' => '',
          'plans' => '',
          'file' => '',
          'subject' => $request->subject,
          'content' => $request->message,
        ];

        $ccUsers = ($request->filled('cc')) ? explode(',',$request->cc) : [];
        $bccUsers = ($request->filled('cc')) ? explode(',',$request->bcc) : [];
       
        $pdffile = $this->totalDownloadPDF($id,true);
 
        $data['pdffile'] = $pdffile;
        $data['fileName'] = $slug.'-budget.pdf';

        dispatch(
           function() use ($request, $data, $ccUsers, $bccUsers){
           $mail = \Mail::to($request->recipient);
             if(array_filter($ccUsers)  &&  count($ccUsers) > 0){
              $mail->cc($ccUsers);
             }
             if(array_filter($bccUsers)  && count($bccUsers) > 0){
              $mail->bcc($bccUsers);
             }
             $mail->send(new MaitToSubcontractor($data));
          }

        )->afterResponse();

      return response()->json(
           [
            'status' => 200,
            'message' => 'Sent Successfully!'
           ]
       );

    }
}
