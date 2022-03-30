<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ITBTracker;
use Gate;


class ITBTrackerController extends Controller
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
         if(Gate::denies('view')) {
               return abort('401');
         } 

         $projects = Project::query();

         $projects = $projects->get(); 

         $projectId = (request()->filled('p')) ? request()->p : $projects->first()->id;

         $project = Project::find($projectId);

         $trades = $project->trades()->get();
           
         $trades = $trades->filter(function($trade) use ($projectId) {
                
                $subcontractors = $trade->subcontractors->filter(function($sc) use ($projectId,$trade){
                           $mail_sent = ITBTracker::where([
                               'project_id' => $projectId, 'trade_id' => $trade->id , 
                               'subcontractors_id' => $sc->id 
                               ])->pluck('mail_sent')->first();

                            $sc->mail_sent = $mail_sent ?? false;

                            return $sc;                           
                });

                return $trade;
         });  

         return view('itb_tracker.index',compact('projects','projectId','trades'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         //
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
     * Update the specified resource in storage.
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


    public function sendMail(Request $request){
     
      (new \App\Jobs\SendEmail())
                ->dispatch();

      return response()->json(
           [
            'status' => 200,
            'message' => 'Sent Successfully!'
           ]
       );

    }
}
