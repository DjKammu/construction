<?php

namespace App\Http\Controllers;

use App\Models\RFISubmittalStatus;
use App\Models\SoftCostCategory;
use App\Models\SoftCostProposal;
use App\Models\SoftCostPayment;
use App\Models\SoftCostBill;
use App\Models\SoftCostTrade;
use Illuminate\Http\Request;
use App\Models\PaymentStatus;
use App\Models\Subcontractor;
use App\Models\DocumentType;
use App\Models\PropertyType;
use App\Models\ProjectType;
use App\Models\FFECategory;
use App\Models\FFEProposal;
use App\Models\FFEPayment;
use App\Models\ITBTracker;
use App\Models\FFETrade;
use App\Models\Category;
use App\Models\Document;
use App\Models\Proposal;
use App\Models\FFEBill;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Status;
use App\Models\Vendor;
use App\Models\Trade;
use App\Models\Bill;
use App\Models\User;
use Carbon\Carbon;
use Gate;


class ProjectController extends Controller
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
         if(Gate::denies('view')) {
               return abort('401');
         } 

         $projects = Project::query();

         if(request()->filled('s')){
            $searchTerm = request()->s;
            $projects->where('name', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('address', 'LIKE', "%{$searchTerm}%")
            ->orWhere('city', 'LIKE', "%{$searchTerm}%")
            ->orWhere('state', 'LIKE', "%{$searchTerm}%")
            ->orWhere('country', 'LIKE', "%{$searchTerm}%")
            ->orWhere('zip_code', 'LIKE', "%{$searchTerm}%")
            ->orWhere('notes', 'LIKE', "%{$searchTerm}%");
         }  

         if(request()->filled('p')){
            $p = request()->p;
            $projects->whereHas('project_type', function($q) use ($p){
                $q->where('slug', $p);
            });
         } 

          if(request()->filled('st')){
            $st = request()->st;
            // $projects->where('status', $st);
            $projects->whereHas('p_status', function($q) use ($st){
                $q->where('id', $st);
            });

         }
        if(request()->filled('pr')){
            $pr = request()->pr;
            $projects->where('property_type_id', $pr);
         } 

         $propertyTypes = PropertyType::orderBy('name')->get(); 
         $projectTypes = ProjectType::orderBy('name')->get(); 
         $statuses = Status::orderBy('name')->get(); 

         $orderBy = 'name';  
         $order ='ASC' ;
         
         $perPage = request()->filled('per_page') ? request()->per_page : (new Project())->perPage;

         if(request()->filled('order')){
            $orderBy = request()->filled('orderby') ? ( !in_array(request()->orderby, 
                ['start_date','name'] ) ? 'name' : request()->orderby ) : 'name';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
             : request()->order;
        }
        
         $projects = $projects->orderBy($orderBy,$order)->paginate($perPage);

         return view('projects.index',compact('projects','projectTypes','propertyTypes','statuses'));
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
        $statuses = Status::orderBy('name')->get(); 
        $propertyTypes = PropertyType::orderBy('name')->get(); 
        $projectTypes = ProjectType::orderBy('name')->get(); 
        $users = User::orderBy('name')->get(); 

        return view('projects.create',compact('projectTypes','propertyTypes','statuses','users'));
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

        $data = $request->except('_token');

        $request->validate([
              'name' => 'required|unique:projects',
              'project_type_id' => 'required|exists:project_types,id',
              'start_date'    => 'nullable|date',
              'end_date'      => 'nullable|date|after_or_equal:start_date',
              'due_date'      => 'nullable|date|after_or_equal:end_date'
        ]);

        $slug = \Str::slug($request->name);

        $data['photo'] = '';    
        // $data['status'] = Project::ACTIVE_STATUS;

        if($request->hasFile('photo')){
               $photo = $request->file('photo');
               $photoName = $slug.'-'.time() . '.' . $photo->getClientOriginalExtension();
              
               $data['photo']  = $request->file('photo')->storeAs(Document::PROJECTS, $photoName, 'public');
        }

        $property = Project::create($data);

        $project_type = $property->project_type;

        $path = public_path().'/'.Document::PROJECT.'/' . $project_type->slug.'/'.$slug;
        \File::makeDirectory($path, $mode = 0777, true, true);

        return redirect('projects')->with('message', 'Project Created Successfully!');
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
         $rfi_statuses = RFISubmittalStatus::orderBy('name')->get(); 
         $statuses = Status::orderBy('name')->get(); 
         $propertyTypes = PropertyType::orderBy('name')->get();
         $projectTypes = ProjectType::orderBy('name')->get();
         $projects = Project::orderBy('name')->get()->except($id);
         $project = Project::find($id);
         $documentTypes = DocumentType::orderBy('name')->get();
         $subcontractors = Subcontractor::orderBy('name')->get();
         $vendors = Vendor::orderBy('name')->get();
         $documents = @$project->documents();
         $trades = $project->trades()->orderBy('name')->get();
         $ffe_trades = $project->ffe_trades()->orderBy('name')->get();
         $sc_trades = $project->sc_trades()->orderBy('name')->get();
         $users = User::orderBy('name')->get();

         $payments = $project->payments();
         $bills = $project->bills();
         $rfis = $project->rfis();
         $submittals = $project->submittals();
         $logs = $project->logs();

         if(request()->filled('log_vendor')){
                $log_vendor = request()->log_vendor;
                $logs->where('vendor_id', $log_vendor);
         } 
        if(request()->filled('log_subcontractor')){
                $log_subcontractor = request()->log_subcontractor;
                $logs->where('subcontractor_id', $log_subcontractor);
         } 
        if(request()->filled('log_status')){
                $log_status = request()->log_status;
                $logs->whereHas('status', function($q) use ($log_status){
                    $q->where('id', $log_status);
                });
         } 


          if(request()->filled('payment_subcontractor')){
                $subcontractor = request()->payment_subcontractor;
                $payments->where('subcontractor_id', $subcontractor)->where('vendor_id',null);
         } 

         if(request()->filled('payment_vendor')){
                $payment_vendor = request()->payment_vendor;
                $payments->where('vendor_id', $payment_vendor);
         } 

         if(request()->filled('payment_trade')){
                $payment_trade = request()->payment_trade;
                $payments->where('trade_id', $payment_trade);
         } 

         if(request()->filled('payment_status')){
                $payment_status = request()->payment_status;
                $payments->where('status', $payment_status);
         }   

         if(request()->filled('bill_subcontractor')){
                $bill_subcontractor = request()->bill_subcontractor;
                $bills->where('subcontractor_id', $bill_subcontractor);
         } 

         if(request()->filled('bill_vendor')){
                $bill_vendor = request()->bill_vendor;
                $bills->where('vendor_id', $bill_vendor);
         } 

         if(request()->filled('bill_trade')){
                $bill_trade = request()->bill_trade;
                $bills->where('trade_id', $bill_trade);
         } 

         if(request()->filled('bill_status')){
                $bill_status = request()->bill_status;
                $bills->where('status', $bill_status);
         }

          if(request()->filled('bill_paid_status')){
                $bill_paid_status = request()->bill_paid_status;
                $bills->where('bill_status', $bill_paid_status);
         } 

         if(request()->filled('bill_assigned_to')){
                $bill_assigned_to = request()->bill_assigned_to;
                $bills->where('assigned_to', $bill_assigned_to);
         } 

         if(request()->filled('rfi_subcontractor')){
                $subcontractor = request()->rfi_subcontractor;

                $rfis->whereHas('subcontractor', function($q) use ($subcontractor){
                    $q->where('id', $subcontractor);
                });
         }

          if(request()->filled('rfi_status')){
                $status = request()->rfi_status;

                $rfis->whereHas('status', function($q) use ($status){
                    $q->where('id', $status);
                });
         } 

        if(request()->filled('start') && request()->filled('end')){
                 $start = Carbon::parse(request()->start)->format('Y-m-d'); 
                 $end = Carbon::parse(request()->end)->format('Y-m-d'); 
                 $rfis->whereRaw("date_sent >=  date('$start')")
                      ->whereRaw("date_recieved <=  date('$end')");
                
         } 

        if(request()->filled('submittal_subcontractor')){
                $subcontractor = request()->submittal_subcontractor;

                $submittals->whereHas('subcontractor', function($q) use ($subcontractor){
                    $q->where('id', $subcontractor);
                });
         }

        if(request()->filled('submittal_status')){
                $status = request()->submittal_status;

                $submittals->whereHas('status', function($q) use ($status){
                    $q->where('id', $status);
                });
         } 

        if(request()->filled('submittal_start') && request()->filled('submittal_end')){
                 $start = Carbon::parse(request()->submittal_start)->format('Y-m-d'); 
                 $end = Carbon::parse(request()->submittal_end)->format('Y-m-d'); 
                 $submittals->whereRaw("date_sent >=  date('$start')")
                      ->whereRaw("date_recieved <=  date('$end')");
                
         } 

         $orderBy = 'created_at';  
         $orderByRFI = 'created_at';  
         $orderBySubmittal = 'created_at';  
         $order ='DESC' ;
         $orderRFI ='DESC' ;
         $orderSubmittal ='DESC' ;
         $orderByLog = 'created_at';  
         $orderLog ='DESC';
                    
        if(request()->filled('order')){
            $orderBy = request()->filled('orderby') ? ( !in_array(request()->orderby, 
                ['date','invoice_number','created_at','sc'] ) ? 'created_at' : request()->orderby ) : 'created_at';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
             : request()->order;

        }

        if(request()->filled('orderRFI')){
            $orderByRFI = request()->filled('orderbyRFI') ? ( !in_array(request()->orderbyRFI, 
                ['number','date_sent','date_recieved'] ) ? 'created_at' : request()->orderbyRFI ) : 'created_at';
            
            $orderRFI = !in_array(\Str::lower(request()->orderRFI), ['desc','asc'])  ? 'ASC' 
             : request()->orderRFI;
        }

        if(request()->filled('orderSubmittal')){
            $orderBySubmittal = request()->filled('orderBySubmittal') ? ( !in_array(request()->orderBySubmittal, ['number','date_sent','date_recieved'] ) ? 'created_at' : request()->orderBySubmittal ) : 'created_at';
            
            $orderSubmittal = !in_array(\Str::lower(request()->orderSubmittal), ['desc','asc'])  ? 'ASC' 
             : request()->orderSubmittal;
        }

        if(request()->filled('orderLog')){
            $orderByLog = request()->filled('orderByLog') ? ( !in_array(request()->orderByLog, ['date','item','po_sent','date_shipped','lead_time_weeks'] ) ? 'date_shipped' : request()->orderByLog ) : 'created_at';
            $orderLog = !in_array(\Str::lower(request()->orderLog), ['desc','asc'])  ? 'ASC' 
             : request()->orderLog;
        }

         $logs     = $logs->orderBy($orderByLog, $orderLog)->get();

         if($orderBy == 'sc' ){
            $payments->join('subcontractors', 'payments.subcontractor_id', '=', 'subcontractors.id')->orderBy('subcontractors.name', $order);
             $bills->orderBy('created_at', 'ASC');
         }else{

            if($orderBy == 'invoice_number'){
               $orderBy = \DB::raw('CONVERT(invoice_number, SIGNED)');
            }
            $payments->orderBy($orderBy, $order);
            $bills->orderBy($orderBy, $order);
         }

         $payments = $payments->get();
         $bills = $bills->get();

         $rfis = $rfis->orderBy($orderByRFI, $orderRFI)->get();
         $submittals = $submittals->orderBy($orderBySubmittal, $orderSubmittal)->get();

         if(request()->filled('s')){
            $searchTerm = request()->s;
            $documents->where('name', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('slug', 'LIKE', "%{$searchTerm}%");
         }  

         if(request()->filled('document_type')){
                $document_type = request()->document_type;
                $documents->whereHas('document_type', function($q) use ($document_type){
                    $q->where('slug', $document_type);
                });
         }

         if(request()->filled('vendor')){
                $vendor = request()->vendor;
                $documents->where('vendor_id', $vendor);
         } 
        
    
          if(request()->filled('subcontractor')){
                $subcontractor = request()->subcontractor;
                $documents->where('subcontractor_id', $subcontractor);
         } 

        
         $trade = @$trades->first()->id;
        
         if(request()->filled('trade')){
                $trade = request()->trade;  
         } 
        
         $awarded = @$project->proposals()->IsAwarded()->exists();

         $proposalQuery = @$project->proposals();

         $awardedProposals = $proposalQuery->IsAwarded()->get();

         $paymentTrades = @$awardedProposals->map(function($prpsl){
                 return $prpsl->trade;
         })->unique()->sortByDesc('name');

         if(!@$project->proposals()->exists()){
             $paymentTrades = Trade::orderBy('name')->get();
         }

         $paymentSubcontractors = @$awardedProposals->map(function($prpsl){
                 return $prpsl->subcontractor;
         })->unique()->sortByDesc('name');

         
         if(request()->filled('proposal_trade')){
                $proposal_trade = request()->proposal_trade;
                $proposalsIds = @$project->proposals()->trade($proposal_trade)->pluck('id');
                $documents->whereIn('proposal_id', $proposalsIds);
         }
    
         $allProposals = @$project->proposals()->get();
         $proposals = @$project->proposals()->trade($trade)->get();
              
         $perPage = request()->filled('per_page') ? request()->per_page : (new Project())->perPage;

         $documents = $documents->has('files')->with('document_type')
                    ->paginate($perPage);
        
       

        $documents->filter(function($doc){

            $project = @$doc->project;

            $project_slug = \Str::slug($project->name);

            $document_type = @$doc->document_type->slug;

            $project_type_slug = @$project->project_type->slug;

            $folderPath = Document::PROJECT."/";

            $project_type_slug = ($project_type_slug) ? $project_type_slug : Document::ARCHIEVED;

            $folderPath .= "$project_type_slug/$project_slug/$document_type/";
            
            if($doc->proposal_id){
                 $proposal = Proposal::find($doc->proposal_id);
                 $trade_slug = @\Str::slug($proposal->trade->name);
                 $folderPath = ($doc->document_type->name == DocumentType::INVOICE) ? Document::INVOICES."/" : ( $doc->document_type->name == DocumentType::LIEN_RELEASE ?  Document::LIEN_RELEASES."/" :   Document::PROPOSALS."/");

                 if($doc->document_type->name == DocumentType::LIEN_RELEASE && $doc->payment_id){
                         $payment_id = Payment::find($doc->payment_id);
                         $trade_slug = @\Str::slug($payment_id->trade->name);
                 }

                 if($doc->payment_id){
                     $payment = Payment::find($doc->payment_id);
                     $trade_slug = @\Str::slug($payment->trade->name);
                 }


                if($doc->document_type->name == DocumentType::BILL && $doc->bill_id){
                     $bill = Bill::find($doc->bill_id);
                     $trade_slug = @\Str::slug($bill->trade->name);
                     $folderPath = Document::BILLS."/";
                     
                }

                if($doc->document_type->name == DocumentType::PURCHASE_ORDER && $doc->bill_id){ 
                     $bill = Bill::find($doc->bill_id);
                     $trade_slug = @\Str::slug($bill->trade->name);
                     $folderPath = Document::BILLS_PURCHASE_ORDERS."/";
                     
                } 

                if($doc->document_type->name == DocumentType::PURCHASE_ORDER && $doc->payment_id){
                     $payment = Payment::find($doc->payment_id);
                     $trade_slug = @\Str::slug($payment->trade->name);
                     $folderPath = Document::PROJECTS_PURCHASE_ORDERS."/";
                     
                }

                $folderPath .= "$project_slug/$trade_slug/";

            }
             else if(!$doc->proposal_id && $doc->payment_id){

                 $payment_id = Payment::find($doc->payment_id);
                 $trade_slug = @\Str::slug($payment_id->trade->name);
                 $folderPath = ($doc->document_type->name == DocumentType::INVOICE) ? Document::INVOICES."/" : ( $doc->document_type->name == DocumentType::LIEN_RELEASE ?  Document::LIEN_RELEASES."/" :   '/');
                  if($doc->document_type->name == DocumentType::PURCHASE_ORDER){
                     $folderPath = Document::PROJECTS_PURCHASE_ORDERS."/";  
                   }

                 $folderPath .= "$project_slug/$trade_slug/";
            }

              else if(!$doc->proposal_id && $doc->bill_id){

                 $bill = Bill::find($doc->bill_id);
                 $trade_slug = @\Str::slug($bill->trade->name);
                 $folderPath = ($doc->document_type->name == DocumentType::PURCHASE_ORDER) ? Document::BILLS_PURCHASE_ORDERS."/" :  Document::BILLS."/";
                 $folderPath .= "$project_slug/$trade_slug/";
            }

            else if($doc->ffe_proposal_id){
                 $proposal = FFEProposal::find($doc->ffe_proposal_id);
                 $trade_slug = @\Str::slug($proposal->trade->name);
                 $folderPath = ($doc->document_type->name == DocumentType::INVOICE) ? Document::INVOICES."/" : ( $doc->document_type->name == DocumentType::LIEN_RELEASE ?  Document::LIEN_RELEASES."/" :   Document::FFE_PROPOSALS."/");
                 if($doc->document_type->name == DocumentType::LIEN_RELEASE && $doc->ffe_payment_id){
                         $payment_id = FFEPayment::find($doc->ffe_payment_id);
                         $trade_slug = @\Str::slug($payment_id->trade->name);
                 }

                 if($doc->document_type->name == DocumentType::BILL && $doc->ffe_bill_id){
                     $bill = FFEBill::find($doc->ffe_bill_id);
                     $trade_slug = @\Str::slug($bill->trade->name);
                     $folderPath = Document::BILLS."/";
                     
                }
                 $folderPath .= "$project_slug/$trade_slug/";
            }  else if(!$doc->ffe_proposal_id && $doc->ffe_payment_id){

                 $payment_id = FFEPayment::find($doc->ffe_payment_id);
                 $trade_slug = @\Str::slug($payment_id->trade->name);
                 $folderPath = ($doc->document_type->name == DocumentType::INVOICE) ? Document::INVOICES."/" : ( $doc->document_type->name == DocumentType::LIEN_RELEASE ?  Document::LIEN_RELEASES."/" :   '/');
                  if($doc->document_type->name == DocumentType::PURCHASE_ORDER){
                     $folderPath = Document::PROJECTS_PURCHASE_ORDERS."/";  
                   }

                 $folderPath .= "$project_slug/$trade_slug/";
            }

              else if(!$doc->ffe_proposal_id && $doc->ffe_bill_id){

                 $bill = FFEBill::find($doc->ffe_bill_id);
                 $trade_slug = @\Str::slug($bill->trade->name);
                 $folderPath = ($doc->document_type->name == DocumentType::PURCHASE_ORDER) ? Document::BILLS_PURCHASE_ORDERS."/" :  Document::BILLS."/";
                 $folderPath .= "$project_slug/$trade_slug/";
            }
             else if($doc->soft_cost_proposal_id){
                 $proposal = SoftCostProposal::find($doc->soft_cost_proposal_id);
                 $trade_slug = @\Str::slug($proposal->trade->name);
                 $folderPath = ($doc->document_type->name == DocumentType::INVOICE) ? Document::INVOICES."/" : ( $doc->document_type->name == DocumentType::LIEN_RELEASE ?  Document::LIEN_RELEASES."/" :   Document::SOFT_COST_PROPOSALS."/");
                 if($doc->document_type->name == DocumentType::LIEN_RELEASE && $doc->soft_cost_payment_id){
                         $payment_id = SoftCostPayment::find($doc->soft_cost_payment_id);
                         $trade_slug = @\Str::slug($payment_id->trade->name);
                 }

                 if($doc->document_type->name == DocumentType::BILL && $doc->soft_cost_bill_id){
                     $bill = SoftCostBill::find($doc->soft_cost_bill_id);
                     $trade_slug = @\Str::slug($bill->trade->name);
                     $folderPath = Document::BILLS."/";
                     
                }
                 $folderPath .= "$project_slug/$trade_slug/";
            } 

             else if(!$doc->soft_cost_proposal_id && $doc->soft_cost_payment_id){

                 $payment_id = SoftCostPayment::find($doc->soft_cost_payment_id);
                 $trade_slug = @\Str::slug($payment_id->trade->name);
                 $folderPath = ($doc->document_type->name == DocumentType::INVOICE) ? Document::INVOICES."/" : ( $doc->document_type->name == DocumentType::LIEN_RELEASE ?  Document::LIEN_RELEASES."/" :   '/');
                  if($doc->document_type->name == DocumentType::PURCHASE_ORDER){
                     $folderPath = Document::PROJECTS_PURCHASE_ORDERS."/";  
                   }

                 $folderPath .= "$project_slug/$trade_slug/";
            }

              else if(!$doc->soft_cost_proposal_id && $doc->soft_cost_bill_id){

                 $bill = SoftCostBill::find($doc->soft_cost_bill_id);
                 $trade_slug = @\Str::slug($bill->trade->name);
                 $folderPath = ($doc->document_type->name == DocumentType::PURCHASE_ORDER) ? Document::BILLS_PURCHASE_ORDERS."/" :  Document::BILLS."/";
                 $folderPath .= "$project_slug/$trade_slug/";
            }

            else if($doc->log_id || $doc->ffe_log_id || $doc->soft_cost_log_id ){
                 if($doc->document_type->name == DocumentType::INVOICE){
                    $folderPath = Document::INVOICES."/$project_slug/";
                 }
                 if($doc->document_type->name == DocumentType::RECEIVED_SHIPMENT){
                    $folderPath = Document::RECEIVED_SHIPMENTS."/$project_slug/";
                 }
                 if($doc->document_type->name == DocumentType::PURCHASE_ORDER){
                    $folderPath = Document::PURCHASE_ORDERS."/$project_slug/";
                 }
            }

            else if($doc->document_type->name == DocumentType::RFI ){
                 $folderPath = Document::RFIS."/";
                 $folderPath .= "$project_slug/";
            }
             else if($doc->document_type->name == DocumentType::SUBMITTAL ){
                 $folderPath = Document::SUBMITTALS."/";
                 $folderPath .= "$project_slug/";
            } 
            else if($doc->document_type->name == DocumentType::PROJECT_BUDGET ){
                 $folderPath = \Storage::url(Document::PROJECTS.'/'.Document::ATTACHMENTS).'/';
            }
            else if($doc->document_type->name == DocumentType::ARCHT_REPORTS ){
                 $folderPath = Document::ARCHT_REPORTS.'/';
            }
          

            $files = $doc->files();

            $file =  ($files->count() == 1) ? $files->pluck('file')->first() : '';

            $doc->file = ($file  ? asset($folderPath.$file) : '') ;

            return $doc->file;
           
         });
       

          $proposals->filter(function($proposal){

            $project = @$proposal->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($proposal->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $folderPath = Document::PROPOSALS."/";

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


          $payments->filter(function($payment){

            $project = @$payment->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($payment->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $folderPath = Document::INVOICES."/";

            if(@!$trade_slug){
                 $vendor  = Vendor::find($payment->vendor_id);
                 $trade_slug = @$vendor->slug;
            }

            $folderPath .= "$project_slug/$trade_slug/";

            $folderPath2 = Document::LIEN_RELEASES."/";
            
            $folderPath2 .= "$project_slug/$trade_slug/";

        
            $payment->file = @($payment->file) ? asset($folderPath.$payment->file) : '' ;
            $payment->conditional_lien_release_file = @($payment->conditional_lien_release_file) ? asset($folderPath2.$payment->conditional_lien_release_file) : '' ;
            $payment->unconditional_lien_release_file = @($payment->unconditional_lien_release_file) ? asset($folderPath2.$payment->unconditional_lien_release_file) : '' ;

            $payment->remaining = (new PaymentController)->proposalDueAmount($payment->proposal,$payment->id);

            $payment->remainingMinusRetainage = (new PaymentController)->remainingMinusRetainage($payment->proposal,$payment->id);

            return $payment->file;
           
         });

          $bills->filter(function($bill){

            $project = @$bill->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($bill->trade->name);

            $project_typ5e_slug = @$project->project_type->slug;

            $folderPath = Document::BILLS."/";

            if(@!$trade_slug){
                 $vendor  = Vendor::find($bill->vendor_id);
                 $trade_slug = @$vendor->slug;
            }

            $folderPath .= "$project_slug/$trade_slug/";

            // $folderPath2 = Document::LIEN_RELEASES."/";
            
            // $folderPath2 .= "$project_slug/$trade_slug/";

        
            $bill->file = @($bill->file) ? asset($folderPath.$bill->file) : '' ;
            
            // $bill->remaining = (new PaymentController)->proposalDueAmount($bill->proposal,$bill->id);

            return $bill->file;
           
         }); 

          $rfis->filter(function($rfi){

            $project = @$rfi->project;

            $project_slug = \Str::slug($project->name);

            $folderPath = Document::RFIS."/";

            $folderPath .= "$project_slug/";
        
            $rfi->sent_file = @($rfi->sent_file) ? asset($folderPath.$rfi->sent_file) : '' ;
            $rfi->recieved_file = @($rfi->recieved_file) ? asset($folderPath.$rfi->recieved_file) : '' ;
           
         });

          $submittals->filter(function($submittal){

            $project = @$submittal->project;

            $project_slug = \Str::slug($project->name);

            $folderPath = Document::SUBMITTALS."/";

            $folderPath .= "$project_slug/";
        
            $submittal->sent_file = @($submittal->sent_file) ?
                          asset($folderPath.$submittal->sent_file) : '' ;
            $submittal->recieved_file = @($submittal->recieved_file) ? 
                          asset($folderPath.$submittal->recieved_file) : '' ;
           
         });


          $logs->filter(function($log){

            $project = @$log->project;
            $project_slug = \Str::slug($project->name);
            $folderPath = Document::RECEIVED_SHIPMENTS."/$project_slug/";
            $folderPath2 = Document::INVOICES."/$project_slug/";
            $folderPath3 = Document::PURCHASE_ORDERS."/$project_slug/";
          
            $files = $log->received_shipment_attachment;
            $files = @array_filter(explode(',',$files));

            $filesArr = [];
            if(!empty($files)){
               foreach (@$files as $key => $file) {
                   $filesArr[] = asset($folderPath.$file);
                }  
            } 

            $log->received_shipment_attachment = @($filesArr) ? @implode(',',$filesArr) : '' ;
            $log->invoice = @($log->invoice) ? asset($folderPath2.$log->invoice) : '' ;
            $log->po_sent_file = @($log->po_sent_file) ? asset($folderPath3.$log->po_sent_file) : '';
        
            return $log;
           
         });

        $ITBtrades = $trades->filter(function($tr) use ($id) {
            
                $tr->subcontractors->filter(function($sc) use ($id,$tr){
                           $itb_tracker = ITBTracker::where([
                               'project_id' => $id, 'trade_id' => $tr->id , 
                               'subcontractors_id' => $sc->id 
                               ])->first();

                            $sc->mail_sent = $itb_tracker->mail_sent ?? false;
                            $sc->bid_recieved  = $itb_tracker->bid_recieved ?? false;
                            $sc->contract_sign = $itb_tracker->contract_sign ?? false;
                            $sc->tracker_id = $itb_tracker->id ?? false;

                            return $sc;                           
                });
                return $tr;
            }); 
         
         $pTrades =  [];
         $ffe_pTrades =  [];
         
         $trade_ids = @$project->payments->whereNotNull('trade_id')
                       ->pluck('trade_id');  

         $pTrades = Trade::whereIn('id',$trade_ids)->get(); 

         $ffe_trade_ids = @$project->ffe_payments->whereNotNull('f_f_e_trade_id')
                       ->pluck('f_f_e_trade_id'); 

          $ffe_pTrades = FFETrade::whereIn('id',$ffe_trade_ids)->get();  

          $sc_trade_ids = @$project->sc_payments->whereNotNull('soft_cost_trade_id')
                       ->pluck('soft_cost_trade_id'); 

          $sc_pTrades = SoftCostTrade::whereIn('id',$sc_trade_ids)->get();   
      
         if($ffe_pTrades){
            $ffe_trades = $ffe_trades->merge($ffe_pTrades);
         }

         if($sc_pTrades){
            $sc_trades = $sc_trades->merge($ffe_pTrades);
         }

         $prTrades = $trades;

         if($pTrades){
            $trades = $trades->merge($pTrades);
         }

         $catids = @($trades->pluck('category_id'))->unique();

         $categories = $paymentCategories = Category::whereIn('id',$catids)->get(); 

         $ffe_catids = @($ffe_trades->pluck('category_id'))->unique();

         $ffe_categories = $ffePaymentCategories = FFECategory::whereIn('id',$ffe_catids)->get(); 
        
         $sc_catids = @($sc_trades->pluck('category_id'))->unique();

         $sc_categories = $scPaymentCategories = SoftCostCategory::whereIn('id',$sc_catids)->get(); 


         if($paymentCategories->count() == 0){   
              $catids = @($pTrades->pluck('category_id'))->unique();
              $paymentCategories = Category::whereIn('id',$catids)->get(); 
         }

         if($ffePaymentCategories->count() == 0){   
              $catids = @($ffe_trades->pluck('category_id'))->unique();
              $ffePaymentCategories = FFECategory::whereIn('id',$catids)->get(); 
         }

         if($scPaymentCategories->count() == 0){   
              $catids = @($sc_trades->pluck('category_id'))->unique();
              $scPaymentCategories = SoftCostCategory::whereIn('id',$catids)->get(); 
         }
         
         $subcontractorsCount = @$project->proposals()
                                  ->withCount('subcontractor')
                                 ->orderBy('subcontractor_count', 'DESC')
                                  ->pluck('subcontractor_count')->max(); 
        $paymentStatuses = PaymentStatus::orderBy('name')->get();

         return view('projects.edit',compact('projectTypes','propertyTypes','project','documentTypes','documents','subcontractors','vendors','trades','projects','trade','proposals',
            'awarded','categories','subcontractorsCount','allProposals','payments','paymentTrades',
            'paymentSubcontractors','paymentCategories','pTrades','prTrades','statuses','rfis',
            'submittals','rfi_statuses','users','bills','ffe_categories','ffePaymentCategories',
            'ffe_pTrades','logs','paymentStatuses','ITBtrades','scPaymentCategories','sc_pTrades'));
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

       $data = $request->except('_token');

       $request->validate([
              'name' => 'required|unique:projects,name,'.$id,
              'project_type_id' => 'required|exists:project_types,id',
              'start_date'    => 'nullable|date',
              'end_date'      => 'nullable|date|after_or_equal:start_date',
              'due_date'      => 'nullable|date|after_or_equal:end_date'
        ]);
     
        $slug = \Str::slug($request->name);
         
        $project = Project::find($id);
        $oldSlug = \Str::slug($project->name);

        if(!$project){
            return redirect()->back();
        }

        $data['photo'] = $project->photo;    


        if($request->hasFile('photo')){
               $photo = $request->file('photo');
               $photoName = $slug.'-'.time() . '.' . $photo->getClientOriginalExtension();
              
               $data['photo']  = $request->file('photo')->storeAs(Document::PROJECTS, $photoName, 'public');
        }
        
        $oldProject_type = ProjectType::find($project->project_type_id);
        $project_type = ProjectType::find($request->project_type_id);

        if(!$oldProject_type){

                $public_path = public_path().'/'.Document::PROJECT.'/';
                $folderPath =  $project_type->slug.'/'.$oldSlug;
                $oldFolderPath = Document::ARCHIEVED.'/'.$oldSlug; 
               \File::copyDirectory($public_path.$oldFolderPath,$public_path.$folderPath); 
               \File::deleteDirectory($public_path.$oldFolderPath);
               
               if($slug  != $oldSlug){
                  $path = public_path().'/'.Document::PROJECT.'/'.@$project_type->slug.'/';
                  @rename($path.$oldSlug, $path.$slug); 
                  $path = public_path().'/'.Document::INVOICES."/";
                  @rename($path.$oldSlug, $path.$slug); 
                  $path = public_path().'/'.Document::PROPOSALS."/";
                  @rename($path.$oldSlug, $path.$slug); 

               }

        }
        elseif((@$oldProject_type->id != $request->project_type_id) || 
            ($slug != $oldSlug)){
             
             if($slug  != $oldSlug){
                 $path = public_path().'/'.Document::PROJECT.'/'.@$oldProject_type->slug.'/';
                 @rename($path.$oldSlug, $path.$slug); 
             }


             if(@$oldProject_type->id != $request->project_type_id)
             { 
               $path = public_path().'/'.Document::PROJECT.'/';
               $projectDir  = ($slug  != $oldSlug) ? $slug : $oldSlug;
                \File::copyDirectory($path.@$oldProject_type->slug.'/'.$projectDir,
                 $path.$project_type->slug.'/'.$projectDir); 
               \File::deleteDirectory($path.@$oldProject_type->slug.'/'.$projectDir);
             }
        }

         if(($project->total_construction_sq_ft != $request->total_construction_sq_ft)
           && $budget_lines = $project->budget_lines()){
            $budget_lines->UpdateSqFt();
         }

         $project->update($data);

        return redirect()->back()->with('message', 'Project Updated Successfully!');
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

         $project = Project::find($id);
         $project_slug = \Str::slug($project->name);
         $project_type = @$project->project_type;

         $project_type_slug = @$project_type->slug;

         $public_path = public_path().'/';

         $folderPath = Document::PROJECT."/";

         $project_type_slug = ($project_type_slug) ? $project_type_slug : Document::ARCHIEVED; 

         $folderPath .= "$project_type_slug/$project_slug";

         $path = $public_path.'/'.$folderPath;
          
         $aPath = public_path().'/'.Document::PROJECT.'/'.Document::ARCHIEVED.'/'.Document::PROJECTS; 
         
         @\File::makeDirectory($aPath, $mode = 0777, true, true);

         @\File::copyDirectory($path, $aPath.'/'.$project_slug);

         @\File::deleteDirectory($path);

         $project->delete();

        return redirect()->back()->with('message', 'Project Delete Successfully!');
    }

    
    public function getAttachment(Request $request, $id){

     $project = Project::find($id);
     $attachment = $project->attachment;
     $attachment_name = @$project->attachment_name;

     $fileInfo = pathinfo($attachment);
     $extension = @$fileInfo['extension'];

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
