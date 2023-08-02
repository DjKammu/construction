<?php

namespace App\Http\Controllers;

use App\Mail\MaitToSubcontractor;
use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\SoftCostTrade;
use App\Models\Document;
use App\Models\SoftCostProposal;
use App\Models\SoftCostBill;
use App\Models\SoftCostPayment;
use App\Models\SoftCostVendor;
use App\Models\SoftCostCategory;
use App\Models\DocumentType;
use App\Models\DocumentFile;
use Gate;
use Carbon\Carbon;

use PDF;

class SoftCostBillController extends Controller
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

        $proposalsQry = $project->sc_proposals()->IsAwarded();

        $proposals = $proposalsQry->get();

        ($request->filled('trade')) ? $proposalsQry->where('soft_cost_trade_id', $request->trade) : '';

        $proposal = $proposalsQry->first();

        $vendors = SoftCostVendor::orderBy('name')->get();

        $trades = $proposals->map(function($prpsl){
             return $prpsl->trade;
        });

        //if(!@$project->proposals()->exists()){
             $allTrades = SoftCostTrade::orderBy('name')->get();
       //  }
        $totalAmount = $this->proposalTotalAmount($proposal);
        $dueAmount = $this->proposalDueTotalAmount($proposal);

        return view('projects.soft_cost.bills-create',compact('id','proposal','vendors','trades','allTrades','totalAmount','dueAmount'));
    }  



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$project, $id)
    {
          if(Gate::denies('add')) {
               return abort('401');
        } 

        
        $proposal  = SoftCostProposal::find($id);  
         
        if(!$proposal &&  $id == null){
            return redirect('/');
        }

        $totalDueMount =  $this->proposalDueTotalAmount($proposal);

        $data = $request->except('_token');

        $non_contract = ($request->filled('non_contract')) ?  $request->non_contract : false;

        if($id == 0){
              
             $non_contract = 1; 
             $request->validate([
                   $non_contract.'_soft_cost_trade_id' => 'required|exists:soft_cost_trades,id',
                   $non_contract.'_soft_cost_vendor_id' => 'required|exists:soft_cost_vendors,id',
                   'payment_amount' => ['required']
              ]
          );

        }else{

            $request->validate([
                   $non_contract.'_soft_cost_trade_id' => 'required|exists:soft_cost_trades,id',
                   $non_contract.'_soft_cost_vendor_id' => 'required|exists:soft_cost_vendors,id',
                   'payment_amount' => ['required']
              ]
          );
        }

        $data['soft_cost_trade_id'] = $data[$non_contract.'_soft_cost_trade_id'];
        $data['soft_cost_vendor_id'] = $data[$non_contract.'_soft_cost_vendor_id'];

        $project_id = @$proposal->project->id;
        $project_id = (!$project_id) ? $request->project_id : $project_id;

        $data['project_id']  = (int) $project_id;
        $data['soft_cost_proposal_id'] = ($id > 0) ? $id : null ;

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d');
        
        $data['total_amount'] = @$this->proposalTotalAmount(@$proposal);

        $project = Project::find($project_id);

        $project_slug = \Str::slug($project->name);

        $trade = SoftCostTrade::find(@$data['soft_cost_trade_id']);

        $trade_slug = @$trade->slug;

        $public_path = public_path().'/';

        $folderPath = Document::BILLS."/";

        $folderPath .= $project_slug.'/'.$trade_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

         $folderPath3 = Document::BILLS_PURCHASE_ORDERS."/";

        $folderPath3 .= $project_slug.'/'.$trade_slug;

        \File::makeDirectory($public_path.$folderPath3, $mode = 0777, true, true);


        $data['file'] = '';

        $bill = SoftCostBill::create($data);
       
        $document_type = DocumentType::where('name', DocumentType::BILL)
                         ->first();

        $name = @$project->name.' '.@$document_type->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['soft_cost_bill_id' => $bill->id,
                    'document_type_id'        => $document_type->id
                     ],
                     ['name' => $name, 'slug' => $slug,
                     'soft_cost_bill_id'       => $bill->id,
                     'soft_cost_proposal_id'   => $id,
                     'project_id'        => $project_id,
                     'document_type_id' => $document_type->id
                     ]
                 );


        if($request->hasFile('file')){

              $file = $request->file('file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $slug.'-'.time().'.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

            $bill->update(['file' => $fileName]);

            $document->files()->create($fileArr);
        }

        if($request->hasFile('purchase_order')){

              $document_type = DocumentType::where('name', DocumentType::PURCHASE_ORDER)
                         ->first();
              $name = @$project->name.' '.@$document_type->name;                
              $slug = @\Str::slug($name); 

              $document = $project->documents()
                 ->firstOrCreate(['soft_cost_bill_id' => $bill->id,
                  'document_type_id'        => $document_type->id
                   ],
                   ['name' => $name, 'slug' => $slug,
                   'soft_cost_bill_id'       => $bill->id,
                   'soft_cost_proposal_id'   => $id,
                   'project_id'        => $project_id,
                   'document_type_id' => $document_type->id
                   ]
               );

              $file = $request->file('purchase_order');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $slug.'-'.time().'.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath3, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

            $bill->update(['purchase_order' => $fileName]);

            $document->files()->create($fileArr);
        }

        if($request->bill_status == 1){
           $bill_status =  SoftCostBill::PAID_BILL_STATUS;
           $this->updateBillStatus($bill,$bill_status);
        }
      
        
        return redirect(route('projects.soft-cost.index',['project' => $project_id]).'#bills')->with('message', 'SoftCost Bill Created Successfully!');
    }
     
    public function proposalTotalAmount($proposal){

       $total =  (float) @$proposal->material + (float) @$proposal->labour_cost + (float) @$proposal->subcontractor_price;  

         if(!@$proposal->changeOrders)
         {
             return $total;
         }

         foreach(@$proposal->changeOrders as $k => $order){
           if($order->type == \App\Models\SoftCostChangeOrder::ADD ){
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
     
         $payments = SoftCostPayment::whereSoftCostProposalId(@$proposal->id)
                    ->where('non_contract','0')
                  ->sum('payment_amount');

         $due = (float) $total - (float) $payments;

        return round($due,2);
    } 

    public function proposalDueAmount($proposal,$payment_id){

         $total =  $this->proposalTotalAmount($proposal);  

         $payments = SoftCostPayment::whereSoftCostProposalId(@$proposal->id)
         ->where('non_contract','0')                   
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
    public function show(Request $request, $projectId, $id)
    {
         if(Gate::denies('edit')) {
           return abort('401');
        } 
        $bill = SoftCostBill::find($id);  

        $project = @$bill->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @\Str::slug($bill->trade->name);

        $project_type_slug = @$project->project_type->slug;

        $folderPath = Document::BILLS."/";

        $folderPath .= "$project_slug/$trade_slug/";

        $folderPath3 = Document::BILLS_PURCHASE_ORDERS."/";

        $folderPath3 .= "$project_slug/$trade_slug/";

        $bill->file = @($bill->file) ? $folderPath.$bill->file : '';
        $bill->purchase_order = @($bill->purchase_order) ? $folderPath3.$bill->purchase_order : '';


        $bill->date = @($bill->date) ? Carbon::parse($bill->date)->format('m-d-Y') : '' ;

        $vendors = SoftCostVendor::orderBy('name')->get(); 

        $totalAmount = $this->proposalTotalAmount($bill->proposal);
        $dueAmount = $this->proposalDueTotalAmount($bill->proposal);
       
        $proposalsQry = $project->sc_proposals()->IsAwarded();

        $proposals = $proposalsQry->get();

        ($request->filled('trade')) ? $proposalsQry->where('soft_cost_trade_id', $request->trade) : '';

        $trades = $proposals->map(function($prpsl){
             return $prpsl->trade;
        });

        $allTrades = SoftCostTrade::orderBy('name')->get();
         

        return view('projects.soft_cost.bills-edit',compact('bill','vendors','totalAmount','dueAmount','trades','allTrades'));
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

        $bill = SoftCostBill::find($id);

        $totalDueMount =  $this->proposalDueTotalAmount($bill->proposal);
     
        $data = $request->except('_token');

        $non_contract = ($request->filled('non_contract')) ?  $request->non_contract : false;

        if($bill->proposal){

             $request->validate([
                   $non_contract.'_soft_cost_trade_id' => 'required|exists:soft_cost_trades,id',
                   $non_contract.'_soft_cost_vendor_id' => 'required|exists:soft_cost_vendors,id',
                   'payment_amount'  => ['required']
              ]
          );

        }else{
             $non_contract = 1; 
             $request->validate([
                   $non_contract.'_soft_cost_trade_id'  => 'required|exists:soft_cost_trades,id',
                   $non_contract.'_soft_cost_vendor_id' => 'required|exists:soft_cost_vendors,id',
                   'payment_amount'  =>  ['required']
              ]
          );

        }

        $data['soft_cost_trade_id'] = $data[$non_contract.'_soft_cost_trade_id'];
        $data['soft_cost_vendor_id'] = $data[$non_contract.'_soft_cost_vendor_id'];


        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d');


        $project = @$bill->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @$bill->trade->slug;

        $public_path = public_path().'/';

        $folderPath = Document::BILLS."/";

        $folderPath .= $project_slug.'/'.$trade_slug;
        
        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        $folderPath3 = Document::BILLS_PURCHASE_ORDERS."/";

        $folderPath3 .= $project_slug.'/'.$trade_slug;
        
        \File::makeDirectory($public_path.$folderPath3, $mode = 0777, true, true);
        
        $document_type = DocumentType::where('name', DocumentType::BILL)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$bill->subcontractor->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['soft_cost_bill_id' => $bill->id,
                          'document_type_id' => $document_type->id],
                     ['name' => $name, 'slug' => $slug,
                     'soft_cost_bill_id'          => $bill->id,
                     'soft_cost_proposal_id'      => @$bill->proposal->id,
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

             $fileName = $slug.'-'.time().'.'. $file->getClientOriginalExtension();

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


        if($request->hasFile('purchase_order')){
              @unlink($folderPath.'/'.$bill->purchase_order);
              $file = $request->file('purchase_order');

               $document_type = DocumentType::where('name', DocumentType::PURCHASE_ORDER)
                         ->first();

               
               $name = @$project->name.' '.@$document_type->name;                
               $slug = @\Str::slug($name); 


               $document = $project->documents()
                   ->firstOrCreate(['soft_cost_bill_id' => $bill->id,
                          'document_type_id' => $document_type->id],
                     ['name' => $name, 'slug' => $slug,
                     'soft_cost_bill_id'          => $bill->id,
                     'soft_cost_proposal_id'      => @$bill->proposal->id,
                     'document_type_id' => $document_type->id,
                     'subcontractor_id' => @$proposal->subcontractor->id
                     ]
                 );


              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $slug.'-'.time().'.'. $file->getClientOriginalExtension();

             $file->storeAs($folderPath3, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                          'name' => $name,
                          'date' => $date,
                          'month' => $month,
                          'year' => $year
                          ];

           @$document->files()->delete();             
            $document->files()->create($fileArr);
            $data['purchase_order'] = $fileName;
        }


        $bill->update($data);

        
        return redirect(route('projects.soft-cost.index',['project' => $bill->project_id]).'?#bills')->with('message', 'Soft Cost Bill Updated Successfully!');
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

         $bill = SoftCostBill::find($id);

         $project = @$bill->project;

         $project_slug = \Str::slug($project->name);

         $trade_slug = @\Str::slug($bill->trade->name);

         $public_path = public_path().'/';

         $folderPath = Document::BILLS."/";

         $folderPath .= "$project_slug/$trade_slug/";

         $path = @public_path().'/'.$folderPath;

         $file = @$bill->file;
         
         $aPath = public_path().'/'. Document::BILLS."/".Document::ARCHIEVED; 
         \File::makeDirectory($aPath, $mode = 0777, true, true);
        @\File::copy($path.$file, $aPath.'/'.$file);

        @unlink($path.$file);

         $project->documents()
                    ->where(['soft_cost_bill_id' => $id])->delete();

        if($bill->bill_status == SoftCostBill::PAID_BILL_STATUS){
           $this->updateBillStatus($bill,SoftCostBill::UNPAID_BILL_STATUS,true);
        }

         $bill->delete();

        return redirect()->back()->with('message', 'SoftCost Bill Delete Successfully!');
    }


     public function destroyFile($project, $id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

          $path = request()->path;

          $bill = SoftCostBill::find($id);

          $file = @end(explode('/', $path));

          $publicPath = public_path().'/';

          $folder = Document::BILLS;

          if (str_contains($path, Document::BILLS_PURCHASE_ORDERS)) { 
             $folder = Document::BILLS_PURCHASE_ORDERS;
          }
           
          $aPath = $publicPath.$folder."/".Document::ARCHIEVED;

          @\File::makeDirectory($aPath, $mode = 0777, true, true);

           @\File::copy($publicPath.$path, $aPath.'/'.$file);

          $docFile  = DocumentFile::whereFile($file)
                      ->whereHas('document', function($q){
                           $q->where('soft_cost_payment_id', NULL);
                            $q->orWhere('soft_cost_payment_id', 0);
                            $q->orWhere('soft_cost_payment_id', '');
                      })->first();

          $coulumn = 'file';

          $coulumn =  ($file == @$bill->purchase_order) ? 'purchase_order' : $coulumn ;  

          (@$docFile) ?  @$docFile->delete() : '';

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

    public function billStatus(Request $request, $project, $id){
      $bill = SoftCostBill::find($id);
      $bill_status = $request->bill_status;
      $bill_status = ($bill_status == 'true') ? SoftCostBill::PAID_BILL_STATUS : SoftCostBill::UNPAID_BILL_STATUS;
      $this->updateBillStatus($bill,$bill_status);
      return redirect()->back()->with('message', 'Status Updated Successfully!');   
    }

    public function updateBillStatus($bill, $bill_status, $force = false){

      if($bill->bill_status == $bill_status && !$force){
        return;
      }

      if($bill_status == SoftCostBill::PAID_BILL_STATUS){
          $data = $bill->toArray();
          $data['soft_cost_bill_id'] = $data['id'];
          unset($data['id']);
          unset($data['soft_cost_payment_id']);
          unset($data['bill_status']);
          unset($data['created_at']);
          unset($data['updated_at']);
  
          $payment  = SoftCostPayment::create($data);

          if($bill->file){

            $project = @$bill->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($bill->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $publicPath = public_path().'/';

            $folderPath = Document::BILLS."/";

            $folderPath .= "$project_slug/$trade_slug/";

            $invoicePath = Document::INVOICES."/$project_slug/$trade_slug/";
            @\File::makeDirectory($public_path.$invoicePath, $mode = 0777, true, true);
            @\File::copy($publicPath.$folderPath.$bill->file, $publicPath.$invoicePath.$bill->file);
             
            $document_type = DocumentType::where('name', DocumentType::INVOICE)
                         ->first();

            $name = @$project->name.' '.@$document_type->name; 

            $slug = @\Str::slug($name);                

            $document = $project->documents()
               ->UpdateOrCreate(['soft_cost_payment_id' => $payment->id,
                'document_type_id' => $document_type->id
                 ],
                 ['name' => $name, 'slug' => $slug,
                 'soft_cost_payment_id'       => $payment->id,
                 'soft_cost_proposal_id'      => @$bill->proposal_id,
                 'document_type_id' => $document_type->id
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
             @$document->files()->delete(); 
             $document->files()->create($fileArr);

          }

          if($bill->purchase_order){

            $project = @$bill->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($bill->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $publicPath = public_path().'/';

            $folderPath = Document::BILLS_PURCHASE_ORDERS."/";

            $folderPath .= "$project_slug/$trade_slug/";

            $invoicePath = Document::PROJECTS_PURCHASE_ORDERS."/$project_slug/$trade_slug/";
            @\File::makeDirectory($public_path.$invoicePath, $mode = 0777, true, true);
            
            @\File::copy($publicPath.$folderPath.$bill->purchase_order, $publicPath.$invoicePath.$bill->purchase_order);
             
            $document_type = DocumentType::where('name', DocumentType::PURCHASE_ORDER)
                         ->first();

            $name = @$project->name.' '.@$document_type->name; 

            $slug = @\Str::slug($name);                

            $document = $project->documents()
               ->UpdateOrCreate(['soft_cost_payment_id' => $payment->id,
                'document_type_id' => $document_type->id
                 ],
                 ['name' => $name, 'slug' => $slug,
                 'soft_cost_payment_id'       => $payment->id,
                 'soft_cost_proposal_id'      => @$bill->proposal_id,
                 'document_type_id' => $document_type->id
                 ]
             );

            $date  = date('d');
            $month = date('m');
            $year  = date('Y');

            $fileArr = ['file' => $bill->purchase_order,
                        'name' => $name,
                        'date' => $date,'month' => $month,
                        'year' => $year
                        ];
           @$document->files()->delete(); 
           $document->files()->create($fileArr);

          }

          $bill->update(['bill_status' => $bill_status]);
          
          return true;
      }
      else if ($bill_status == SoftCostBill::UNPAID_BILL_STATUS){

          SoftCostPayment::where('soft_cost_bill_id',$bill->id)->delete();

          if($bill->file){

            $project = @$bill->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($bill->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $publicPath = public_path().'/';

            $invoicePath = Document::INVOICES."/$project_slug/$trade_slug/";

            @unlink($publicPath.$invoicePath.$bill->file);

            $docFile  = DocumentFile::whereFile($bill->file)
                        ->whereHas('document', function($q){
                            $q->where('soft_cost_bill_id', NULL);
                            $q->orWhere('soft_cost_bill_id', 0);
                            $q->orWhere('soft_cost_bill_id', '');
                        })->first();            

            (@$docFile) ?  @$docFile->delete() : ''; 

          }

          if($bill->purchase_order){

            $project = @$bill->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($bill->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $publicPath = public_path().'/';

            $invoicePath = Document::PROJECTS_PURCHASE_ORDERS."/$project_slug/$trade_slug/";
            
            @unlink($publicPath.$invoicePath.$bill->purchase_order);

            $docFile  = DocumentFile::whereFile($bill->purchase_order)
                        ->whereHas('document', function($q){
                            $q->where('soft_cost_bill_id', NULL);
                            $q->orWhere('soft_cost_bill_id', 0);
                            $q->orWhere('soft_cost_bill_id', '');
                        })->first();
          
            (@$docFile) ?  @$docFile->delete() : '';

          }
          
          $bill->update(
           ['bill_status' => $bill_status]
          );
          

          return true;



      }

      return;

    }


}
