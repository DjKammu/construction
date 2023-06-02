<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectLine;
use App\Models\ChangeOrderApplicationLine;
use Gate;
use Carbon\Carbon;
use PDF;

class ProjectLineController extends Controller
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
       $project  = Project::find($id);  
      
       $project_lines = $project->project_lines();

        $orderBy = 'created_at';  
        $order ='DESC' ;
                    
      if(request()->filled('order')){
    
            $orderBy = request()->filled('orderBy') ? ( !in_array(request()->orderBy, 
                ['account_number'] ) ? 'created_at' : request()->orderBy ) : 'created_at';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'DESC' 
             : request()->order;
       }


      $project_lines = $project_lines->orderBy($orderBy, $order)->get();

       return response()->json(
           [
            'status' => 200,
            'data' => $project_lines
           ]
       );

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

        if(!$project->retainage_percentage || !$project->original_amount){
           return redirect()->back()->with('error' ,'Retainage Percentage or Original Amount is not available!');
        }

        $applications_count = $project->applications()->count();
      

        return view('projects.aia.index',compact('project','applications_count'));
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

        $project_lines = $project->project_lines();

        $applications = @$project->applications()->exists();

        if($applications && array_filter($data) ){

               return response()->json(
               [
                'status' => 200,
                'error'  => true,
                'message' => 'Project Lines can`t be Added!'
               ]
            );
         }

        foreach ($data['description'] as $key => $value) {

             $project_lines->Create(
                    [
                      'description' => $value,
                      'value' => $data['value'][$key],
                      'retainage' => $data['retainage'][$key],
                      'account_number' => $data['account_number'][$key] 
                    ]
              );

        }

        if($lines){

         foreach ($lines as $key => $line) {
              ProjectLine::where('id',$line['id'])
                  ->update(
                  [
                      'value' => $line['value'],
                      'retainage' => $line['retainage'],
                      'description' => $line['description'],
                      'account_number' => $line['account_number']
                  ]
              );
         }

        }  

        return response()->json(
           [
            'status' => 200,
            'message' => 'Project Lines added Successfully!'
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
        //
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
       //
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

        $project_line = ProjectLine::find($id);
        
        $applications = @$project_line->project->applications()->exists();

        if($applications){

             return response()->json(
             [
              'status' => 200,
              'error' => true,
              'message' => 'Project Lines can`t be Deleted!'
             ]
          );
        }

        $project_line->delete();

        return response()->json(
           [
            'status' => 200,
            'message' => 'Project Lines Delete Successfully!'
           ]
        );
    }


    public function deleteLines(Request $request, $id){
         
         if(Gate::denies('delete')) {
               return abort('401');
          } 

        $project  = Project::find($id);  

        $project_lines = $project->project_lines();

        $applications = @$project->applications();  
        $changeOrderApplications = @$project->changeOrderApplications();  
        $closeProject = @$project->closeProject(); 

        $password = $request->password;

        $user = \Auth::user();

        if(!\Hash::check($password, $user->password)) { 
          return response()->json(
               [
                'status' => 200,
                'error' => true,
                'message' => 'Password not matched!'
               ]
            );
        }

        if(@$applications->exists() == false){

             return response()->json(
             [
              'status' => 200,
              'error' => true,
              'message' => 'Application can`t be Deleted!'
             ]
          );
        }
        
        $applications->delete();
        $changeOrderApplications->delete();
        $closeProject->delete();

        return response()->json(
           [
            'status' => 200,
            'message' => 'Application Delete Successfully!'
           ]
        );

    }

    public function importLines(Request $request, $id){
         
         if(Gate::denies('delete')) {
               return abort('401');
          } 

        $project  = Project::find($id);  

        $project_lines = $project->project_lines();

        $password = $request->password;

        $user = \Auth::user();

        if(!\Hash::check($password, $user->password)) { 
          return response()->json(
               [
                'status' => 200,
                'error' => true,
                'message' => 'Password not matched!'
               ]
            );
        }

        $project_lines->delete();

        $bProject =  Project::find($id);
        $budget_lines = $bProject->budget_lines()->get();

        if($budget_lines->count() == 0){
            return response()->json(
               [
                'status' => 200,
                'error' => true,
                'message' => 'Line Items are not there!'
               ]
            );
        }

        foreach ($budget_lines as $key => $budget_line) {

             $project_lines->Create(
                    [
                      'description' => $budget_line['trade'],
                      'account_number' => $budget_line['account_number'],
                      'value' => $budget_line['budget'],
                      'retainage' => @$bProject['retainage_percentage']
                    ]
              );

        }

        return response()->json(
           [
            'status' => 200,
            'message' => 'Line Items Import Successfully!'
           ]
        );

    }

  public function undoLine(Request $request, $id){
         
         if(Gate::denies('delete')) {
               return abort('401');
          } 

        $project  = Project::find($id);  

        $applications = @$project->applications();  

        $applications_count = @$applications->count();

        $last_application = @$applications->latest()->first();
        
        $changeOrderApplicationsLines  = ChangeOrderApplicationLine::select('id')
                                          ->where('app_no', $applications_count)
                                          ->whereIn('change_order_application_id', 
                                          @$project->changeOrderApplications()->pluck('id')
                                          );                        
  

        if(@$applications->exists() == false){

             return response()->json(
             [
              'status' => 200,
              'error' => true,
              'message' => 'Application can`t be Deleted!'
             ]
          );
        }
        
        $last_application->delete();
        $changeOrderApplicationsLines->delete();

        return response()->json(
           [
            'status' => 200,
            'message' => 'Application Delete Successfully!'
           ]
        );

    }


}
