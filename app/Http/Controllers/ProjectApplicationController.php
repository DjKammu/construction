<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Application;
use App\Models\ApplicationLine;
use Gate;
use Carbon\Carbon;
use PDF;

class ProjectApplicationController extends Controller
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
      
       $application = $project->applications()->latest()->first();

       $orderBy = 'created_at';  
       $order ='DESC' ;

       if(!$application){
            $applications = $project->project_lines()
            ->orderBy($orderBy, $order)->get();
       }     
       else{

          $applications = $application->application_lines()
                      ->orderBy($orderBy, $order)->get();

          $applications->filter(function($app){
              $app->description = $app->project_line->description;
              $app->value = $app->project_line->value;
              $app->total_percentage = number_format($app->work_completed/ $app->value*100, 1);
          });               

       }
    
       return response()->json(
           [
            'status' => 200,
            'data' => $applications,
            'application_date' => @$application->application_date,
            'period_to' => @$application->period_to
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

        $application_id = $project->applications()->latest()->first();

        $application_id = @$application_id->id;

        // if(!$project->retainage_percentage || !$project->original_amount){
        //    return redirect()->back()->with('error' ,'Retainage Percentage or Original Amount is not available!');
        // }
      
        return view('projects.includes.applications',compact('project','application_id'));
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

        $application_id =  $request->input('application_id');
         
        $msg = 'Application added Successfully!';

        if($application_id){
          $msg = 'Application updated Successfully!';
        }

        $this->saveApplications($request,$id,$application_id);

        return response()->json(
           [
            'status' => 200,
            'message' => $msg
           ]
        );
        


    }

    public function saveApplications(Request $request,$project_id,$application_id = null){


        $application_date =  $request->input('application_date');

        $period_to =  $request->input('period_to');

        $application = Application::UpdateOrCreate(
                     ['id' => $application_id],
                    //['id' => 1],
                    [
                      'project_id' => $project_id,
                      'application_date' => $application_date,
                      'period_to' => $period_to
                    ]
              );

        $data = $request->data;
       

       if($application_id){

       foreach ($data as $key => $dt) {
             $update = [
                 'billed_to_date' => $dt['billed_to_date'],
                 'stored_to_date' => $dt['stored_to_date'],
                 'work_completed' => $dt['work_completed'],
                 'materials_stored' => $dt['materials_stored'],
             ];
             ApplicationLine::whereId($dt['id'])
                ->update($update);
       }
    
       }
       else{
         
        $data = collect($data)->map(function($dt){
          $dt['project_line_id'] = $dt['id'];
          return $dt;
        }); 
        $application_lines = $application->application_lines()->createMany($data);
       }


    }
     
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function summary($id)
    {
      $project  = Project::find($id);  
      
                  
       $data = $this->getSummary($project);

       return response()->json(
           [
            'status' => 200,
            'data' => $data
           ]
       );
    }


    public function  getSummary($project){

      $applications = $project->applications()->get();

      $totalStored = 0;
      $retainageToDate = 0;
      $totalEarned = 0;
     
      foreach (@$applications as $key => $application) {
           $lines = $application->application_lines()
                      ->get();
            $currentDuePayment = 0;
           foreach (@$lines as $key => $line) {

                    $totalStored = $totalStored + (float) $line['work_completed'];

                    $retainage = $line->project_line->retainage;
                    $retainageToDate = $retainageToDate + (float) ($line['work_completed'] * $retainage/100);

                    $totalEarned =  (float) $totalStored -  (float) ($retainageToDate);
                  
            } 
            $currentDuePayment =  (float) $totalEarned;          
                     
       }
       
       $data['currentDuePayment'] = (float) $currentDuePayment;
       $data['retainageToDate'] = (float) $retainageToDate;
       $data['totalStored'] = (float) $totalStored;
       $data['totalEarned'] = (float) $totalEarned;

       return  $data;

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

        ProjectLine::find($id)->delete();

        return response()->json(
           [
            'status' => 200,
            'message' => 'Project Lines Delete Successfully!'
           ]
        );
    }


}
