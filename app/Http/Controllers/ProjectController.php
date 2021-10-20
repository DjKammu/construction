<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\Document;
use App\Models\ProjectType;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\Vendor;
use Gate;


class ProjectController extends Controller
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

         if(request()->filled('p')){
            $p = request()->p;
            $projects->whereHas('project_type', function($q) use ($p){
                $q->where('slug', $p);
            });
         } 
         
         $projectTypes = ProjectType::all(); 

         $perPage = request()->filled('per_page') ? request()->per_page : (new Project())->perPage;

         $projects = $projects->paginate($perPage);

         return view('projects.index',compact('projects','projectTypes'));
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

        $projectTypes = ProjectType::all(); 

        return view('projects.create',compact('projectTypes'));
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

        $data = $request->except('_token');

        $request->validate([
              'name' => 'required|unique:projects',
              'project_type_id' => 'required|exists:project_types,id',
              'start_date'    => 'nullable|date',
              'end_date'      => 'nullable|date|after_or_equal:start_date',
              'due_date'      => 'nullable|date|after_or_equal:end_date'
        ]);

        $slug = \Str::slug($request->name);

        $data['photo'] = '';    

        if($request->hasFile('photo')){
               $photo = $request->file('photo');
               $photoName = $slug.'-'.time() . '.' . $photo->getClientOriginalExtension();
              
               $data['photo']  = $request->file('photo')->storeAs(Document::PROJECTS, $photoName, 'public');
        }

        $property = Project::create($data);

        $project_type = $property->project_type;

        $path = public_path().'/'.Document::PROJECT.'/' . $project_type->slug.'/'.$slug;
        \File::makeDirectory($path, $mode = 0777, true, true);

        return redirect('projects')->with('message', 'Project Created Successfully!');
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

         $projectTypes = ProjectType::all();
         $projects = Project::all()->except($id);
         $project = Project::find($id);
         $documentTypes = DocumentType::all();
         $subcontractors = Subcontractor::all();
         $vendors = Vendor::all();
         $documents = $project->documents();
         $trades = $project->trades()->get();

         if(request()->filled('s')){
            $searchTerm = request()->s;
            $documents->where('name', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('slug', 'LIKE', "%{$searchTerm}%");
         }  

         if(request()->filled('document_type')){
                $document_type = request()->document_type;
                $documents->whereHas('document_type', function($q) use ($document_type){
                    $q->where('slug', $document_type);
                });
         }

         if(request()->filled('vendor')){
                $vendor = request()->vendor;
                $documents->where('vendor_id', $vendor);
         } 
        
        if(request()->filled('subcontractor')){
                $subcontractor = request()->subcontractor;
                $documents->where('subcontractor_id', $subcontractor);
         } 
              
         $perPage = request()->filled('per_page') ? request()->per_page : (new Project())->perPage;

         $documents = $documents->with('document_type')
                    ->paginate($perPage);


        $documents->filter(function($doc){

            $project = @$doc->project;

            $project_slug = \Str::slug($project->name);

            $document_type = @$doc->document_type->slug;

            $project_type_slug = @$project->project_type->slug;

            $folderPath = Document::PROJECT."/";

            $project_type_slug = ($project_type_slug) ? $project_type_slug : Document::ARCHIEVED;

            $folderPath .= "$project_type_slug/$project_slug/$document_type/";
            
            $files = $doc->files();

            $file =  ($files->count() == 1) ? $files->pluck('file')->first() : '';

            $doc->file = ($file  ? asset($folderPath.$file) : '') ;

            return $doc->file;
           
         });


         return view('projects.edit',compact('projectTypes','project','documentTypes','documents','subcontractors','vendors','trades','projects'));
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

       $data = $request->except('_token');

       $request->validate([
              'name' => 'required|unique:projects,name,'.$id,
              'project_type_id' => 'required|exists:project_types,id',
              'start_date'    => 'nullable|date',
              'end_date'      => 'nullable|date|after_or_equal:start_date',
              'due_date'      => 'nullable|date|after_or_equal:end_date'
        ]);
     
        $slug = \Str::slug($request->name);
         
        $project = Project::find($id);
        $oldSlug = \Str::slug($project->name);

        if(!$project){
            return redirect()->back();
        }

        $data['photo'] = $project->photo;    


        if($request->hasFile('photo')){
               $photo = $request->file('photo');
               $photoName = $slug.'-'.time() . '.' . $photo->getClientOriginalExtension();
              
               $data['photo']  = $request->file('photo')->storeAs(Document::PROJECTS, $photoName, 'public');
        }
        
        $oldProject_type = ProjectType::find($project->project_type_id);
        $project_type = ProjectType::find($request->project_type_id);

        if(!$oldProject_type){

                $public_path = public_path().'/'.Document::PROJECT.'/';
                $folderPath =  $project_type->slug.'/'.$oldSlug;
                $oldFolderPath = Document::ARCHIEVED.'/'.$oldSlug; 
               \File::copyDirectory($public_path.$oldFolderPath,$public_path.$folderPath); 
               \File::deleteDirectory($public_path.$oldFolderPath);
               
               if($slug  != $oldSlug){
                  $path = public_path().'/'.Document::PROJECT.'/'.@$project_type->slug.'/';
                  @rename($path.$oldSlug, $path.$slug); 
               }

        }
        elseif((@$oldProject_type->id != $request->project_type_id) || 
            ($slug != $oldSlug)){
             
             if($slug  != $oldSlug){
                 $path = public_path().'/'.Document::PROJECT.'/'.@$oldProject_type->slug.'/';
                 @rename($path.$oldSlug, $path.$slug); 
             }


             if(@$oldProject_type->id != $request->project_type_id)
             { 
               $path = public_path().'/'.Document::PROJECT.'/';
               $projectDir  = ($slug  != $oldSlug) ? $slug : $oldSlug;
                \File::copyDirectory($path.@$oldProject_type->slug.'/'.$projectDir,
                 $path.$project_type->slug.'/'.$projectDir); 
               \File::deleteDirectory($path.@$oldProject_type->slug.'/'.$projectDir);
             }
        }

         $project->update($data);

        return redirect('projects')->with('message', 'Project Updated Successfully!');
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

         $project = Project::find($id);
         $project_slug = \Str::slug($project->name);
         $project_type = @$project->project_type;

         $project_type_slug = @$project_type->slug;

         $public_path = public_path().'/';

         $folderPath = Document::PROJECT."/";

         $project_type_slug = ($project_type_slug) ? $project_type_slug : Document::ARCHIEVED; 

         $folderPath .= "$project_type_slug/$project_slug";

         $path = $public_path.'/'.$folderPath;
          
         $aPath = public_path().'/'.Document::PROJECT.'/'.Document::ARCHIEVED.'/'.Document::PROJECTS; 
         
         @\File::makeDirectory($aPath, $mode = 0777, true, true);

         @\File::copyDirectory($path, $aPath.'/'.$project_slug);

         @\File::deleteDirectory($path);

         $project->delete();

        return redirect()->back()->with('message', 'Project Delete Successfully!');
    }
}
