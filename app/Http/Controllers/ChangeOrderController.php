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

class ChangeOrderController extends Controller
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

         $applications_count = $project->applications()->count();

        return view('projects.aia.change-orders',compact('project','applications_count'));
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

        $edit =  $request->input('edit');

        $application_id = null;
         
        $msg = 'Change Order added Successfully!';

        if($edit){
          $application_id =  $request->input('application_id');
          $msg = 'Change Order updated Successfully!';
        }

        $this->saveChangeOrder($request,$project,$application_id);

        return response()->json(
           [
            'status' => 200,
            'message' => $msg
           ]
        );
        
    }

    public function saveChangeOrder(Request $request,$project){

        $changeOrderApplications = $project->changeOrderApplications();

        $data = $request->data;
        $revised = $request->revised;

        $changeOrderApplications->UpdateOrCreate(
                     ['id' => @$data['id']],
                    [
                      'account_number' => $data['account_number'],
                      'description' => $data['description'],
                      'retainage' => $data['retainage'],
                      'value' => $data['value'],
                      'app' => $data['app'],
                      'revised' => (int) $revised
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
