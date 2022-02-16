<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\Document;
use App\Models\ProjectType;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\Proposal;
use App\Models\Category;
use App\Models\Vendor;
use Gate;
use PDF;


class ReportController extends Controller
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
    public function index(Request $request)
    {
         if(Gate::denies('view')) {
               return abort('401');
         } 

         $projects = Project::query();

         if(request()->filled('s')){
            $searchTerm = request()->s;
            $projects->where('name', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('address', 'LIKE', "%{$searchTerm}%")
            ->orWhere('city', 'LIKE', "%{$searchTerm}%")
            ->orWhere('state', 'LIKE', "%{$searchTerm}%")
            ->orWhere('country', 'LIKE', "%{$searchTerm}%")
            ->orWhere('zip_code', 'LIKE', "%{$searchTerm}%")
            ->orWhere('notes', 'LIKE', "%{$searchTerm}%");
         }  
          
          $categories = $trades = $project = $payments = $project_subcontractors = [];
         
        
        if(request()->filled('pt')){
            $pt = request()->pt;
            $projects->whereHas('project_type', function($q) use ($pt){
                $q->where('slug', $pt);
            });
         } 
          
        if(request()->filled('p')){
            $p = request()->p;
            $sc = request()->filled('sc') ? request()->sc : null;
            @extract($this->getreportDetails($p,$sc));

            $proposalsQry = @$project->proposals()->IsAwarded();

            $proposals = @$proposalsQry->get();

            $project_subcontractors = @$proposals->map(function($prpsl){
                     return $prpsl->subcontractor;
            })->unique();

            // $project_trades = $proposals->map(function($prpsl){
            //      return $prpsl->trade;
            // });

        }


         $perPage = request()->filled('per_page') ? request()->per_page : (new Project())->perPage;

         $projects = $projects->paginate($perPage);

         $projectTypes = ProjectType::all(); 

         return view('reports.index',compact('projects','projectTypes','categories','trades','project','project_subcontractors','payments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(Gate::denies('add')) {
               return abort('401');
         } 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
          if(Gate::denies('add')) {
               return abort('401');
        } 

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
    }
   
    public function getreportDetails($id, $sc){

        $project = Project::find($id); 
        $trades = $project->trades()->get();
        $catids = @($trades->pluck('category_id'))->unique();
        $categories = Category::whereIn('id',$catids)->get();
        $payments = [];

        if($sc){
            $payments = $project->payments();
            $payments->where('subcontractor_id', $sc);
            $payments = $payments->get();
         }
     
        return compact('project','trades','categories','payments');

    }

    public function getReport($id,$type){
        
        $data = @extract($this->getreportDetails($id));

       // return View('reports.'.$type.'-pdf',
       //    ['categories' => $categories,
       //    'trades' => $trades,'project' => $project]
       //  );

        $pdf = PDF::loadView('reports.'.$type.'-pdf',
          ['categories' => $categories,
          'trades' => $trades,'project' => $project]
        );

        $slug = \Str::slug($project->name);

        // return $pdf->stream($type.'_'.$slug .'.pdf');
        return $pdf->download($type.'_'.$slug.'.pdf');

    }

}
