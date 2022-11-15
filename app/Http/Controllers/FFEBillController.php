<?php

namespace App\Http\Controllers;

use App\Mail\MaitToSubcontractor;
use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\FFETrade;
use App\Models\Document;
use App\Models\FFEProposal;
use App\Models\FFEBill;
use App\Models\FFEPayment;
use App\Models\FFEVendor;
use App\Models\FFECategory;
use App\Models\DocumentType;
use App\Models\DocumentFile;
use Gate;
use Carbon\Carbon;

use PDF;

class FFEBillController extends Controller
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

        return view('projects.ffe.bills-create',compact('id','proposal','vendors','trades','allTrades','totalAmount','dueAmount'));
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

        
        $proposal  = FFEProposal::find($id);  
         
        if(!$proposal &&  $id == null){
            return redirect('/');
        }

        $totalDueMount =  $this->proposalDueTotalAmount($proposal);

        $data = $request->except('_token');
        
        $non_contract = ($request->filled('non_contract')) ?  $request->non_contract : false;

        if($id == 0){
              
             $non_contract = 1; 
             $request->validate([
                   $non_contract.'_ffe_trade_id' => 'required|exists:f_f_e_trades,id',
                   $non_contract.'_ffe_vendor_id' => 'required|exists:f_f_e_vendors,id',
                   'payment_amount' => ['required']
              ]
          );

        }else{

            $request->validate([
                   $non_contract.'_ffe_trade_id' => 'required|exists:f_f_e_trades,id',
                   $non_contract.'_ffe_vendor_id' => 'required|exists:f_f_e_vendors,id',
                   'payment_amount' => ['required']
              ]
          );
        }

        $data['ffe_trade_id'] = $data[$non_contract.'_ffe_trade_id'];
        $data['ffe_vendor_id'] = $data[$non_contract.'_ffe_vendor_id'];

        $project_id = @$proposal->project->id;
        $project_id = (!$project_id) ? $request->project_id : $project_id;

        $data['project_id']  = (int) $project_id;
        $data['ffe_proposal_id'] = ($id > 0) ? $id : null ;

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d');
        
        $data['total_amount'] = @$this->proposalTotalAmount(@$proposal);

        $project = Project::find($project_id);

        $project_slug = \Str::slug($project->name);

        $trade = FFETrade::find(@$data['ffe_trade_id']);

        $trade_slug = @$trade->slug;

        $public_path = public_path().'/';

        $folderPath = Document::BILLS."/";

        $folderPath .= $project_slug.'/'.$trade_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);


        $data['file'] = '';

        $bill = FFEBill::create($data);
       
        $document_type = DocumentType::where('name', DocumentType::BILL)
                         ->first();

        $name = @$project->name.' '.@$document_type->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['ffe_bill_id' => $bill->id,
                    'document_type_id'        => $document_type->id
                     ],
                     ['name' => $name, 'slug' => $slug,
                     'ffe_bill_id'       => $bill->id,
                     'ffe_proposal_id'   => $id,
                     'project_id'        => $project_id,
                     'document_type_id' => $document_type->id
                     ]
                 );


        if($request->hasFile('file')){

              $file = $request->file('file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $trade_slug.'-'.time().'.'. $file->getClientOriginalExtension();
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
           $bill_status =  FFEBill::PAID_BILL_STATUS;
           $this->updateBillStatus($bill,$bill_status);
        }

        
        return redirect(route('ffe.index',['project' => $project_id]).'#bills')->with('message', 'FFE Bill Created Successfully!');
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
                    ->where('non_contract','0')
                  ->sum('payment_amount');

         $due = (float) $total - (float) $payments;

        return round($due,2);
    } 

    public function proposalDueAmount($proposal,$payment_id){

         $total =  $this->proposalTotalAmount($proposal);  

         $payments = FFEPayment::whereFfeProposalId(@$proposal->id)
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
        $bill = FFEBill::find($id);  

        $project = @$bill->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @\Str::slug($bill->trade->name);

        $project_type_slug = @$project->project_type->slug;

        $folderPath = Document::BILLS."/";

        $folderPath .= "$project_slug/$trade_slug/";

        
        $bill->file = @($bill->file) ? $folderPath.$bill->file : '';


        $bill->date = @($bill->date) ? Carbon::parse($bill->date)->format('m-d-Y') : '' ;

        $vendors = FFEVendor::orderBy('name')->get(); 

        $totalAmount = $this->proposalTotalAmount($bill->proposal);
        $dueAmount = $this->proposalDueTotalAmount($bill->proposal);
       
        $proposalsQry = $project->ffe_proposals()->IsAwarded();

        $proposals = $proposalsQry->get();

        ($request->filled('trade')) ? $proposalsQry->where('ffe_trade_id', $request->trade) : '';

        $trades = $proposals->map(function($prpsl){
             return $prpsl->trade;
        });

        $allTrades = FFETrade::orderBy('name')->get();
         

        return view('projects.ffe.bills-edit',compact('bill','vendors','totalAmount','dueAmount','trades','allTrades'));
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

        $bill = FFEBill::find($id);

        $totalDueMount =  $this->proposalDueTotalAmount($bill->proposal);
     
        $data = $request->except('_token');

         $non_contract = ($request->filled('non_contract')) ?  $request->non_contract : false;

        if($bill->proposal){

             $request->validate([
                   $non_contract.'_ffe_trade_id' => 'required|exists:f_f_e_trades,id',
                   $non_contract.'_ffe_vendor_id' => 'required|exists:f_f_e_vendors,id',
                   'payment_amount'  => ['required']
              ]
          );

        }else{
             $non_contract = 1; 
            $request->validate([
                   $non_contract.'_ffe_trade_id'  => 'required|exists:f_f_e_trades,id',
                   $non_contract.'_ffe_vendor_id' => 'required|exists:f_f_e_vendors,id',
                   'payment_amount'  =>  ['required']
              ]
          );

        }

        $data['ffe_trade_id'] = $data[$non_contract.'_ffe_trade_id'];
        $data['ffe_vendor_id'] = $data[$non_contract.'_ffe_vendor_id'];


        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d');


        $project = @$bill->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @$bill->trade->slug;

        $public_path = public_path().'/';

        $folderPath = Document::BILLS."/";

        $folderPath .= $project_slug.'/'.$trade_slug;
        
        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);
        
        $document_type = DocumentType::where('name', DocumentType::BILL)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$bill->subcontractor->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['ffe_bill_id' => $bill->id,
                          'document_type_id' => $document_type->id],
                     ['name' => $name, 'slug' => $slug,
                     'ffe_bill_id'          => $bill->id,
                     'ffe_proposal_id'      => $id,
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

             $fileName = $trade_slug.'-'.time().'.'. $file->getClientOriginalExtension();

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

        
        return redirect(route('ffe.index',['project' => $bill->project_id]).'?#bills')->with('message', 'FFE Bill Updated Successfully!');
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

         $bill = FFEBill::find($id);

         $project = @$bill->project;

         $project_slug = \Str::slug($project->name);

         $trade_slug = @\Str::slug($bill->trade->name);

         $public_path = public_path().'/';

         $folderPath = Document::BILLS."/";

         $folderPath .= "$project_slug/$trade_slug/";

         $path = @public_path().'/'.$folderPath;

         $file = @$payment->file;
         
         $aPath = public_path().'/'. Document::BILLS."/".Document::ARCHIEVED; 
         \File::makeDirectory($aPath, $mode = 0777, true, true);
        @\File::copy($path.$file, $aPath.'/'.$file);

        @unlink($path.$file);

         $project->documents()
                    ->where(['ffe_bill_id' => $id])->delete();

        if($bill->bill_status == FFEBill::PAID_BILL_STATUS){
           $this->updateBillStatus($bill,FFEBill::UNPAID_BILL_STATUS,true);
        }

         $bill->delete();

        return redirect()->back()->with('message', 'FFE Bill Delete Successfully!');
    }


     public function destroyFile($id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

          $path = request()->path;

          $bill = FFEBill::find($id);

          $file = @end(explode('/', $path));

          $publicPath = public_path().'/';

          $folder = Document::BILLS;
          // if (str_contains($path, Document::LIEN_RELEASES)) { 
          //    $folder = Document::LIEN_RELEASES;
          // }

          $aPath = $publicPath.$folder."/".Document::ARCHIEVED;

          @\File::makeDirectory($aPath, $mode = 0777, true, true);

           @\File::copy($publicPath.$path, $aPath.'/'.$file);

          $docFile  = DocumentFile::whereFile($file)->firstOrFail();

          $coulumn = 'file';

          // $coulumn = ( $file == @$payment->conditional_lien_release_file ) ? 'conditional_lien_release_file' : ( $file == @$payment->unconditional_lien_release_file ? 'unconditional_lien_release_file' : $coulumn);  
          
          @$docFile->delete();  

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
      $bill = FFEBill::find($id);
      $bill_status = $request->bill_status;
      $bill_status = ($bill_status == 'true') ? FFEBill::PAID_BILL_STATUS : FFEBill::UNPAID_BILL_STATUS;
      $this->updateBillStatus($bill,$bill_status);
      return redirect()->back()->with('message', 'Status Updated Successfully!');   
    }

    public function updateBillStatus($bill, $bill_status, $force = false){

      if($bill->bill_status == $bill_status && !$force){
        return;
      }
      if($bill_status == FFEBill::PAID_BILL_STATUS){
          $data = $bill->toArray();
          $data['ffe_bill_id'] = $data['id'];
          $data['f_f_e_trade_id'] = $data['ffe_trade_id'];
          $data['f_f_e_vendor_id'] = $data['ffe_vendor_id'];
          unset($data['id']);
          unset($data['ffe_payment_id']);
          unset($data['ffe_trade_id']);
          unset($data['ffe_vendor_id']);
          unset($data['bill_status']);
          unset($data['created_at']);
          unset($data['updated_at']);

          $payment  = FFEPayment::create($data);

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
               ->firstOrCreate(['ffe_payment_id' => $payment->id,
                'document_type_id' => $document_type->id
                 ],
                 ['name' => $name, 'slug' => $slug,
                 'ffe_payment_id'       => $payment->id,
                 'ffe_proposal_id'      => @$bill->proposal_id,
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

                  $document->files()->create($fileArr);

          }

          $bill->update(['bill_status' => $bill_status]);
          
          return true;
      }
      else if ($bill_status == FFEBill::UNPAID_BILL_STATUS){

           FFEPayment::where('ffe_bill_id',$bill->id)->delete();

          if($bill->file){

            $project = @$bill->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($bill->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $publicPath = public_path().'/';

            $folderPath = Document::BILLS."/";

            $folderPath .= "$project_slug/$trade_slug/";

            $invoicePath = Document::INVOICES."/$project_slug/$trade_slug/";
            
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
