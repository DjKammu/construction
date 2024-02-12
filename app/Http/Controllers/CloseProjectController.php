<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Application;
use App\Models\ChangeOrderApplication;
use Gate;
use Carbon\Carbon;
use PDF;

class CloseProjectController extends Controller
{
    const APPLICATION = 'application';

    const CONTINUATIONSHEET = 'continuation-sheet';

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
    public function index(Request $request, $id)
    {
        $project  = Project::find($id);  

        $changeOrderApplications = $project->changeOrderApplications()->get();

       return response()->json(
            ['status' => 200,'data' => $changeOrderApplications]
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

        $closeProject = $project->closeProject()->exists();
           
        if($closeProject){
            return redirect()->back();
        }

        $changeOrdersTotal = @$project->changeOrderApplications()->sum('value');

        $retainage_value = @(float) $project['original_amount'] + @(float) $changeOrdersTotal;                      
         return view('projects.aia.close-project',compact('project','retainage_value'));
    }  



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        // if(Gate::denies('add')) {
        //      return abort('401');
        // } 
        
        $project  = Project::find($id);  

        $application_id = null;
         
        $msg = 'Project Closed Successfully!';

        $closeProject = $project->closeProject();

        $data = $request->all();

        if($closeProject->exists()){

             return response()->json(
             [
              'status' => 200,
              'error' => true,
              'message' => 'Project Already Closed!'
             ]
          );
        }

        $closeProject->Create(
                    [
                      'application_date' => $data['application_date'],
                      'retainage_value' => $data['retainage_value'],
                      'period_to' => $data['period_to']
                    ]
        );

        $project->update(['status' => Project::FINISHED_STATUS]);

        return response()->json(
           [
            'status' => 200,
            'message' => $msg
           ]
        );
        
    }  

    public function undo(Request $request, $id)
    {
        // if(Gate::denies('add')) {
        //      return abort('401');
        // } 
        
        $project  = Project::find($id);  

        $application_id = null;
         
        $msg = 'Fianl Undo Successfully!';

        $closeProject = $project->closeProject();

        $data = $request->all();

        if(!$closeProject->exists()){

             return response()->json(
             [
              'status' => 200,
              'error' => true,
              'message' => 'Closed Project Not exists!'
             ]
          );
        }

        $closeProject->delete();

        $project->update(['status' => '']);

        return response()->json(
           [
            'status' => 200,
            'message' => $msg
           ]
        );
        
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

        $changeOrderApplication = ChangeOrderApplication::find($id);
        
        $applications = @$changeOrderApplication->application_lines()->exists();

        if($applications){

             return response()->json(
             [
              'status' => 200,
              'error' => true,
              'message' => 'Change Order can`t be Deleted!'
             ]
          );
        }

        $changeOrderApplication->delete();

        return response()->json(
           [
            'status' => 200,
            'message' => 'Change Order Delete Successfully!'
           ]
        );
    }


}
