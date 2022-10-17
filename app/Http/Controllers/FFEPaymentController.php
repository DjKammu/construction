<?php

namespace App\Http\Controllers;

use App\Mail\MaitToSubcontractor;
use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\FFETrade;
use App\Models\Document;
use App\Models\FFEProposal;
use App\Models\FFEPayment;
use App\Models\FFEVendor;
use App\Models\FFECategory;
use App\Models\DocumentType;
use App\Models\DocumentFile;
use Gate;
use Carbon\Carbon;

use PDF;

class FFEPaymentController extends Controller
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

        $proposalsQry = $project->ffe_proposals()->IsAwarded();

        $proposals = $proposalsQry->get();

        ($request->filled('trade')) ? $proposalsQry->where('f_f_e_trade_id', $request->trade) : '';

        $proposal = $proposalsQry->first();

        $vendors = FFEVendor::orderBy('name')->get();

        $trades = $proposals->map(function($prpsl){
             return $prpsl->trade;
        });

        //if(!@$project->proposals()->exists()){
             $allTrades = FFETrade::orderBy('name')->get();
       //  }


        $totalAmount = $this->proposalTotalAmount($proposal);
        $dueAmount = $this->proposalDueTotalAmount($proposal);
     
        return view('projects.ffe.payments-create',compact('id','proposal','vendors','trades','allTrades','totalAmount','dueAmount'));
    }  



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $project, $id)
    {
          if(Gate::denies('add')) {
               return abort('401');
        } 

        
        $proposal  = FFEProposal::find($id);  
         
        if(!$proposal &&  $id == null){
            return redirect('/');
        }

        $totalDueMount =  $this->proposalDueTotalAmount($proposal);

        $data = $request->except('_token');

     
       // $type = ($request->filled('type')) ?  $request->type : Payment::VENDOR;

        if($id == 0){
             
             $request->validate([
                   'f_f_e_trade_id' => 'required|exists:f_f_e_trades,id',
                   'f_f_e_vendor_id' => 'required|exists:f_f_e_vendors,id',
                   'payment_amount' => ['required',
                        function ($attribute, $value, $fail) use ($totalDueMount){
                          if ( $value > $totalDueMount ) {
                              $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                          }
                      }
                    ]
                  // 'status' => 'required'
              ]
          );

        }else{

            $request->validate([
                   'f_f_e_trade_id' => 'required|exists:f_f_e_trades,id',
                   'f_f_e_vendor_id' => 'required|exists:f_f_e_vendors,id',
                   // 'vendor_id' => function ($attribute, $value, $fail){
                   //      if (!$value &&  request()->type  == Payment::VENDOR) {
                   //          $fail('Vendor Id is required');
                   //      }
                   //  },
                   'payment_amount' => ['required',
                      //   function ($attribute, $value, $fail) use ($totalDueMount){
                      //     if (request()->type  != FFEPayment::VENDOR && !request()->filled('vendor_id') && $value > $totalDueMount ) {
                      //         $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                      //     }
                      // }
                    ]
                  // 'status' => 'required'
              ]
          );
        }
         

        $project_id = @$proposal->project->id;
        $project_id = (!$project_id) ? $request->project_id : $project_id;

        $data['project_id']  = (int) $project_id;
        $data['ffe_proposal_id'] = ($id > 0) ? $id : null ;

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d');
        
        $data['total_amount'] = @$this->proposalTotalAmount(@$proposal);

        $project = Project::find($project_id);

        $project_slug = \Str::slug($project->name);

        $trade = FFETrade::find(@$data['f_f_e_trade_id']);

        $trade_slug = @$trade->slug;

        $vendor  = FFEVendor::find($request->f_f_e_vendor_id);
        $subcontractor_slug = @$vendor->slug;
      
        $public_path = public_path().'/';

        $folderPath = Document::INVOICES."/";

        $folderPath .= $project_slug.'/'.$trade_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        $folderPath2 = Document::LIEN_RELEASES."/";

        $folderPath2 .= $project_slug.'/'.$trade_slug;

        \File::makeDirectory($public_path.$folderPath2, $mode = 0777, true, true);

        $data['file'] = '';

        $payment = FFEPayment::create($data);
       
        $document_type = DocumentType::where('name', DocumentType::INVOICE)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$proposal->subcontractor->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['ffe_payment_id' => $payment->id,
                    'document_type_id' => $document_type->id
                     ],
                     ['name' => $name, 'slug' => $slug,
                     'ffe_payment_id'       => $payment->id,
                     'ffe_proposal_id'  => $id,
                     'document_type_id' => $document_type->id
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
                         ->firstOrCreate(['ffe_payment_id' => $payment->id,
                          'document_type_id' => $document_type->id],
                           ['name' => $name, 'slug' => $slug,
                           'ffe_payment_id'       => $payment->id,
                           'ffe_proposal_id'      => $id,
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
                         ->firstOrCreate(['ffe_payment_id' => $payment->id,
                          'document_type_id' => $document_type->id],
                           ['name' => $name, 'slug' => $slug,
                           'ffe_payment_id'       => $payment->id,
                           'ffe_proposal_id'  => $id,
                           'document_type_id' => $document_type->id
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

        

        return redirect(route('ffe.index',['project' => $project_id]).'#payments')->with('message', 'FFE Payment Created Successfully!');
    }
     
    public function proposalTotalAmount($proposal){

       $total =  (float) @$proposal->material + (float) @$proposal->labour_cost + (float) @$proposal->subcontractor_price;  

         if(!@$proposal->changeOrders)
         {
             return $total;
         }

         foreach(@$proposal->changeOrders as $k => $order){
           if($order->type == \App\Models\FFEChangeOrder::ADD ){
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
     
         $payments = FFEPayment::whereFfeProposalId(@$proposal->id)
                    // ->whereNull('f_f_e_vendor_id')
                    ->sum('payment_amount');

         $due = (float) $total - (float) $payments;

        return round($due,2);
    } 

    public function proposalDueAmount($proposal,$payment_id){

         $total =  $this->proposalTotalAmount($proposal);  

         $payments = FFEPayment::whereFfeProposalId(@$proposal->id)
         // ->whereNull('f_f_e_vendor_id')                   
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
    public function show(Request $request, $project, $id)
    {
         if(Gate::denies('edit')) {
           return abort('401');
        } 
        $payment = FFEPayment::find($id);  

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

        $vendors = FFEVendor::orderBy('name')->get(); 

        $totalAmount = $this->proposalTotalAmount($payment->proposal);
        $dueAmount = $this->proposalDueTotalAmount($payment->proposal);
       
        //dd($totalAmount);

        $proposalsQry = $project->proposals()->IsAwarded();

        $proposals = $proposalsQry->get();

        ($request->filled('trade')) ? $proposalsQry->where('trade_id', $request->trade) : '';

        $trades = $proposals->map(function($prpsl){
             return $prpsl->trade;
        });

        $allTrades = FFETrade::orderBy('name')->get();
         
         session()->flash('url', route('projects.show',['project' => $payment->project_id]).'?#payments'); 


         // dd($payment);

        return view('projects.ffe.payments-edit',compact('payment','vendors','totalAmount','dueAmount','trades','allTrades'));
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
    public function update(Request $request, $project, $id)
    {
        if(Gate::denies('update')) {
               return abort('401');
        } 

        $data = $request->except('_token');

        $payment = FFEPayment::find($id);

        $totalDueMount =  $this->proposalDueTotalAmount($payment->proposal);

        $data = $request->except('_token');


         if($payment->proposal){

               
             $request->validate([
                   'f_f_e_trade_id' => 'required|exists:f_f_e_trades,id',
                   'f_f_e_vendor_id' => 'required|exists:f_f_e_vendors,id',
                   'payment_amount' => ['required',
                        function ($attribute, $value, $fail) use ($totalDueMount){
                          if ($value > $totalDueMount ) {
                              $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                          }
                      }
                    ]
                  // 'status' => 'required'
              ]
          );


        }else{

            $request->validate([
                   'f_f_e_trade_id' => 'required|exists:f_f_e_trades,id',
                   'f_f_e_vendor_id' => 'required|exists:f_f_e_vendors,id',
                   // 'vendor_id' => function ($attribute, $value, $fail){
                   //      if (!$value &&  request()->type  == Payment::VENDOR) {
                   //          $fail('Vendor Id is required');
                   //      }
                   //  },
                   'payment_amount' => ['required',
                        function ($attribute, $value, $fail) use ($totalDueMount){
                          if ( request()->filled('ffe_proposal_id') && $value > $totalDueMount ) {
                              $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                          }
                      }
                    ]
                  // 'status' => 'required'
              ]
          );


        }

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d');


        $project = @$payment->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @$payment->trade->slug;

        $vendor  = FFEVendor::find($request->f_f_e_vendor_id);
        $subcontractor_slug  =  @$vendor->slug;
      

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
                   ->firstOrCreate(['ffe_payment_id' => $payment->id,
                          'document_type_id' => $document_type->id],
                     ['name' => $name, 'slug' => $slug,
                     'ffe_payment_id'       => $payment->id,
                     'ffe_proposal_id'      => $id,
                     'document_type_id' => $document_type->id
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
                         ->firstOrCreate(['ffe_payment_id' => $payment->id,
                          'document_type_id' => $document_type->id],
                           ['name' => $name, 'slug' => $slug,
                           'ffe_payment_id'       => $payment->id,
                           'ffe_proposal_id'      => $id,
                           'document_type_id' => $document_type->id
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
                         ->firstOrCreate(['ffe_payment_id' => $payment->id,
                          'document_type_id' => $document_type->id],
                           ['name' => $name, 'slug' => $slug,
                           'ffe_payment_id'       => $payment->id,
                           'ffe_proposal_id'      => $id,
                           'document_type_id' => $document_type->id
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

        
        return redirect(route('ffe.index',['project' => $payment->project_id]).'?#payments')->with('message', 'FFE Payment Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($project, $id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

         $payment = FFEPayment::find($id);

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
                    ->where(['ffe_payment_id' => $id])->delete();


         $payment->delete();

        return redirect()->back()->with('message', 'FFE Payment Delete Successfully!');
    }


     public function destroyFile($project, $id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

          $path = request()->path;

          $payment = FFEPayment::find($id);

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

        $data['pdffile'] = $pdffile;
        $data['fileName'] = $slug.'-budget.pdf';

        dispatch(
          function() use ($request, $data){
           \Mail::to($request->recipient)->send(new MaitToSubcontractor($data));
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
