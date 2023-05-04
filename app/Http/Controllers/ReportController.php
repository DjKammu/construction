<?php
namespace App\Http\Controllers;

use App\Mail\MaitToSubcontractor;
use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\Document;
use App\Models\ProjectType;
use App\Models\PropertyType;
use App\Models\PropertyGroup;
use App\Models\Subcontractor;
use App\Models\Proposal;
use App\Models\Category;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\Status;
use App\Models\Trade;
use App\Models\User;
use App\Models\Bill;
use Gate;
use PDF;


class ReportController extends Controller
{
        CONST AWARDED   = 'awarded';
        CONST PENDING   = 'pending';

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
    public function index(Request $request)
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
          
          $categories = $trades = $project = $payments = $project_subcontractors =
           $project_vendors = [];
         
        
        if(request()->filled('st')){
            $st = request()->st;
            $projects->where('status', $st);
         } 

         if(request()->filled('p') &&  request()->t == 'project-by-status'){
            $p = request()->p;
            $projects->where('projects.id', $p);
         }  

         if(request()->filled('pg') &&  request()->t == 'project-by-status'){
            $pg = request()->pg;
            @$propertyIds = PropertyType::where('property_group_id', $pg)->pluck('id');
            $projects->whereIn('property_type_id', $propertyIds);
         } 
         
         if(request()->filled('u') &&  request()->t == 'project-by-status'){
            $u = request()->u;
            $projects->where('user_id', $u);
         } 

        if(request()->filled('pt')){
            $pt = request()->pt;
            $projects->whereHas('project_type', function($q) use ($pt){
                $q->where('slug', $pt);
            });
         } 
         
         if(request()->filled('pr')){
            $pr = request()->pr;
            $projects->where('property_type_id', $pr);
         } 
         $vendor = $subcontractor = null;
          
        if(request()->filled('p')){
            $p = request()->p;
            $sc = request()->filled('sc') ? request()->sc : null;
            $v = request()->filled('v') ? request()->v : null;
            @extract($this->getreportDetails($p,$sc, $v));

            $proposalsQry = @$project->proposals()->IsAwarded();

            $proposals = @$proposalsQry->get();

            $project_subcontractors = @$proposals->map(function($prpsl){
                     return $prpsl->subcontractor;
            })->unique()->sortBy('name');

            // $project_trades = $proposals->map(function($prpsl){
            //      return $prpsl->trade;
            // });

            
            $vendorIds = $project->payments()->whereNotNull('vendor_id')
                             ->pluck('vendor_id')->unique();
           
            $project_vendors = Vendor::whereIn('id',$vendorIds) ->orderBy('name')->get();

            $vendor = Vendor::find($v);
            $subcontractor = Subcontractor::find($sc);

        }

        if(request()->filled('order')){
            $orderBy = request()->filled('orderby') ? (!in_array(request()->orderby, 
            ['name','project_type','property']) ? 'created_at' : request()->orderby ) : 'created_at';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
             : request()->order;

             if($orderBy == 'property'){
                $projects->join('property_types', 'projects.property_type_id', '=', 'property_types.id')->orderBy('property_types.name', $order);
             }
             elseif($orderBy == 'project_type'){
                $projects->join('project_types', 'projects.project_type_id', '=', 'project_types.id')->orderBy('project_types.name', $order);
             }
             else{
                $projects->orderBy($orderBy, $order);
             } 
        }
        

       $bills = Bill::query();

        if(request()->t == 'unpaid-bills'){
            
            if(request()->filled('at')){
                $at = request()->at;
                $bills->where('assigned_to', $at);
             } 

             if(request()->filled('ps')){
                $ps = request()->ps;
                $bills->where('bill_status', $ps);
             }  

            if(request()->filled('pt') || request()->filled('pr')){
                $pt = request()->pt;
                @$projects->whereHas('project_type', function($q) use ($pt){
                    $q->where('slug', $pt);
                });

                if(request()->filled('pr')){
                    $pr = request()->pr;
                    $projects->where('property_type_id', $pr);
                 } 

                 $project_ids = @$projects->pluck('id')->toArray();
                 $bills->whereIn('project_id',$project_ids);
             } 
             
            if(request()->filled('p')){
                    $p = request()->p;
                    $bills->where('project_id', $p);
            }  
            
            if(request()->filled('order')){
                $orderBy = request()->filled('orderby') ? ( !in_array(request()->orderby, 
                    ['project'] ) ? 'created_at' : request()->orderby ) : 'created_at';
                
                $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
                 : request()->order;

                 if($orderBy == 'project'){
                    $bills->join('projects', 'bills.project_id', '=', 'projects.id')->orderBy('projects.name', $order);
                 }
                 else{
                    $bills->orderBy($orderBy, $order);
                 } 
            }
        }

         $projects = $projects->get();
         $bills = @$bills->get();

         @$bills->filter(function($bill){

            $project = @$bill->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($bill->trade->name);

            $project_type_slug = @$project->project_type->slug;

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


         $projectTypes = ProjectType::orderBy('name')->get(); 
         $propertyTypes = PropertyType::orderBy('name')->get(); 
         $statuses = Status::orderBy('name')->get();
         $users = User::orderBy('name')->get();
         $propertyGroups = PropertyGroup::orderBy('name')->get();

         return view('reports.index',compact('projects','bills','projectTypes','propertyTypes','categories','trades','project','project_subcontractors','project_vendors','payments','statuses','users','propertyGroups','subcontractor','vendor'));
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
   
    public function getreportDetails($id, $sc = null, $v= null){

        $project = Project::find($id); 
        $trades = @$project->trades()->get(); 
        $paymentTrades = [];
      
        $trade_ids = @$project->payments->whereNotNull('trade_id')
                       ->pluck('trade_id');
        $paymentTrades = Trade::whereIn('id',$trade_ids)->get();             
       
        if($paymentTrades){
            $trades = $trades->merge($paymentTrades);
        }
        $catids = @($trades->pluck('category_id'))->unique();
        $categories = Category::whereIn('id',$catids)->get();

        $payments = [];

        if($sc || $v){
            $payments = $project->payments();
            if($sc){
              $payments->where('subcontractor_id', $sc)
              ->where('vendor_id',null);  
            }else{
              $payments->where('vendor_id', $v);
            }

            $payments = $payments->get();

            $payments->filter(function($payment){

                $payment->remaining = (new PaymentController)->proposalDueAmount($payment->proposal,$payment->id);

                return $payment;
               
             });

         }
     
        return compact('project','trades','categories','payments');

    }


    public function getreportByStatus(){
          
          $projects = Project::query();

    
        if(request()->filled('st')){
            $st = request()->st;
            $projects->where('status', $st);
         } 

        if(request()->filled('pt')){
            $pt = request()->pt;
            $projects->whereHas('project_type', function($q) use ($pt){
                $q->where('slug', $pt);
            });
         } 
         
         if(request()->filled('pr')){
            $pr = request()->pr;
            $projects->where('property_type_id', $pr);
         } 

        if(request()->filled('p') &&  request()->t == 'project-by-status'){
            $p = request()->p;
            $projects->where('id', $p);
         }  

         if(request()->filled('pg') &&  request()->t == 'project-by-status'){
            $pg = request()->pg;
            $projects->where('property_group_id', $pg);
         } 
         
         if(request()->filled('u') &&  request()->t == 'project-by-status'){
            $u = request()->u;
            $projects->where('user_id', $u);
         } 


         $projects = $projects->orderBy('name')->get();

        return compact('projects');

    }

    public function getReport($id,$type,$sc = null, $view = null){


        $v = request()->v;

        $categories = $payments = $trades = $project = $projects = [];

        if($type == 'project-by-status'){ 
         $data = @extract($this->getreportByStatus());
        }else{
          $data = @extract($this->getreportDetails($id,$sc,$v));
        }
        
       // return View('reports.'.$type.'-pdf',
       //    ['projects' => $projects, 'categories' => $categories,'payments' => $payments,
       //    'trades' => $trades,'project' => $project,'sc' => $sc]
       //  );

       $vendor = Vendor::find($v);
       $subcontractor = Subcontractor::find($sc);
      
        $pdf = PDF::loadView('reports.'.$type.'-pdf',
          ['projects' => $projects, 'categories' => $categories,'payments' => $payments,
          'trades' => $trades,'project' => $project,'sc' => $sc,'subcontractor' => $subcontractor,'vendor' => $vendor]
        );

        $slug = \Str::slug(@$project->name);

        if($type == 'subcontractor-payment'){
            $type = ((@$sc) ?  @$subcontractor->name  : @$vendor->name ).' Payment Summary';
            $type  = \Str::slug($type);
        }

        // $view = true;
        
        if($view){
         // return $pdf->stream('project_'.$slug .'_budget.pdf');
         return $pdf->setPaper('a4')->output();
        }

         //return $pdf->stream($slug.'_'.$type .'.pdf');
        return $pdf->download($slug.'-'.$type .'.pdf');

    }

    public function reportSendMail(Request $request, $id){
      
     $type = request()->t;
     $sc = request()->sc;

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

        if($request->t == 'awarded-contracts'){
          $pdffile = null;
          $data['subject'] = $project->name.' Awarded and Pending Contacts Report';
          $files =  $this->getContractsReport($id);
          $data['pdffiles'] = $files;
        }
        else{
        $pdffile =  $this->getReport($id,$type,$sc, true);
        }    
    
        $data['pdffile'] = $pdffile;
        $data['fileName'] = $slug.'-'.$type.'.pdf';

        $ccUsers = ($request->filled('cc')) ? explode(',',$request->cc) : [];
        $bccUsers = ($request->filled('cc')) ? explode(',',$request->bcc) : [];

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
        
        // dispatch(
        //   function() use ($request, $data){
        //    \Mail::to($request->recipient)->send(new MaitToSubcontractor($data));
        //   }
        // )->afterResponse();

      return response()->json(
           [
            'status' => 200,
            'message' => 'Sent Successfully!'
           ]
       );
    }
    
    public function ContractsReports(Request $request){
        
         if(!$request->filled('p')) {
               return abort('401');
        } 

        $project = Project::find($request->p);

        $awardedTrades = @$project->proposals()->IsAwarded()->pluck('trade_id');
        
        $trades = $project->trades();
        if($request->t == self::AWARDED){
           $trades->whereIn('trades.id',$awardedTrades); 
        }
        elseif($request->t == self::PENDING){
           $trades->whereNotIn('trades.id',$awardedTrades); 
        }
        $trades = $trades->orderBy('name')->get();
     
         $catids = @($trades->pluck('category_id'))->unique();

         $categories = $paymentCategories = Category::whereIn('id',$catids)->get(); 
         
         $type = $request->filled('t') ? $request->t : self::PENDING;

         $pdf = PDF::loadView('reports.'.$type.'-contracts-pdf',
              ['project' => $project, 'categories' => $categories,'trades' => $trades]
            );

        
        $slug = \Str::slug(@$project->name);

        $view = true;

        $pdf->setOptions(['margin' => 5]);

        $pdf->setPaper('a4', 'landscape');  

        if($view){
         return $pdf->stream('project_'.$slug.'-'.$request->t.'-contracts.pdf');
         return $pdf->setPaper('a4')->output();
        }

        return $pdf->download($slug.'-'.$type .'.pdf');
    }


    public function getContractsReport( $id){

        $project = Project::find($id);

        $awardedTrades = @$project->proposals()->IsAwarded()->pluck('trade_id');
        
        $tradesQuery = $project->trades();
       
        $aTrades = $tradesQuery->whereIn('trades.id',$awardedTrades); 
    
        $pTrades  = $tradesQuery->whereNotIn('trades.id',$awardedTrades); 

        $aTrades = $aTrades->orderBy('name')->get();
        $pTrades = $pTrades->orderBy('name')->get();
     
        $catids = @($aTrades->pluck('category_id'))->unique();

        $categories  = Category::whereIn('id',$catids)->get(); 
         
        $pdf = PDF::loadView('reports.'.self::AWARDED.'-contracts-pdf',
              ['project' => $project, 'categories' => $categories,'trades' => $aTrades]
            );
        
        $slug = \Str::slug(@$project->name);

        $pdf->setOptions(['margin' => 5]);

        $pdf->setPaper('a4', 'landscape');  

        $files[$slug.'-'.self::AWARDED] = $pdf->output();

        $catids = @($pTrades->pluck('category_id'))->unique();

        $categories  = Category::whereIn('id',$catids)->get(); 
         
        $pdf = '';

        $pdf = PDF::loadView('reports.'.self::PENDING.'-contracts-pdf',
              ['project' => $project, 'categories' => $categories,'trades' => $pTrades]
            );

        
        $slug = \Str::slug(@$project->name);

        $pdf->setOptions(['margin' => 5]);

        $pdf->setPaper('a4', 'landscape');  

        $files[$slug.'-'.self::PENDING] = $pdf->output();

        return $files;

    }
}
