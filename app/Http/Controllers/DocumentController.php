<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\DocumentFile;
use App\Models\ProjectType;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\Document;
use App\Models\Proposal;
use App\Models\Vendor;
use Gate;


class DocumentController extends Controller
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

         $projects = $projects->paginate((new Project())->perPage);

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

         $id = request()->id;
        
        $project = Project::find($id); 
        $documentsTypes = DocumentType::all(); 
        $subcontractors = Subcontractor::all();
        $vendors = Vendor::all();

        return view('projects.includes.documents-create',compact('project','documentsTypes','subcontractors','vendors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
          if(Gate::denies('add')) {
               return abort('401');
        } 

        $data = $request->except('_token');

        $request->validate([
              'name' => [
                    'required',
                     Rule::unique('documents')->where(function ($query) use($id) {
                        return $query->where('project_id', $id);
                    }),
                ],
              'document_type_id' => 'required|exists:document_types,id',
              // 'file' => 'nullable|sometimes|mimes:pdf,doc,docx,jpeg,jpg,png,csv,xlsx,xls'
        ]);

         $slug = \Str::slug($request->name);

         $data['file'] = '';    
       
         $project = Project::find($id);

         if(!$project){
            return redirect()->back();
        }
        
        $document_type = DocumentType::find($request->document_type_id);

        $project_slug = \Str::slug($project->name);

        $project_type = @$project->project_type;

        $project_type_slug = @$project_type->slug; 

        $document_type_slug = $document_type->slug;

        $public_path = public_path().'/';

        $folderPath = Document::PROJECT."/";

        $project_type_slug = ($project_type_slug) ? $project_type_slug : Document::ARCHIEVED;

        $folderPath .= $project_type_slug.'/'.$project_slug.'/'.$document_type_slug;


        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        $data['slug'] = $slug;
        
        $document = $project->documents()->create($data);

        if($request->hasFile('file')){
               $filesArr = [];
               $files = $request->file('file');
               $name = $request->name;
               $date = $request->filled('date')  ? ( $request->date) : 0;
               $month = $request->filled('month')  ?  $request->month : 0;
               $year = $request->filled('year')  ?  $request->year : date('Y');

               foreach ($files as $key => $file) {
                  $fileName = \Str::slug($name).'-'.time().$key.'.'. $file->getClientOriginalExtension();
                  $file->storeAs($folderPath, $fileName, 'doc_upload');
                  $filesArr[]  = ['file' => $fileName,'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];
               }
                $document->files()->createMany($filesArr);
        }

        return redirect(route('projects.show',['project' => $id]).'#documents')->with('message', 'Document Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$doc)
    {
          if(Gate::denies('edit')) {
               return abort('401');
          } 

        $document = Document::with('files')->find($doc);

        $documentsTypes = DocumentType::all(); 

        $project = @$document->project;
        
        $subcontractors = Subcontractor::all();

        $vendors = Vendor::all();
        
        $project_slug = \Str::slug($project->name);

        $document_type = $document->document_type()->pluck('slug')->first();

        $project_type_slug = @$project->project_type->slug;

        $folderPath = Document::PROJECT."/";

        $project_type_slug = ($project_type_slug) ? $project_type_slug : Document::ARCHIEVED;  
        $folderPath .= "$project_type_slug/$project_slug/$document_type/";

        if($document->proposal_id){
               $proposal = Proposal::find($document->proposal_id);
               $trade_slug = @\Str::slug($proposal->trade->name);
               $folderPath = Document::PROPOSALS."/";
               $folderPath .= "$project_slug/$trade_slug/";
          }

        $document->files->filter(function($file) use ($folderPath){

          $file->file = ($folderPath.$file->file);

           return $file->file;
         
       });

      return view('projects.includes.documents-edit',compact('documentsTypes','document',
        'subcontractors','vendors'));

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
               'name' => 'required',
              'document_type_id' => 'required|exists:document_types,id'
       ]);

        $slug = \Str::slug($request->name);

        $data['file'] = '';    
       
        $document = Document::with('document_type')->find($id);

         if(!$document){
            return redirect()->back();
        }
        
        $project = @$document->project; 

        $document_type = DocumentType::find($request->document_type_id);

        $project_slug = \Str::slug($project->name);

        $project_type = @$project->project_type;

        $project_type_slug = @$project_type->slug; 

        $document_type_slug = $document_type->slug;

        $old_document_type = $document->document_type()->first();

        $public_path = public_path().'/';

        $folderPath = Document::PROJECT."/";

        $project_type_slug = ($project_type_slug) ? $project_type_slug : Document::ARCHIEVED;

        $folderPath .= $project_type_slug.'/'.$project_slug.'/'.$document_type_slug;
        
        if(($old_document_type->id != $request->document_type_id)){
             
               $oldFolderPath = Document::PROJECT.'/'.$project_type_slug.'/'.$project_slug.'/'.$old_document_type->slug;   

               \File::copyDirectory($public_path.$oldFolderPath,$public_path.$folderPath); 
               \File::deleteDirectory($public_path.$oldFolderPath);
        }

        $document->update($data);

        if($request->hasFile('file')){
               $filesArr = [];
               $files = $request->file('file');
               $dnames = $request->dname;
               $date = @$request->date ?? 0;
               $month = @$request->month ?? 0;
               $year = @$request->year ?? 0;

               foreach ($files as $key => $file) {
                  $dname = (!$dnames[$key]) ? $request->name :  $dnames[$key];
                  $fileName = \Str::slug($dname).'-'.time().$key.'.'. $file->getClientOriginalExtension();
                  $file->storeAs($folderPath, $fileName, 'doc_upload');
                  $filesArr[]  = ['file' => $fileName,'name' => $dname, 
                                  'date' => $date,'month' => $month,
                                  'year' => $year];
               }
                $document->files()->createMany($filesArr);
        }


        return redirect("projects/$project->id#documents")->with('message', 'Document Updated Successfully!');
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

         $document = Document::find($id);

         $project = @$document->project; 

         $project_slug = \Str::slug($project->name);

         $document_type = @$document->document_type->slug;

         $project_type_slug = @ProjectType::find($project->project_type_id)->slug;

         $folderPath = Document::PROJECT."/";

         $project_type_slug = ($project_type_slug) ? $project_type_slug : Document::ARCHIEVED;

         $folderPath .= "$project_type_slug/$project_slug/$document_type/";

         $files = $document->files()->get();

         $publicPath = public_path().'/';

         if(@$document->proposal_id){
           
           $proposal = Proposal::find($document->proposal_id);

           $proposal->update(['files' => '']);

           $aPath = $publicPath.Document::PROPOSALS."/".Document::ARCHIEVED;

           $trade_slug = @\Str::slug($proposal->trade->name);
           $folderPath = Document::PROPOSALS."/";
           $folderPath .= "$project_slug/$trade_slug/";

          } else {

          $aPath = $publicPath.Document::PROJECT."/".Document::ARCHIEVED.'/'. 
          Document::DOCUMENTS; 

          }

          $path = @public_path().'/'.$folderPath;

         \File::makeDirectory($aPath, $mode = 0777, true, true);
         
         foreach (@$files as $key => $file) {
            $proprty_type = ProjectType::find($id);
            @\File::copy($path.$file->file, $aPath.'/'.$file->file);
            @unlink($path.$file->file);
         }

         $document->delete();

        return redirect()->back()->with('message', 'Document Delete Successfully!');
    }

     public function destroyFile($id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

          $path = request()->path;

          $file = DocumentFile::find($id);
         
          $publicPath = public_path().'/';

          if(@$file->document->proposal_id){
           
           $proposal = Proposal::find($file->document->proposal_id);

           $files = @array_filter(explode(',',$proposal->files));

          if (($key = array_search($file->file, $files)) !== false) {
              unset($files[$key]);
          }

           $files = implode(',', $files);          

           $proposal->update(['files' => $files]);

           $aPath = $publicPath.Document::PROPOSALS."/".Document::ARCHIEVED;

          } else {

          $aPath = $publicPath.Document::PROJECT."/".Document::ARCHIEVED.'/'. 
          Document::DOCUMENTS; 

          }

          @\File::makeDirectory($aPath, $mode = 0777, true, true);
        
          @\File::copy($publicPath.$path, $aPath.'/'.$file->file); 
          

          @unlink($path);

          @$file->delete();

         return redirect()->back()->with('message', 'File Delete Successfully!');
    }


    public function search(){


         if(Gate::denies('view')) {
               return abort('401');
         } 

         $docsIds  = [];

         $documents = DocumentFile::query();

         if(request()->filled('property_type')){
          $property_type = ProprtyType::where('slug',request()->property_type)
                           ->with('properties')->first(); 

          $properties = @$property_type->properties->pluck('id');

          $docsIds = Document::propertyIds($properties)->pluck('id');

         }

         if(request()->filled('property')){
          $property = Property::where('id',request()->property)
                           ->with('documents')->first(); 

          $pDocsIds = @$property->documents->pluck('id');

          if($docsIds){

            $docsIds = $docsIds->filter(function ($value, $key) use ($pDocsIds){
                return $pDocsIds->contains($value);
            });            

          }
          else{
             $docsIds = $pDocsIds->merge($docsIds); 
          }
           
           // dd($docsIds);
         }

         if(request()->filled('document_type')){
          $document_type = DocumentType::where('slug',request()->document_type)
                           ->with('documents')->first(); 
          
          $dDocsIds = $document_type->documents->pluck('id');

          if($docsIds){

            $docsIds = $docsIds->filter(function ($value, $key) use ($dDocsIds){
                return $dDocsIds->contains($value);
            });            

          }
          else{
             $docsIds = $dDocsIds->merge($docsIds); 
          }

         // dd($docsIds);

         }
          

         $propertyTypes = ProprtyType::all(); 
         $documentTypes = DocumentType::all(); 
         $properties = Property::all();
         $tenants = Tenant::all();

         $docsIds =    ($docsIds) ? @$docsIds->unique() : []; 

         if($docsIds){
            $documents->docIds($docsIds);
         }

         if(request()->filled('tenant')){

           $documents = $documents->whereHas('document', function ($query) {
                  $query->where('tenant_id', request()->tenant);
           });
         } 

         if(request()->filled('year')){
           $documents = $documents->where('year',request()->year);
         }

        if(request()->filled('month')){
           $documents =  $documents->where('month',request()->month);
         }
        
        if(request()->filled('date')){
           $documents =  $documents->where('date',request()->date);
         }

          if(request()->filled('s')){
            $searchTerm = request()->s;
            $documents->where('file', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('date', 'LIKE', "%{$searchTerm}%")
            ->orWhere('month', 'LIKE', "%{$searchTerm}%")
            ->orWhere('year', 'LIKE', "%{$searchTerm}%");
         }  

         $perPage = request()->filled('per_page') ? request()->per_page : (new DocumentFile())->perPage;

         $documents = $documents->with('document')->paginate($perPage);

       $documents->filter(function($doc){
         
         $property = @$doc->document->property()->first();
        
        $property_slug = \Str::slug($property->property_name);

        $document_type = $doc->document->document_type()->pluck('slug')->first();

        $property_type_slug = @ProprtyType::find($property->proprty_type_id)->slug;

        $folderPath = Document::PROPERTY."/";

        $property_type_slug = ($property_type_slug) ? $property_type_slug : Document::ARCHIEVED;  
        $folderPath .= "$property_type_slug/$property_slug/$document_type/";

        $doc->file = url($folderPath.$doc->file);

         return $doc->file;
       
     });


         //dd($documents);

         return view('properties.documents',compact('documents','propertyTypes',
          'properties','documentTypes','tenants'));
    }



}
