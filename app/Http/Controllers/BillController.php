<?php

namespace App\Http\Controllers;

use App\Mail\MaitToSubcontractor;
use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Trade;
use App\Models\Document;
use App\Models\Proposal;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\DocumentType;
use App\Models\DocumentFile;
use App\Models\Subcontractor;
use App\Models\User;
use Gate;
use Carbon\Carbon;

use PDF;

class BillController extends Controller
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
          $users = User::orderBy('name')->get();

        return view('projects.includes.bills-create',compact('id','proposal','vendors','trades','allTrades','totalAmount','dueAmount','users'));
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
                      //   function ($attribute, $value, $fail) use ($totalDueMount){
                      //     if (!request()->filled('vendor_id') && $value > $totalDueMount ) {
                      //         $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                      //     }
                      // }
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
                      //   function ($attribute, $value, $fail) use ($totalDueMount){
                      //     if (request()->type  != Payment::VENDOR && !request()->filled('vendor_id') && $value > $totalDueMount ) {
                      //         $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                      //     }
                      // }
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

        $folderPath = Document::BILLS."/";

        $folderPath .= $project_slug.'/'.$trade_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        // $folderPath2 = Document::LIEN_RELEASES."/";

        // $folderPath2 .= $project_slug.'/'.$trade_slug;

        // \File::makeDirectory($public_path.$folderPath2, $mode = 0777, true, true);

        $data['file'] = ' ';

        $bill = Bill::create($data);
       
        $document_type = DocumentType::where('name', DocumentType::BILL)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$proposal->subcontractor->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['bill_id' => $bill->id,
                    'document_type_id'        => $document_type->id
                     ],
                     ['name' => $name, 'slug' => $slug,
                     'bill_id'       => $bill->id,
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

            $bill->update(['file' => $fileName]);

            $document->files()->create($fileArr);
        }

        if($request->bill_status == 1){
           $bill_status =  Bill::PAID_BILL_STATUS;
           $this->updateBillStatus($bill,$bill_status);
        }

        
        return redirect(route('projects.show',['project' => $project_id]).'#bills')->with('message', 'Bill Created Successfully!');
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
        $bill = Bill::find($id);  
        $subcontractor = @$bill->subcontractor;

        $project = @$bill->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @\Str::slug($bill->trade->name);

        $project_type_slug = @$project->project_type->slug;

        $folderPath = Document::BILLS."/";

        $folderPath .= "$project_slug/$trade_slug/";

        // $folderPath2 = Document::LIEN_RELEASES."/";

        // $folderPath2 .= "$project_slug/$trade_slug/";

        
        $bill->file = @($bill->file) ? $folderPath.$bill->file : '';

        // $payment->unconditional_lien_release_file = @($payment->unconditional_lien_release_file) ? $folderPath2.$payment->unconditional_lien_release_file : '';
        // $payment->conditional_lien_release_file = @($payment->conditional_lien_release_file) ? $folderPath2.$payment->conditional_lien_release_file : '';

        $bill->date = @($bill->date) ? Carbon::parse($bill->date)->format('m-d-Y') : '' ;

        $vendors = Vendor::orderBy('name')->get(); 

        $totalAmount = $this->proposalTotalAmount($bill->proposal);
        $dueAmount = $this->proposalDueTotalAmount($bill->proposal);
       
        //dd($totalAmount);

        $proposalsQry = $project->proposals()->IsAwarded();

        $proposals = $proposalsQry->get();

        ($request->filled('trade')) ? $proposalsQry->where('trade_id', $request->trade) : '';

        $trades = $proposals->map(function($prpsl){
             return $prpsl->trade;
        });

        $allTrades = Trade::orderBy('name')->get();
        $users = User::orderBy('name')->get();
         
         session()->flash('url', route('projects.show',['project' => $bill->project_id]).'?#bills'); 


        return view('projects.includes.bills-edit',compact('subcontractor','bill','vendors','totalAmount','dueAmount','trades','allTrades','users'));
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

        $bill = Bill::find($id);

        $totalDueMount =  $this->proposalDueTotalAmount($bill->proposal);
     
        $data = $request->except('_token');

         $type = ($request->filled('type')) ?  $request->type : Payment::VENDOR;

         if($bill->proposal){

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
                    //   function ($attribute, $value, $fail) use ($totalDueMount,$payment){
                    //     if (request()->type  != Payment::VENDOR &&  $value > ((float) $totalDueMount + $payment->payment_amount) ) {
                    //         $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                    //     }
                    // }
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
                      //   function ($attribute, $value, $fail) use ($totalDueMount){
                      //     if ( !request()->filled('vendor_id') && $value > $totalDueMount ) {
                      //         $fail('Error! The payment amount must be less than or equal '.$totalDueMount.'.');
                      //     }
                      // }
                    ]
                  // 'status' => 'required'
              ]
          );  

          $data['trade_id'] = $data[$type.'_trade_id'];

        }

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d');


        $project = @$bill->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @$bill->trade->slug;

        $subcontractor_slug = @$bill->subcontractor->slug;

         if(@!$bill->proposal){
             $vendor  = Vendor::find($request->vendor_id);
             $subcontractor_slug  =  @$vendor->slug;
        }

        $public_path = public_path().'/';

        $folderPath = Document::BILLS."/";

        $folderPath .= $project_slug.'/'.$trade_slug;
        
        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        // $folderPath2 = Document::LIEN_RELEASES."/";

        // $folderPath2 .= $project_slug.'/'.$trade_slug;
        
        // \File::makeDirectory($public_path.$folderPath2, $mode = 0777, true, true);
        
        $document_type = DocumentType::where('name', DocumentType::BILL)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$bill->subcontractor->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['bill_id' => $bill->id,
                          'document_type_id' => $document_type->id],
                     ['name' => $name, 'slug' => $slug,
                     'bill_id'          => $bill->id,
                     'proposal_id'      => $id,
                     'document_type_id' => $document_type->id,
                     'subcontractor_id' => @$proposal->subcontractor->id
                     ]
                 );


        if($request->hasFile('file')){
              @unlink($folderPath.'/'.$bill->file);
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


        $bill->update($data);

        
        return redirect(route('projects.show',['project' => $bill->project_id]).'?#bills')->with('message', 'Bill Updated Successfully!');
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

         $bill = Bill::find($id);

         $project = @$bill->project;

         $project_slug = \Str::slug($project->name);

         $trade_slug = @\Str::slug($bill->trade->name);

         $public_path = public_path().'/';

         $folderPath = Document::BILLS."/";

         $folderPath .= "$project_slug/$trade_slug/";

         // $folderPath2 = Document::LIEN_RELEASES."/";

         // $folderPath2 .= "$project_slug/$trade_slug/";

         $path = @public_path().'/'.$folderPath;
         // $path2 = @public_path().'/'.$folderPath2;

         $file = @$payment->file;
         // $unconditional_lien_release_file = @$payment->unconditional_lien_release_file;
         // $conditional_lien_release_file = @$payment->conditional_lien_release_file;
         
         $aPath = public_path().'/'. Document::BILLS."/".Document::ARCHIEVED; 
         \File::makeDirectory($aPath, $mode = 0777, true, true);
         // $aPath2 = public_path().'/'. Document::LIEN_RELEASES."/".Document::ARCHIEVED; 
         // \File::makeDirectory($aPath2, $mode = 0777, true, true);

        @\File::copy($path.$file, $aPath.'/'.$file);
        // @\File::copy($path2.$conditional_lien_release_file, $aPath2.'/'.$conditional_lien_release_file);
        // @\File::copy($path2.$unconditional_lien_release_file, $aPath2.'/'
          // .$unconditional_lien_release_file);
        @unlink($path.$file);
        // @unlink($path2.$conditional_lien_release_file);
        // @unlink($path2.$unconditional_lien_release_file);

         $project->documents()
                    ->where(['bill_id' => $id])->delete();

          if($bill->bill_status == Bill::PAID_BILL_STATUS){
             $this->updateBillStatus($bill,Bill::UNPAID_BILL_STATUS,true);
          }

         $bill->delete();

        return redirect()->back()->with('message', 'Bill Delete Successfully!');
    }


     public function destroyFile($id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

          $path = request()->path;

          $bill = Bill::find($id);

          $file = @end(explode('/', $path));

          $publicPath = public_path().'/';

          $folder = Document::BILLS;
          // if (str_contains($path, Document::LIEN_RELEASES)) { 
          //    $folder = Document::LIEN_RELEASES;
          // }

          $aPath = $publicPath.$folder."/".Document::ARCHIEVED;

          @\File::makeDirectory($aPath, $mode = 0777, true, true);

           @\File::copy($publicPath.$path, $aPath.'/'.$file);

          $docFile  = DocumentFile::whereFile($file)->first();
          (@$docFile) ?  @$docFile->delete() : ''; 

          $coulumn = 'file';

          // $coulumn = ( $file == @$payment->conditional_lien_release_file ) ? 'conditional_lien_release_file' : ( $file == @$payment->unconditional_lien_release_file ? 'unconditional_lien_release_file' : $coulumn);  
          

          $bill->update([$coulumn => '']);

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

    public function billStatus(Request $request, $id){
      $bill = Bill::find($id);
      $bill_status = $request->bill_status;
      $bill_status = ($bill_status == 'true') ? Bill::PAID_BILL_STATUS : Bill::UNPAID_BILL_STATUS;
      $this->updateBillStatus($bill,$bill_status);
      return redirect()->back()->with('message', 'Status Updated Successfully!');   
    }

    public function updateBillStatus($bill, $bill_status, $force = false){

      if($bill->bill_status == $bill_status && !$force){
        return;
      }

      if($bill_status == Bill::PAID_BILL_STATUS){
          $data = $bill->toArray();
          $data['bill_id'] = $data['id'];
          unset($data['id']);
          unset($data['payment_id']);
          unset($data['bill_status']);
          unset($data['created_at']);
          unset($data['updated_at']);

          $payment  = Payment::create($data);

          if($bill->file){

            $project = @$bill->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($bill->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $publicPath = public_path().'/';

            $folderPath = Document::BILLS."/";

            $folderPath .= "$project_slug/$trade_slug/";

            $invoicePath = Document::INVOICES."/$project_slug/$trade_slug/";
            
            @\File::copy($publicPath.$folderPath.$bill->file, $publicPath.$invoicePath.$bill->file);
             
            $document_type = DocumentType::where('name', DocumentType::INVOICE)
                         ->first();

            $name = @$project->name.' '.@$document_type->name; 

            $slug = @\Str::slug($name);                

            $document = $project->documents()
               ->firstOrCreate(['payment_id' => $payment->id,
                'document_type_id' => $document_type->id
                 ],
                 ['name' => $name, 'slug' => $slug,
                 'payment_id'       => $payment->id,
                 'proposal_id'      => @$bill->proposal_id,
                 'document_type_id' => $document_type->id,
                 'subcontractor_id' => @$bill->subcontractor_id
                 ]
             );

            $date  = date('d');
            $month = date('m');
            $year  = date('Y');

            $fileArr = ['file' => $bill->file,
                        'name' => $name,
                        'date' => $date,'month' => $month,
                        'year' => $year
                        ];

            $document->files()->create($fileArr);

          }

          $bill->update(['bill_status' => $bill_status]);
          
          return true;
      }
      else if ($bill_status == Bill::UNPAID_BILL_STATUS){

           Payment::where('bill_id',$bill->id)->delete();

          if($bill->file){

            $project = @$bill->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($bill->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $publicPath = public_path().'/';

            $folderPath = Document::BILLS."/";

            $folderPath .= "$project_slug/$trade_slug/";

            $invoicePath = Document::INVOICES."/$project_slug/$trade_slug/";
            
            // @\File::copy($publicPath.$folderPath.$bill->file, $publicPath.$invoicePath.$bill->file);

            // @unlink($publicPath.$folderPath.$bill->file);
            @unlink($publicPath.$invoicePath.$bill->file);

            $docFile  = DocumentFile::whereFile($bill->file)->firstOrFail();
          
            @$docFile->delete();  

          }
          
          $bill->update(
           ['bill_status' => $bill_status]
          );
          

          return true;



      }

      return;

    }


}
