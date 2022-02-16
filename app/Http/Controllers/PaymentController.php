<?php

namespace App\Http\Controllers;

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
use App\Models\Subcontractor;
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

        $vendors = Vendor::all();

        $trades = $proposals->map(function($prpsl){
             return $prpsl->trade;
        });

        $totalAmount = $this->proposalTotalAmount($proposal);
        $dueAmount = $this->proposalDueTotalAmount($proposal);

        return view('projects.includes.payments-create',compact('proposal','vendors','trades',
          'totalAmount','dueAmount'));
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
         
        if(!$proposal){
            return redirect('/');
        }

        $totalDueMount =  $this->proposalDueTotalAmount($proposal);

        $data = $request->except('_token');

        $request->validate([
                'subcontractor_id' => ['required',
                'exists:subcontractors,id'],
                'trade_id' => 'required|exists:trades,id',
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

        $project_id = @$proposal->project->id;
        $data['project_id']  = $project_id;
        $data['proposal_id'] = $id;

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d');
        
        $data['total_amount'] = $this->proposalTotalAmount($proposal);


        $project = Project::find($project_id);

        $project_slug = \Str::slug($project->name);

        $trade = Trade::find($request->trade_id);

        $trade_slug = @$trade->slug;

        $subcontractor = Subcontractor::find($request->subcontractor_id);

        $subcontractor_slug = $subcontractor->slug;

        $public_path = public_path().'/';

        $folderPath = Document::INVOICES."/";

        $folderPath .= $project_slug.'/'.$trade_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        $data['file'] = '';

        $payment = Payment::create($data);

        $document_type = DocumentType::where('name', DocumentType::INVOICE)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$proposal->subcontractor->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['payment_id' => $payment->id],
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

        

        return redirect(route('projects.show',['project' => $project_id]).'#payments')->with('message', 'Payment Created Successfully!');
    }
     
    public function proposalTotalAmount($proposal){

       $total =  (float) @$proposal->material + (float) @$proposal->labour_cost + (float) @$proposal->subcontractor_price;  
  
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
     
         $payments = Payment::whereProposalId($proposal->id)
                    ->whereNull('vendor_id')->sum('payment_amount');

         $due = (float) $total - (float) $payments;

        return round($due,2);
    } 

    public function proposalDueAmount($proposal,$payment_id){

         $total =  $this->proposalTotalAmount($proposal);  


         $payments = Payment::whereProposalId($proposal->id)
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
    public function show($id)
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
        
        $payment->file = @($payment->file) ? $folderPath.$payment->file : '';

        $payment->date = @($payment->date) ? Carbon::parse($payment->date)->format('m-d-Y') : '' ;

        $vendors = Vendor::all(); 

        $totalAmount = $this->proposalTotalAmount($payment->proposal);
        $dueAmount = $this->proposalDueTotalAmount($payment->proposal);
         
         session()->flash('url', route('projects.show',['project' => $payment->project_id]).'?#payments'); 

        return view('projects.includes.payments-edit',compact('subcontractor','payment','vendors','totalAmount','dueAmount'));
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

        $request->validate([
              // 'subcontractor_id' => ['required',
              // 'exists:subcontractors,id'],
              'trade_id' => 'required|exists:trades,id',
              'payment_amount' => ['required',
                    function ($attribute, $value, $fail) use ($totalDueMount,$payment){
                      if (!request()->filled('vendor_id') && $value > ((float) $totalDueMount + $payment->payment_amount) ) {
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

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d');


        $project = @$payment->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @$payment->trade->slug;

        $subcontractor_slug = @$payment->subcontractor->slug;

        $public_path = public_path().'/';

        $folderPath = Document::INVOICES."/";

        $folderPath .= $project_slug.'/'.$trade_slug;
        
        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);
        $document_type = DocumentType::where('name', DocumentType::INVOICE)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$payment->subcontractor->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['payment_id' => $id],
                     ['name' => $name, 'slug' => $slug,
                     'payment_id'       => $payment->id,
                     'proposal_id'      => $id,
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

           @$document->files()->whereFile($file)->delete();             
            $document->files()->create($fileArr);
            $data['file'] = $fileName;
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

         $path = @public_path().'/'.$folderPath;

         $file = @$payment->file;
         
         $aPath = public_path().'/'. Document::INVOICES."/".Document::ARCHIEVED; 
         \File::makeDirectory($aPath, $mode = 0777, true, true);
        
          @\File::copy($path.$file, $aPath.'/'.$file);
          @unlink($path.$file);

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

          $aPath = $publicPath.Document::INVOICES."/".Document::ARCHIEVED; 

          @\File::makeDirectory($aPath, $mode = 0777, true, true);

          @\File::copy($publicPath.$path, $aPath.'/'.$file);

          $file = '';

          $documents = @$payment->project->documents()
                       ->where('payment_id',$id)->first();
          $docFiles = @$documents->files()->whereFile($file)->delete();             

          $payment->update(['file' => $file]);

          @unlink($path);

         return redirect()->back()->with('message', 'File Delete Successfully!');
    }

    public function downloadPDF($id){

        $project = Project::find($id); 
        $trades = $project->trades()->get();
        $catids = @($trades->pluck('category_id'))->unique();
        $categories = Category::whereIn('id',$catids)->get(); 

        $pdf = PDF::loadView('projects.includes.budget-pdf',
          ['categories' => $categories,
          'trades' => $trades,'project' => $project]
        );

        $slug = \Str::slug($project->name);

        // return $pdf->stream('project_'.$slug .'_budget.pdf');
        return $pdf->download('project_'.$slug.'_budget.pdf');

    }
}
