<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentStatus;
use App\Models\Subcontractor;
use App\Models\FFEITBTracker;
use App\Models\DocumentType;
use App\Models\PropertyType;
use App\Models\SoftCostProposal;
use App\Models\SoftCostCategory;
use App\Models\ProjectType;
use App\Models\SoftCostVendor;
use App\Models\SoftCostTrade;
use App\Models\Document;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Status;
use Carbon\Carbon;
use Gate;


class SoftCostController extends Controller
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
    public function index($id)
    {
         if(Gate::denies('view')) {
               return abort('401');
         } 

         // $statuses = Status::orderBy('name')->get(); 
         // $propertyTypes = PropertyType::orderBy('name')->get();
         // $projectTypes = ProjectType::orderBy('name')->get();


         $projects = Project::has('sc_trades')->orderBy('name')
                     ->get()->except($id);
                     
         $project = Project::find($id);
         // $documentTypes = DocumentType::orderBy('name')->get();

         $vendors = SoftCostVendor::orderBy('name')->get();
         // $documents = $project->documents();
         $trades = $project->sc_trades()->orderBy('name')->get();

         $payments = $project->sc_payments();
         // $rfis = $project->rfis();
         // $submittals = $project->submittals();
         // $logs = $project->ffe_logs();
         // $bills = $project->ffe_bills();


         if(request()->filled('payment_vendor')){
                $payment_vendor = request()->payment_vendor;
                $payments->where('soft_cost_vendor_id', $payment_vendor);
         }

        //   if(request()->filled('non_contract')){
        //         $non_contract = request()->non_contract;
        //         $payments->where('non_contract', $non_contract);
        //  } 

         if(request()->filled('payment_trade')){
                $payment_trade = request()->payment_trade;
                $payments->where('soft_cost_trade_id', $payment_trade);
         } 

         if(request()->filled('payment_status')){
                $payment_status = request()->payment_status;
                $payments->where('status', $payment_status);
         } 

        //  if(request()->filled('log_vendor')){
        //         $log_vendor = request()->log_vendor;
        //         $logs->where('ffe_vendor_id', $log_vendor);
        //  } 
        // if(request()->filled('log_status')){
        //         $log_status = request()->log_status;
        //         $logs->whereHas('status', function($q) use ($log_status){
        //             $q->where('id', $log_status);
        //         });
        //  } 
         


         // if(request()->filled('bill_vendor')){
         //        $bill_vendor = request()->bill_vendor;
         //        $bills->where('ffe_vendor_id', $bill_vendor);
         // } 

         // if(request()->filled('bill_trade')){
         //        $bill_trade = request()->bill_trade;
         //        $bills->where('ffe_trade_id', $bill_trade);
         // } 

         // if(request()->filled('bill_status')){
         //        $bill_status = request()->bill_status;
         //        $bills->where('status', $bill_status);
         // }

         //  if(request()->filled('bill_paid_status')){
         //        $bill_paid_status = request()->bill_paid_status;
         //        $bills->where('bill_status', $bill_paid_status);
         // } 


         $orderBy = 'created_at';  
         $order ='DESC' ;

         $orderByLog = 'created_at';  
         $orderLog ='DESC' ;
                    
        if(request()->filled('order')){
            $orderBy = request()->filled('orderby') ? ( !in_array(request()->orderby, 
                ['date','invoice_number','created_at'] ) ? 'created_at' : request()->orderby ) : 'created_at';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
             : request()->order;
        }
       
       // if(request()->filled('orderLog')){
       //      $orderByLog = request()->filled('orderByLog') ? ( !in_array(request()->orderByLog, ['date','item','po_sent','date_shipped','lead_time_weeks'] ) ? 'date_shipped' : request()->orderByLog ) : 'created_at';
       //      $orderLog = !in_array(\Str::lower(request()->orderLog), ['desc','asc'])  ? 'ASC' 
       //       : request()->orderLog;
       //  }

         // $bills    = $bills->orderBy($orderBy, $order)->get();
          $payments = $payments->orderBy($orderBy, $order)->get();
         // $logs     = $logs->orderBy($orderByLog, $orderLog)->get();

         // if(request()->filled('s')){
         //    $searchTerm = request()->s;
         //    $documents->where('name', 'LIKE', "%{$searchTerm}%") 
         //    ->orWhere('slug', 'LIKE', "%{$searchTerm}%");
         // }  

         // if(request()->filled('document_type')){
         //        $document_type = request()->document_type;
         //        $documents->whereHas('document_type', function($q) use ($document_type){
         //            $q->where('slug', $document_type);
         //        });
         // }

         // if(request()->filled('vendor')){
         //        $vendor = request()->vendor;
         //        $documents->where('vendor_id', $vendor);
         // } 
        
    
         //  if(request()->filled('subcontractor')){
         //        $subcontractor = request()->subcontractor;
         //        $documents->where('subcontractor_id', $subcontractor);
         // } 

        
         $trade = @$trades->first()->id;
        
         if(request()->filled('trade')){
                $trade = request()->trade;  
         } 
        
         $awarded = @$project->sc_proposals()->IsAwarded()->exists();

         $proposalQuery = @$project->sc_proposals();

         $awardedProposals = $proposalQuery->IsAwarded()->get();

         $paymentTrades = @$awardedProposals->map(function($prpsl){
                 return $prpsl->trade;
         })->unique()->sortByDesc('name');

         // if(!@$project->sc_proposals()->exists()){
         //     $paymentTrades = SoftCostTrade::orderBy('name')->get();
         // }

         // $paymentSubcontractors = @$awardedProposals->map(function($prpsl){
         //         return $prpsl->subcontractor;
         // })->unique()->sortByDesc('name');
         
         // if(request()->filled('proposal_trade')){
         //        $proposal_trade = request()->proposal_trade;
         //        $proposalsIds = @$project->proposals()->trade($proposal_trade)->pluck('id');
         //        $documents->whereIn('proposal_id', $proposalsIds);
         // }
    
         $allProposals = @$project->sc_proposals()->get();

         $proposals = @$project->sc_proposals()->trade($trade)->get();
      
         $perPage = request()->filled('per_page') ? request()->per_page : (new Project())->perPage;


          $proposals->filter(function($proposal){

            $project = @$proposal->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($proposal->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $folderPath = Document::SOFT_COST_PROPOSALS."/";

            $folderPath .= "$project_slug/$trade_slug/";
            
            $files = $proposal->files;

            $files = @array_filter(explode(',',$files));

            $filesArr = [];
            
            if(!empty($files)){
               foreach (@$files as $key => $file) {
                   $filesArr[] = asset($folderPath.$file);
                }  
            } 

            $proposal->files = @($filesArr) ? @implode(',',$filesArr) : '' ;

            return $proposal->files;
           
         });


         //  $logs->filter(function($log){

         //    $project = @$log->project;

         //    $project_slug = \Str::slug($project->name);

         //    $folderPath = Document::RECEIVED_SHIPMENTS."/$project_slug/";
         //    $folderPath2 = Document::INVOICES."/$project_slug/";
         //    $folderPath3 = Document::PURCHASE_ORDERS."/$project_slug/";
          
         //    $files = $log->received_shipment_attachment;

         //    $files = @array_filter(explode(',',$files));

         //    $filesArr = [];
            
         //    if(!empty($files)){
         //       foreach (@$files as $key => $file) {
         //           $filesArr[] = asset($folderPath.$file);
         //        }  
         //    } 

         //    $log->received_shipment_attachment = @($filesArr) ? @implode(',',$filesArr) : '' ;
         //    $log->invoice = @($log->invoice) ? asset($folderPath2.$log->invoice) : '' ;
         //    $log->po_sent_file = @($log->po_sent_file) ? asset($folderPath3.$log->po_sent_file) : '';
         
         //    return $log;
           
         // });
        

          $payments->filter(function($payment){

            $project = @$payment->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($payment->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $folderPath = Document::INVOICES."/";

            if(@!$trade_slug){
                 $vendor  = SoftCostVendor::find($payment->f_f_e_vendor_id);
                 $trade_slug = @$vendor->slug;
            }

            $folderPath .= "$project_slug/$trade_slug/";

            $folderPath2 = Document::LIEN_RELEASES."/";
            
            $folderPath2 .= "$project_slug/$trade_slug/";

        
            $payment->file = @($payment->file) ? asset($folderPath.$payment->file) : '' ;
            $payment->conditional_lien_release_file = @($payment->conditional_lien_release_file) ? asset($folderPath2.$payment->conditional_lien_release_file) : '' ;
            $payment->unconditional_lien_release_file = @($payment->unconditional_lien_release_file) ? asset($folderPath2.$payment->unconditional_lien_release_file) : '' ;

            $payment->remaining = (new SoftCostPaymentController)->proposalDueAmount($payment->proposal,$payment->id);

            return $payment->file;
           
         });  

        // $bills->filter(function($bill){

        //     $project = @$bill->project;

        //     $project_slug = \Str::slug($project->name);

        //     $trade_slug = @\Str::slug($bill->trade->name);

        //     $folderPath = Document::BILLS."/";

        //     $folderPath .= "$project_slug/$trade_slug/";

        //     $bill->file = @($bill->file) ? asset($folderPath.$bill->file) : '' ;
          
        //     return $bill->file;
           
        //  }); 
          
          $ITBtrades = [];
          
        //  $ITBtrades = $trades->filter(function($tr) use ($id) {

        //     $tr->sc_vendors->filter(function($v) use ($id,$tr){

        //                $itb_tracker =FFEITBTracker::where([
        //                    'project_id' => $id, 
        //                    'ffe_trade_id' => $tr->id, 
        //                    'ffe_vendor_id' => $v->id 
        //                    ])->first();

        //                 $v->mail_sent = $itb_tracker->mail_sent ?? false;
        //                 $v->bid_recieved  = $itb_tracker->bid_recieved ?? false;
        //                 $v->contract_sign = $itb_tracker->contract_sign ?? false;
        //                 $v->tracker_id = $itb_tracker->id ?? false;

        //                 return $v;                           
        //     });
        //     return $tr;
        // }); 

        
          $catids = @($trades->pluck('category_id'))->unique();

          $categories = $paymentCategories = SoftCostCategory::whereIn('id',$catids)->get(); 

         $pTrades =  [];
         
         $trade_ids = @$project->sc_payments->whereNotNull('soft_cost_trade_id')
                       ->pluck('soft_cost_trade_id'); 


         $pTrades = SoftCosttrade::whereIn('id',$trade_ids)->get();   
      
          $prTrades = $trades;
   
         if($pTrades){
            $trades = $trades->merge($pTrades);
         }

         if($paymentCategories->count() == 0){   
              $catids = @($pTrades->pluck('category_id'))->unique();
              $paymentCategories = SoftCostCategory::whereIn('id',$catids)->get(); 
         }
        
         $subcontractorsCount = @$project->proposals()
                                  ->withCount('subcontractor')
                                 ->orderBy('subcontractor_count', 'DESC')
                                  ->pluck('subcontractor_count')->max();                      
         //  $paymentStatuses = PaymentStatus::orderBy('name')->get();    

        
         return view('projects.soft_cost.index',compact('project','trades','projects','trade','proposals','awarded','prTrades','allProposals','categories','subcontractorsCount','vendors','paymentTrades','payments','paymentCategories','pTrades'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Gate::denies('add')) {
               return abort('401');
         } 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
          if(Gate::denies('add')) {
               return abort('401');
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
          if(Gate::denies('edit')) {
               return abort('401');
          } 
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
     * Update the specified resource in storage.
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
    }

    
    public function getAttachment(Request $request, $id){

     $project = Project::find($id);
     $attachment = $project->attachment;
     $attachment_name = @$project->attachment_name;

     $fileInfo = pathinfo($attachment);
     $extension = $fileInfo['extension'];

     if(in_array(\Str::lower($extension),['doc','docx','docm','dot',
    'dotm','dotx'])){
         $extension = 'word'; 
     }
    else if(in_array(\Str::lower($extension),['csv','dbf','dif','xla',
        'xls','xlsb','xlsm','xlsx','xlt','xltm','xltx'])){
         $extension = 'excel'; 
    }

    return response()->json(
           [
            'status' => 200,
            'message' => true,
            'attachment_name' => $attachment_name,
            'URL'     => url(\Storage::url($attachment)),
            'extension'  => $extension
           ]
        );

    } 

    public function uploadAttachment(Request $request, $id){

        $project = Project::find($id);

        $document_type = DocumentType::where('name', DocumentType::PROJECT_BUDGET)
                         ->first();

        $name = @$project->name.' '.@$request->name.' Project Budget';  

        $slug = @\Str::slug($name);

        $attachment_name = $request->attachment_name;                

        $document = $project->documents()
                   ->firstOrCreate(['project_id' => $project->id,
                    'document_type_id' => $document_type->id
                     ],
                     ['name' => $attachment_name, 'slug' => $slug,
                     'project_id'       => $project->id,
                     'document_type_id' => $document_type->id
                     ]
        );

        if($request->hasFile('attachment')){
               $attachment = $request->file('attachment');
               $attachmentName = @\Str::slug($attachment_name) .'.'.$attachment->getClientOriginalExtension();
               $path = Document::PROJECTS.'/'.Document::ATTACHMENTS;
               $data['attachment']  = $request->file('attachment')->storeAs($path, $attachmentName,
                'public');
               $data['attachment_name']  = $attachment_name;

             $date  = date('d');
             $month = date('m');
             $year  = date('Y');

             $fileName = $attachmentName;

             $fileArr = ['file' => $fileName,
                      'name' => $attachment_name,
                      'date' => $date,'month' => $month,
                      'year' => $year
                      ];

               @unlink('storage/'.$project->attachment);
               $document->files()->delete();
               $document->files()->create($fileArr);
        }

        $project->update($data);

        return response()->json(
           [
            'status' => 200,
            'message' => 'Attachment Uploaded Successfully!'
           ]
        );
    }

}
