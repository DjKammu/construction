<?php

namespace App\Http\Controllers;

use App\Mail\MaitToSubcontractor;
use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Trade;
use App\Models\Document;
use App\Models\Proposal;
use App\Models\Payment;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\DocumentType;
use App\Models\DocumentFile;
use App\Models\Subcontractor;
use App\Models\BallInCourt;
use App\Models\Assignee;
use App\Models\RFISubmittalStatus;
use App\Models\User;
use App\Models\Submittal;
use App\Models\Inspection;
use App\Models\InspectionType;
use App\Models\InspectionCategory;
use Gate;
use Carbon\Carbon;

use PDF;

class InspectionController extends Controller
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
        //
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

        $inspectionTypes = InspectionType::orderBy('name')->get();
        $inspectionCategories = InspectionCategory::orderBy('name')->get(); 

        return view('projects.includes.inspection-create',compact('id','inspectionTypes','project','inspectionCategories'));
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
         
        $project = Project::find($id);

         
        if(!$project &&  $id == null){
            return redirect('/');
        }
        $data = $request->except('_token','files');

        $data['project_id'] = (int) $id;

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : null;

        $project_slug = \Str::slug($project->name);


        $public_path = public_path().'/';

        $folderPath = Document::INSPECTIONS."/";

        $folderPath .= $project_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

    
        $inspection = Inspection::create($data);
       
        $document_type = DocumentType::where('name', DocumentType::INSPECTION)
                         ->first();

        $name = @$project->name.' '.@$request->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['project_id' => $project->id,
                    'document_type_id' => $document_type->id,
                    'inspection_id'  => $inspection->id
                     ],
                     ['name' => $name, 'slug' => $slug,
                      'inspection_id'  => $inspection->id,
                     'project_id'       => $project->id,
                     'document_type_id' => $document_type->id
                     ]
                 );

       if($request->hasFile('files')){
             $filesArr = [];
             $files = $request->file('files');
             $date  = date('d');
             $month = date('m');
             $year  = date('Y');
           
             foreach ($files as $key => $file) {
                    $fileName = $slug.'-'.@\Str::slug(DocumentType::INSPECTION).'-'.time().$key.'.'. $file->getClientOriginalExtension();
                    $file->storeAs($folderPath, $fileName, 'doc_upload');
                    $filesArr[] = $fileName; 

                     $fileArr[] = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];
               }

            $document->files()->createMany($fileArr);
            $inspection->update(['files' => implode(',',$filesArr)]);
        }



      return redirect(route('projects.show',['project' => $id]).'#inspection')->with('message', 'Inspection Created Successfully!');
    }
     

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
         if(Gate::denies('edit')) {
           return abort('401');
        } 
        $inspection = Inspection::find($id);  

        $project = @$inspection->project;

        $project_slug = \Str::slug($project->name);

        $filesCollection = ($inspection->files) ? @explode(',',$inspection->files) : [];

        $inspection->files = @collect($filesCollection)->map(function($file) use ($inspection){

            $project = @$inspection->project;

            $project_slug = \Str::slug($project->name);

            $folderPath = Document::INSPECTIONS."/";

             $folderPath .= "$project_slug/";
            
            $file = @($file) ? $folderPath.$file : '' ;

            return $file;
           
         })->implode(',');

        $inspection->date = @($inspection->date) ? Carbon::parse($inspection->date)->format('m-d-Y') : '' ;

        $inspectionTypes = InspectionType::orderBy('name')->get();
        $inspectionCategories = InspectionCategory::orderBy('name')->get(); 

        session()->flash('url', route('projects.show',
             ['project' => $inspection->project_id]).'?#inspection'); 

        return view('projects.includes.inspection-edit',compact('id','inspection', 'inspectionTypes',
          'project','inspectionCategories'));

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

        $inspection = Inspection::find($id);  
        $data = $request->except('_token','files');

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : null;
        $data['passed'] = ($request->filled('passed')) ? $request->passed : 0;
        
         $project = @$inspection->project;

        $project_slug = \Str::slug($project->name);

        $public_path = public_path().'/';

        $folderPath = Document::INSPECTIONS."/";

        $folderPath .= $project_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

       
        $document_type = DocumentType::where('name', DocumentType::INSPECTION)
                         ->first();         

        $name = @$project->name.' '.@$request->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['project_id' => $project->id,
                    'document_type_id' => $document_type->id,
                    'inspection_id'  => $inspection->id
                     ],
                     ['name' => $name, 'slug' => $slug,
                      'inspection_id'  => $inspection->id,
                     'project_id'       => $project->id,
                     'document_type_id' => $document_type->id
                     ]
                 );
         
        if($request->hasFile('files')){
             $filesArr = @array_filter(explode(',',$inspection->files));
             
             $files = $request->file('files');
             $date  = date('d');
             $month = date('m');
             $year  = date('Y');

             foreach ($files as $key => $file) {
                     $fileName = $slug.'-'.@\Str::slug(DocumentType::INSPECTION).'-'.time().$key.'.'. $file->getClientOriginalExtension();
                     $file->storeAs($folderPath, $fileName, 'doc_upload');
                     $filesArr[] = $fileName; 
                     $fileArr[] = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];
               }
            $document->files()->createMany($fileArr);
            $data['files'] = implode(',',$filesArr);
        }
        $inspection->update($data);

        return redirect(route('projects.show',['project' => $inspection->project_id]).'?#inspection')->with('message', 'Inspection Updated Successfully!');
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

         $inspection = Inspection::find($id);

         $project = @$inspection->project;

         $project_slug = \Str::slug($project->name);

         $public_path = public_path().'/';

         $folderPath = Document::INSPECTIONS."/";

         $folderPath .= "$project_slug/";

         $path = @public_path().'/'.$folderPath;
         
         $aPath = public_path().'/'. Document::INSPECTIONS."/".Document::ARCHIEVED; 
         \File::makeDirectory($aPath, $mode = 0777, true, true);

         $files = @explode(',',$proposal->files);

        foreach (@$files as $key => $file) {
            @\File::copy($path.$file, $aPath.'/'.$file);
            @unlink($path.$file);
         }

         $project->documents()
                    ->where(['inspection_id' => $id])->delete();

         $inspection->delete();

        return redirect()->back()->with('message', 'Inspection Delete Successfully!');
    }


     public function destroyFile($id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

          $path = request()->path;

          $inspection = Inspection::find($id);

          $file = @end(explode('/', $path));

          $publicPath = public_path().'/';

          $aPath = $publicPath.Document::INSPECTIONS."/".Document::ARCHIEVED; 
          @\File::makeDirectory($aPath, $mode = 0777, true, true);
          @\File::copy($publicPath.$path, $aPath.'/'.$file);

          $docFile  = DocumentFile::whereFile($file)->firstOrFail();

          $files = @array_filter(explode(',',$inspection->files));

          if (($key = array_search($file, $files)) !== false) {
              unset($files[$key]);
          }

          $files = implode(',', $files); 

          @$docFile->delete();          

          $inspection->update(['files' => $files]);

          @unlink($path);

          return redirect()->back()->with('message', 'File Delete Successfully!');
    }


      public function sendMail(Request $request, $id){

        set_time_limit(0);
        $submittal = Submittal::find($id); 
        $slug = \Str::slug($submittal->name);
   
        $files = [];

        if($submittal->recieved_file){
           $files[] =   public_path(Document::SUBMITTALS.'/'.\Str::slug(@$submittal->project->name).'/'.$submittal->recieved_file);
        }

        if($submittal->sent_file){
          $files[] =  public_path(Document::SUBMITTALS.'/'.\Str::slug(@$submittal->project->name).'/'.$submittal->sent_file);
        } 
              

        $ccUsers = ($request->filled('cc')) ? explode(',',$request->cc) : [];
        $bccUsers = ($request->filled('cc')) ? explode(',',$request->bcc) : [];

        $data = [
          'heading' => '',
          'plans' => '',
          'file' => '' ,
          'files' => $files ,
          'subject' => $request->subject,
          'content' => $request->message,
        ];
        
        dispatch(
          function() use ($request, $data, $ccUsers, $bccUsers){
           $mail = \Mail::to($request->recipient);
             if(array_filter($ccUsers)  &&  count($ccUsers) > 0){
              $mail->cc($ccUsers);
             }
             if(array_filter($bccUsers)  && count($bccUsers) > 0){
              $mail->bcc($bccUsers);
             }
             $mail->send(new MaitToSubcontractor($data));
          }
        )->afterResponse();


      return response()->json(
           [
            'status' => 200,
            'message' => 'Sent Successfully!'
           ]
       );

    }
}
