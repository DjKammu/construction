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
use PDF;
use Excel;
use App\Exports\BudgetLinesExport;

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

       @extract($this->getLines($id));

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

        $projectLines = $request->projectLines;
        $totalBudget = 0;

        if($lines){

         foreach ($lines as $key => $line) {
              $totalBudget = (float) (@$line['budget']) + $totalBudget;
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
        
        $msg2 = ' AIA Applications already exists for this project!';

        if(!$projectLines && !$project->applications()->exists()){
           $project->original_amount = $totalBudget;
           $project->save();
           $msg2 = ' Budget saved for AIA Applications!';
        }

        $msg = ((!$projectLines) ? 'Budget Save' : 'Budget Lines added ' ) . ' Successfully!';

        $msg .= (!$projectLines) ? $msg2 : '';

        return response()->json(
           [
            'status' => 200,
            'message' => $msg
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

    public function pdfDownload(Request $request, $id){
     
     @extract($this->getLines($id));

      $pdf = PDF::loadView('projects.budget.pdf',
              ['lines' => $budget_lines,
              'total_budget' => $total_budget,
              'project' => $project,
              'total_price_sq_ft' => $total_price_sq_ft]
        );

        $slug = \Str::slug(@$project->name).'-budget';

         $view = request()->view;
        
        if($view){
         return $pdf->setPaper('a4')->output();
        }
        
        //return $pdf->stream($slug .'.pdf');

        return $pdf->download($slug.'.pdf');
    } 

    public function excelDownload(Request $request, $id){
     
     @extract($this->getLines($id));

     $data = ['lines' => $budget_lines,
              'total_budget' => $total_budget,
              'project' => $project,
              'total_price_sq_ft' => $total_price_sq_ft];
     $file = \Str::slug(@$project->name).'-budget.xlsx';         
     
     return Excel::download(new BudgetLinesExport($data) , $file);

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
        $total_construction_sq_ft = $project->total_construction_sq_ft;

        foreach ($budget_lines as $key => $value) {

          $where = ['trade' => $value['trade'],
                      'account_number' => $value['account_number']
            ];

          $update = ['trade' => $value['trade'],
                      'account_number' => $value['account_number'],
                      'price_sq_ft' => number_format($value['budget']/$total_construction_sq_ft,2),
                      'budget' => $value['budget'] 
            ];

            $assignToproject->budget_lines()->UpdateOrCreate($where,$update);
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
     
    public function getLines( $id){

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

      $budget_lines = $budget_lines->select('*', \DB::raw('CAST(price_sq_ft AS DOUBLE) AS price_sq_ft'),
        \DB::raw('CAST(budget AS DOUBLE) AS budget'))->orderBy($orderBy, $order)->get();

      $total_price_sq_ft = (float)  @$budget_lines->sum('price_sq_ft');
      $total_budget =(float)  @$budget_lines->sum('budget');

      return compact('budget_lines','total_price_sq_ft','total_budget','project');

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

    
    
}
