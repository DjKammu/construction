<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Link;
use Gate;

class TaskController extends Controller
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

	public function store(Request $request, $id){

		if(Gate::denies('add')) {
               return abort('401');
        } 
       
		$task = new Task();

		$task->text = $request->text;
		$task->start_date = $request->start_date;
		$task->duration = $request->duration;
		$task->project_id = $id;
		$task->progress = $request->has("progress") ? $request->progress : 0;
		$task->parent = $request->parent;
		$task->type = $request->type;
		$task->sortorder = Task::max("sortorder") + 1;;

		$task->save();

		return response()->json([
			"action" => "inserted",
			"tid" => $task->id
		]);
	}

    public function update($pid, $id, Request $request){
		$task = Task::find($id);	
		$task->text = $request->text;
		$task->start_date = $request->start_date;
		$task->duration = $request->duration;
		$task->progress = $request->has("progress") ? $request->progress : 0;
		$task->parent = $request->parent;
		$task->type = $request->type;

		if($request->has("target")){
			$this->updateOrder($id, $request->target);
		}  
		$task->save();

		return response()->json([
			"action" => "updated"
		]);
	}
      

     private function updateOrder($taskId, $target){
		$nextTask = false;
		$targetId = $target;

		if(strpos($target, "next:") === 0){
			$targetId = substr($target, strlen("next:"));
			$nextTask = true;
		}

		if($targetId == "null")
			return;

		$targetOrder = Task::find($targetId)->sortorder;
		if($nextTask)
			$targetOrder++;

		Task::where("sortorder", ">=", $targetOrder)->increment("sortorder");

		$updatedTask = Task::find($taskId);
		$updatedTask->sortorder = $targetOrder;
		$updatedTask->save();
	}

     public function destroy($pid,$id){
		$task = Task::find($id);
        Link::where('target', $id)->orWhere('source',$id)->delete(); 
		$task->delete();
		return response()->json([
			"action" => "deleted"
		]);
	}

}