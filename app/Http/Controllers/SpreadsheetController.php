<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Spreadsheet;
use App\Models\Project;
use App\Models\ProjectType;
use Gate;

class SpreadsheetController extends Controller
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

          $spreadsheet = new Spreadsheet();

     
          $spreadsheet = ($spreadsheet->whereProjectId($id)->exists() ) ? $spreadsheet->whereProjectId($id)->pluck('state')->first() : [];
      
           // dd($spreadsheet);
          if($spreadsheet){

            $spreadsheet = ($spreadsheet) ? (@json_decode($spreadsheet)) : $spreadsheet;

            @$spreadsheet->sheets = @collect($spreadsheet->sheets)->filter(function($sheet) {
              $sheet->rows = [];
              $sheet->cols = [];
               return $sheet;
           });

          }

          $spreadsheet = (!empty($spreadsheet))  ?   $spreadsheet : [];

       

        
         return view('projects.spreadsheet.index',compact('projects','project','projectTypes','spreadsheet'));

	}

	public function data(Request $request,$id){
  		
  		if(Gate::denies('view')) {
                 return abort('401');
        } 

        $spreadsheet = new Spreadsheet();

        return response()->json([
            "data" => ($spreadsheet->whereProjectId($id)->exists() ) ? $spreadsheet->whereProjectId($id)->first()->pluck('state') : []
        ]);
  	}

    public function store(Request $request,$id){
        
         if(Gate::denies('add')) {
               return abort('401');
          } 

        $project  = Project::find($id);
        $spreadsheet = @$project->spreadsheet ?: new Spreadsheet;
        // $spreadsheet->state = @json_encode($state);
        $state= @str_replace(['\n','\r'] , " ",$request->state);  
        $spreadsheet->state = @str_replace("'", "`",$state);
        // dd($spreadsheet->state);
        $project->spreadsheet()->save($spreadsheet);
        $project->save();
     
        return response()->json([
            "message"=> 'Saved Successfully!'
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

             $cu = $assignToproject->spreadsheet()->UpdateOrCreate($where,$update);

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

         // $this->updateParent($entryArr);

        return redirect()->back()->with('Budget Lines added Successfully!');

    }

}