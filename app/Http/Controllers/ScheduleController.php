<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Schedule;
use App\Models\Project;
use App\Models\ProjectType;
use Gate;

class ScheduleController extends Controller
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
    
	public function index($id){

     	if(Gate::denies('view')) {
               return abort('401');
         } 

         $project = Project::find($id);


        if(!$project){
            return redirect()->back();
        }

        $projectTypes = ProjectType::orderBy('name')->get(); 

         $projects = Project::has('gantt_lines')->orderBy('name');

         if(request()->filled('project_type')){
            $p = request()->project_type;
            $projects->whereHas('project_type', function($q) use ($p){
                $q->where('slug', $p);
            });
         } 

         $projects = $projects->get()->except($id);

         return view('projects.schedule.index',compact('projects','project','projectTypes'));

	}
	public function data(Request $request,$id){
  		
  		if(Gate::denies('view')) {
                 return abort('401');
        } 

        $events = new Schedule();

        $from = $request->from;
        $to = $request->to;

        return response()->json([
            "data" => $events->
                where("start_date", "<", $to)->
                where("end_date", ">=", $from)->
                whereProjectId($id)->get()
        ]);
  	}

    public function store(Request $request,$id){
   
        $event = new Schedule();
     
        $event->text = strip_tags($request->text);
        $event->start_date = $request->start_date;
        $event->end_date = $request->end_date;
        $event->rec_type = $request->rec_type;
        $event->event_length = $request->event_length;
        $event->event_pid = $request->event_pid;
        $event->project_id = $id;
        $event->save();
     
        $status = "inserted";
        if($event->rec_type == "none"){
            $status = "deleted";
        }
     
        return response()->json([
            "action"=> $status,
            "tid" => $event->id
        ]);
    }

    private function deleteRelated($event){
        if($event->event_pid && $event->event_pid !== "none"){
          Schedule::where("event_pid", $event->id)->delete();
        }
    }
       
    public function update($pid, $id, Request $request){
            $event = Schedule::find($id);
     
            $event->text = strip_tags($request->text);
            $event->start_date = $request->start_date;
            $event->end_date = $request->end_date;
            $event->rec_type = $request->rec_type;
            $event->event_length = $request->event_length;
            $event->event_pid = $request->event_pid;
            $event->save();
            $this->deleteRelated($event); 
            return response()->json([
            "action"=> "updated"
        ]);
    }
     
    public function destroy($pid, $id){
        $event = Schedule::find($id);
     
        // delete the modified instance of the recurring series
        if($event->event_pid){
            $event->rec_type = "none";
            $event->save();
        }else{
            // delete a regular instance
            $event->delete();
        }
        $this->deleteRelated($event);
        return response()->json([
              "action"=> "deleted"
        ]);
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

        $gantt_lines = $project->gantt_lines()->get();

        //dd($gantt_lines);


         $arr = $oldNewArr = [];

         $entryArr = [];

        foreach ($gantt_lines as $key => $value) {
           
           $where = ['text' => $value['text'],'sortorder' => $value['sortorder']];

           $update = ['text' => $value['text'],
                      'duration' => $value['duration'],
                      'progress' => $value['progress'],
                      'start_date' => $value['start_date'],
                      'sortorder' => $value['sortorder'],
                      'type'      => $value['type'],
                      'parent' => 0,
                      'project_id' => $id 
            ];

             $cu = $assignToproject->gantt_lines()->UpdateOrCreate($where,$update);

              $arr[] =  [
                  'id'      => $value['id'],
                  'parent'  => $value['parent'],
                  'new'     => $cu->id
               ];
              $oldNewArr[$value['id']] = $cu->id; 
        }

          $entryArr = [
               'entries' => @$arr,
               'oldNewPair' => @$oldNewArr,
               'project_id' => @$id
           ];

         $this->updateParent($entryArr);

        return redirect()->back()->with('Budget Lines added Successfully!');

    }


    public function updateParent($entryArr){
         
        if(count($entryArr['entries'] ) > 0 && count($entryArr['oldNewPair'] ) > 0 ){
           foreach (@$entryArr['entries'] as $key => $entry) {
                 if($entry['parent'] > 0){
                       $task = Task::find($entry['new']);
                       $parent = @$entryArr['oldNewPair'][$entry['parent']] ?? 0;
                       $task->parent = $parent;
                       $task->save();
               $this->updateLink($entry['parent'],$entryArr['oldNewPair'],$entryArr['project_id']);
               // $this->updateTargetLink($entry['parent'],$entryArr['oldNewPair'],$entryArr['project_id']);
              }
            }
        } 
        
    }

    public function updateLink($id, $oldNewPair,$project_id){     
        if($id > 0 && count($oldNewPair ) > 0 ){
                       $links = Link::where('source',$id)
                                ->orWhere('target',$id)->get();
                       foreach ($links as $key => $lk) {
                           if(@$oldNewPair[$lk['source']] && @$oldNewPair[$lk['target']]){
                             $where = ['source' => @$oldNewPair[$lk['source']],'target' => @$oldNewPair[$lk['target']]];

                             $update = [
                                        'project_id' => $project_id,
                                        'type' => $lk['type'],
                                        'source' => @$oldNewPair[$lk['source']],
                                        'target' =>@$oldNewPair[$lk['target']]
                              ];
                              $link = new Link();
                              $link->UpdateOrCreate($where,$update);
 
                                // $link = new Link();
                                // $link->project_id = $project_id;
                                // $link->type  = $lk['type'];
                                // $link->source = @$oldNewPair[$lk['source']];
                                // $link->target = @$oldNewPair[$lk['target']];
                                // $link->save();
                           }
                       }
        } 
        
    }

}