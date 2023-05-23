<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentStatus;
use App\Models\Subcontractor;
use App\Models\FFEITBTracker;
use App\Models\DocumentType;
use App\Models\PropertyType;
use App\Models\FFEProposal;
use App\Models\FFECategory;
use App\Models\ProjectType;
use App\Models\FFEVendor;
use App\Models\FFETrade;
use App\Models\Document;
use App\Models\Payment;
use App\Models\Project;
use App\Models\BudgetLine;
use App\Models\Status;
use Carbon\Carbon;
use Gate;


class BudgetController extends Controller
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
         $project = Project::find($id);


        if(!$project){
            return redirect()->back();
        }

        if(!$project->total_construction_sq_ft){
           return redirect()->back()->with('error' ,'Total Construction SQ Ft is not available!');
        }
          
         $projectTypes = ProjectType::orderBy('name')->get(); 

         $projects = Project::has('budget_lines')->orderBy('name');

         if(request()->filled('project_type')){
            $p = request()->project_type;
            $projects->whereHas('project_type', function($q) use ($p){
                $q->where('slug', $p);
            });
         } 

         $projects = $projects->get()->except($id);

         return view('projects.budget.index',compact('projects','project','projectTypes'));


    }


    public function create($id)
    {
        if(Gate::denies('add')) {
               return abort('401');
         } 

       $project  = Project::find($id);  
      
       $budget_lines = $project->budget_lines();

        $orderBy = 'created_at';  
        $order ='DESC' ;
                    
      if(request()->filled('order')){
    
            $orderBy = request()->filled('orderBy') ? ( !in_array(request()->orderBy, 
                ['account_number'] ) ? 'created_at' : request()->orderBy ) : 'created_at';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'DESC' 
             : request()->order;
       }


      $budget_lines = $budget_lines->orderBy($orderBy, $order)->get();
      $total_price_sq_ft = @$budget_lines->sum('price_sq_ft');
      $total_budget = @$budget_lines->sum('budget');

       return response()->json(
           [
            'status' => 200,
            'data' => [
              'lines' => $budget_lines,
              'total_budget' => $total_budget,
              'total_price_sq_ft' => $total_price_sq_ft
            ]
           ]
       );

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
        
        $project  = Project::find($id);  

        $data = $request->data;
        
        $lines = $request->lines;

        $budget_lines = $project->budget_lines();

        // $applications = @$project->applications()->exists();

        // if($applications && array_filter($data) ){

        //        return response()->json(
        //        [
        //         'status' => 200,
        //         'error'  => true,
        //         'message' => 'Project Lines can`t be Added!'
        //        ]
        //     );
        //  }

        foreach ($data['trade'] as $key => $value) {

             $budget_lines->Create(
                    [
                      'trade' => $value,
                      'account_number' => $data['account_number'][$key] ,
                      'price_sq_ft' => @$data['price_sq_ft'][$key] ,
                      'budget' => @$data['budget'][$key] 
                    ]
              );

        }

        $ifBudget = false;

        if($lines){

         foreach ($lines as $key => $line) {
              $ifBudget = (@$line['price_sq_ft']) ? true : false;
              BudgetLine::where('id',$line['id'])
                  ->update(
                  [
                      'account_number' => $line['account_number'],
                      'price_sq_ft' => @$line['price_sq_ft'],
                      'trade' => $line['trade'],
                      'budget' => @$line['budget']
                  ]
              );
         }

        }  

        $msg = ($ifBudget) ? 'Budget Save' : 'Budget Lines added';

        return response()->json(
           [
            'status' => 200,
            'message' => $msg.' Successfully!'
           ]
        );
        
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

    public function otherAssign(Request $request, $id)
    {
          if(Gate::denies('edit')) {
               return abort('401');
          } 

        $project  = Project::find($request->project_id);  
        $assignToproject  = Project::find($id);  

         if(!@$project || !@$assignToproject){
            return redirect()->back();
        }

        $budget_lines = $project->budget_lines()->get();
        

        foreach ($budget_lines as $key => $value) {

          $update = ['trade' => $value['trade'],
                      'account_number' => $value['account_number'] 
              ];

            $assignToproject->budget_lines()->UpdateOrCreate($update,$update);
        }

        return redirect()->back()->with('Budget Lines added Successfully!');



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


        $budget_line = BudgetLine::find($id);
        
        // $applications = @$budget_line->project->applications()->exists();

        // if($applications){

        //      return response()->json(
        //      [
        //       'status' => 200,
        //       'error' => true,
        //       'message' => 'Project Lines can`t be Deleted!'
        //      ]
        //   );
        // }

        $budget_line->delete();

        return response()->json(
           [
            'status' => 200,
            'message' => 'Budget Line Delete Successfully!'
           ]
        );

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
