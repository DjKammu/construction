<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Application;
use App\Models\ApplicationLine;
use App\Models\ChangeOrderApplicationLine;
use Gate;
use Carbon\Carbon;
use PDF;

class ProjectApplicationController extends Controller
{
    const APPLICATION = 'application';

    const APPLICATION_CLOSE_PROJECT = 'application-cp';

    const CONTINUATIONSHEET  = 'continuation-sheet';

    const CONTINUATIONSHEET_CLOSE_PROJECT  = 'continuation-sheet-cp';

    const CHANGE_ORDERS  = 'change-order';

    const CHANGE_ORDERS_CLOSE_PROJECT  = 'change-order-cp';

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
        $edit = $request->edit;

        $applications = $this->getApplication($id,$edit);

       return response()->json(
           array_merge(
            ['status' => 200],
            $applications)
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
      
        return view('projects.aia.applications',compact('project','application_id'));
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
         
        $msg = 'Application added Successfully!';

        if($edit){
          $application_id =  $request->input('application_id');
          $msg = 'Application updated Successfully!';
        }
         
        $this->saveApplications($request,$id,$application_id);
        $this->saveChangeOrderApplications($request,$id);

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
          if(@!$dt['project_line_id']){
              $dt['project_line_id'] =  $dt['id'];
          }
          return $dt;
        }); 

        $application_lines = $application->application_lines()->createMany($data);
       }


    }

     public function saveChangeOrderApplications(Request $request,$project_id,$application_id = null){

        $change_orders = $request->change_orders;
        $app_no = $request->app_no;

       foreach ($change_orders as $key => $dt) {

             $update = [
                 'billed_to_date' => $dt['billed_to_date'],
                 'stored_to_date' => $dt['stored_to_date'],
                 'work_completed' => $dt['work_completed'],
                 'materials_stored' => $dt['materials_stored'],
                 'change_order_application_id' => $dt['id'],
                 'app_no' => $app_no
             ];

              ChangeOrderApplicationLine::UpdateOrCreate(
                     ['id' => @$dt['line_id']],
                     $update
              );   
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


    public function  getSummary($project, $app_id = null){

      $applications = $project->applications();
      
      if($app_id){
         $applications = $applications->where('id', '<=',$app_id);
      }

      $applications = $applications->get();

      $totalStored = 0;
      $retainageToDate = 0;
      $totalEarned = 0;
      $changeOrdertotalEarned = 0;
      $currentDuePayment = 0;
      $closeProject = false;
     
      foreach (@$applications as $ak => $application) {
           $lines = $application->application_lines()
                      ->get();
           $currentDuePayment = 0;
           $closeProject = true;

           foreach (@$lines as $key => $line) {

                    $total = (float) $line['work_completed'] + (float) $line['materials_stored'];

                    $totalStored = $totalStored + $total;

                    $retainage = $line->project_line->retainage;
                    $retainageToDate = $retainageToDate + (float) ($total * $retainage/100);

                    $totalEarned =  (float) $totalStored -  (float) ($retainageToDate);

                    if(count($applications) == $ak+1){
                        $currentDuePayment = $currentDuePayment + $total - (float) ($total * $retainage/100);

                        $totalBilled = (float) $line['work_completed'] + (float) $line['billed_to_date'];
                        $percentage = @number_format(@$totalBilled / @$line->project_line->value*100, 1);

                      if( ($percentage < 100) && ($closeProject)){
                          $closeProject = false;
                      }
                    }
            } 
            $currentDuePayment =  (float) $currentDuePayment;                     
       }

      $applicationsCount = @$applications->count(); 

      $changeOrderApplications = $project->changeOrderApplications()
                                   ->where('app', '<=',($applicationsCount != 0) ? $applicationsCount : 1)->get();                        

      $changeOrdercloseProject = true;

      $changeOrdersTotal = 0; 
      $changeOrdercurrentDue = 0;

      foreach (@$changeOrderApplications as $ck => $changeOrder) {

           $changeOrdersTotal = $changeOrdersTotal + $changeOrder->value;

           $changeOrderlines = $changeOrder->application_lines()
                                ->where('app_no','<=',$applicationsCount)->get();
                                                      
          $changeOrdercloseProject = false;

          foreach (@$changeOrderlines as $k => $cLine) {

                    $total = (float) $cLine['work_completed'] + (float) $cLine['materials_stored'];

                    $totalStored = $totalStored + $total;

                    $retainage = $changeOrder->retainage;

                    $retainageToDate = $retainageToDate + (float) ($total * $retainage/100);

                    $totalEarned =  (float) $totalStored -  (float) ($retainageToDate);
                    
                    if(@count($changeOrderlines) == $k+1){


                       $changeOrdercurrentDue =  $changeOrdercurrentDue + $total - (float) ($total * @$retainage/100);

                        $totalBilled = (float) $cLine['work_completed'] + (float) $cLine['billed_to_date'];
                        $percentage = number_format($totalBilled / $changeOrder->value*100, 1);

                      if( ((float) $percentage >= 100) && (!$changeOrdercloseProject)){
                          $changeOrdercloseProject = true;
                      }
                    }
            }
      }

       $changeOrdercurrentDue =  (float) $changeOrdercurrentDue;
    
       $currentDuePayment =  (float) $currentDuePayment + (float) $changeOrdercurrentDue;  
    
       $lastApplicationsPayments = (@$applications->count() > 1) ? $totalEarned - $currentDuePayment 
       : 0;
        
       $isProjectClosed = @$project->closeProject()->first();  

       $data['applicationsCount'] = @$applicationsCount;
       $data['lastApplicationsPayments'] = (float) $lastApplicationsPayments;
       $data['currentDuePayment'] = (float) $currentDuePayment;
       $data['changeOrdersTotal'] = (float) $changeOrdersTotal;
       $data['retainageToDate'] = (float) $retainageToDate;
       $data['totalStored'] = (float) $totalStored;
       $data['totalEarned'] = (float) $totalEarned;
       $data['isProjectClosed'] = $isProjectClosed;
       $data['closeProject'] =  ($closeProject &&  $changeOrdercloseProject ) ? $closeProject : false;

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
        
        if(Gate::denies('add')) {
               return abort('401');
        }

        $project  = Project::find($id);  
         
        if(!$project){
            return redirect()->back();
        }

        $application_id = $project->applications()->latest()->first();

        $application_id = @$application_id->id;

        $edit = true;
      
        return view('projects.aia.applications',compact('project','application_id','edit'));
    }
    
    public function getApplication($id,$edit = false){
    
       $project  = Project::find($id);  
      
       $app = $project->applications();

       $application = $app->latest()->first();

       $applications_count = $app->count();

       $orderBy = 'created_at';  
       $order ='DESC' ;

    

       if(!$application){
            $applications = $project->project_lines()
            // ->with('project_line')
           // ->join('project_lines', 'application_lines.project_line_id', '=', 'project_lines.id')
           ->orderBy('project_lines.account_number')->get();
            // ->orderBy($orderBy, $order)->get();

               $applications->filter(function($app) use ($edit){
                  $app->billed_to_date = 0;
                  $app->stored_to_date = 0;
                  $app->work_completed = 0;
                  $app->materials_stored = 0;
            }); 

       } 
       else{


            $applications = $application->application_lines()
                      ->select('*', 'application_lines.id as id')
                      ->with('project_line')
                      ->join('project_lines', 'application_lines.project_line_id', '=', 'project_lines.id')
                      ->orderBy('project_lines.account_number')->get();       

             $applications->filter(function($app) use ($edit){

                $app->account_number = $app->project_line->account_number;
                $app->description = $app->project_line->description;
                $app->value = $app->project_line->value;
                $total = (float) $app->work_completed + (float) $app->billed_to_date;
                $app->total_percentage = number_format($total/ $app->value*100, 1);

                 if($edit == false){
                  $app->billed_to_date = $total;
                  $app->stored_to_date = (float) $app->materials_stored + (float) $app->stored_to_date;
                  $app->work_completed = 0;
                  $app->materials_stored = 0;
                }

            }); 
                      
           if($edit == false){
            $application->application_date = '';
            $application->period_to = '';
           }

       }

       $changeOrders = $this->getChangeOrders($project, $edit);
       
        return  [ 'data' => $applications,
                  'applications_count' => ($edit) ? @$applications_count : 
                  @$applications_count +1 ,
                  'change_orders' => $changeOrders,
                  'application_date' => @$application->application_date,
                  'period_to' => @$application->period_to
                ];
   

    }

    public function getChangeOrders($project, $edit = false){
      
       $changeOrderApplications = $project->changeOrderApplications()
                                   ->orderBy('account_number')->get();
       $orderBy = 'created_at';  
       $order ='DESC' ; 

       if($changeOrderApplications->count() == 0){
          return [];
       }

       if(@!$changeOrderApplications->first()->has('application_lines')->exists()){

               $changeOrderApplications->filter(function($app) use ($edit){
                  $app->billed_to_date = 0;
                  $app->stored_to_date = 0;
                  $app->work_completed = 0;
                  $app->materials_stored = 0;
            }); 
       } 
       else{
                 
             $changeOrderApplications->filter(function($changeOrder) use ($edit){

                 $line = $changeOrder->application_lines()
                      ->latest()->first(); 

                $changeOrder->description = $changeOrder->description;
                $changeOrder->value = $changeOrder->value;
                $total = (float) @$line->work_completed + (float) @$line->billed_to_date;
                $changeOrder->total_percentage = number_format($total/ $changeOrder->value*100, 1);
                $changeOrder->billed_to_date =  @$line->billed_to_date ?? 0;
                $changeOrder->stored_to_date =  @$line->stored_to_date ?? 0;
                $changeOrder->work_completed =  @$line->work_completed ?? 0;
                $changeOrder->materials_stored = @$line->materials_stored ?? 0;

                 if($edit == false){
                   $changeOrder->billed_to_date = $total;
                   $changeOrder->stored_to_date = (float) $changeOrder->materials_stored + (float) $changeOrder->stored_to_date;
                   $changeOrder->work_completed = 0;
                   $changeOrder->materials_stored = 0;
                 }else{
                     $changeOrder->line_id = @$line->id;
                 }     
            }); 
                   
       }

       return $changeOrderApplications;

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
      //
    }

    public function allApplications($id){
    
        $project  = Project::find($id);  
      
        $applications = $project->applications()->latest()->get();

        $applications->filter(function ($application, $key) use ($project,$applications) {
             $changeOrderApplications = $project->changeOrderApplications()
                                       ->where('app','<=', ($applications->count() - $key))->exists();
            return $application['has_change_order'] = $changeOrderApplications;   
      
        });

        return response()->json(
            ['status' => 200, 'data' => $applications]
       );

    }

    public function generatePDF($id, $to, $app_id){
        
         if(Gate::denies('view')) {
               return abort('401');
         }

        $project  = Project::find($id); 

        $data = [];
     
        if($to == self::APPLICATION  || $to == self::CONTINUATIONSHEET || $to == self::CHANGE_ORDERS){

          $application = $project->applications()
                       ->where('id',$app_id)->first();

          $lines = $application->application_lines()
                    ->with('project_line')
                   ->join('project_lines', 'application_lines.project_line_id', '=', 'project_lines.id')
                   ->orderBy('project_lines.account_number')
                   ->get();

          $summary = $this->getSummary($project,$app_id);
        
          $data = array_merge([
                   'application' => $application,
                   'lines' => $lines,
                   'project' => $project
                  ],$summary);

        }elseif ($to == self::APPLICATION_CLOSE_PROJECT) {

           $application = $project->closeProject()->first();

           $lastApplication = $project->applications()
                       ->latest()->first();
       
           $summary = $this->getSummary($project,$lastApplication->id);

           $summary['lastApplicationsPayments'] = (float) $summary['lastApplicationsPayments'] + (float) $summary['currentDuePayment'];

           $summary['currentDuePayment'] = 0.0;

           $summary['applicationsCount'] = $summary['applicationsCount'] + 1;

           $data = array_merge([
                   'application' => $application,
                   'project' => $project
                  ],$summary);
        }  
        
        elseif ($to == self::CONTINUATIONSHEET_CLOSE_PROJECT) {
            $application = $project->closeProject()->first();

            $lastApplication = $project->applications()
                       ->latest()->first();       

           $lines = $lastApplication->application_lines()
                   ->with('project_line')
                   ->join('project_lines', 'application_lines.project_line_id', '=', 'project_lines.id')
                   ->orderBy('project_lines.account_number')
                     ->get(); 

           $summary = $this->getSummary($project,$lastApplication->id);

           $summary['lastApplicationsPayments'] = (float) $summary['lastApplicationsPayments'] + (float) $summary['currentDuePayment'];

           $summary['currentDuePayment'] = 0.0;

           $summary['applicationsCount'] = $summary['applicationsCount'] + 1;

           $data = array_merge([
                   'application' => $application,
                   'lines' => $lines,
                   'project' => $project
                  ],$summary);
        }   

        elseif ($to == self::CHANGE_ORDERS_CLOSE_PROJECT) {
            $application = $project->closeProject()->first();

            $lastApplication = $project->applications()
                       ->latest()->first();       

           $summary = $this->getSummary($project,$lastApplication->id);

           $summary['lastApplicationsPayments'] = (float) $summary['lastApplicationsPayments'] + (float) $summary['currentDuePayment'];

           $summary['currentDuePayment'] = 0.0;

           $summary['applicationsCount'] = $summary['applicationsCount'] + 1;

           $data = array_merge([
                   'application' => $application,
                   'project' => $project
                  ],$summary);
        }      

          // return View('projects.aia.'. $to .'-pdf',
          //   $data
          // );

       // dd($data); 
        
        $pdf = PDF::loadView('projects.aia.'. $to .'-pdf',
          $data
        )->setOptions(['margin' => 5]);

        $pdf->setPaper('a4', 'landscape');  

        $slug = \Str::slug($project->name);

        return $pdf->stream('project_'.$slug .'_budget.pdf');
        return $pdf->download('project_'.$slug.'_budget.pdf');
    }

}
