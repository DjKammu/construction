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
use App\Models\Status;
use App\Models\User;
use App\Models\Submittal;
use Gate;
use Carbon\Carbon;

use PDF;

class SubmittalController extends Controller
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

        $subcontractors = Subcontractor::all();
        $statuses = Status::all(); 
        $ballInCourts = BallInCourt::all(); 
        $assignees = Assignee::all(); 
        $users = User::whereNotIn('id',[1])->get();
        // $number = RFI::max('number') + 1;
        return view('projects.includes.submittal-create',compact('id','subcontractors','project','statuses','ballInCourts','assignees','users'));
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
         
        $request->validate([
              'number' => 'required|unique:submittals,number'
        ]);
        
        $project = Project::find($id);
         
        if(!$project &&  $id == null){
            return redirect('/');
        }
        $data = $request->except('_token');
        $data['number'] = Submittal::max('id')+1;
        $data['project_id'] = (int) $id;

        $data['date_sent'] = ($request->filled('date_sent')) ? Carbon::createFromFormat('m-d-Y',$request->date_sent)->format('Y-m-d') : null;

        $data['date_recieved'] = ($request->filled('date_recieved')) ? Carbon::createFromFormat('m-d-Y',$request->date_recieved)->format('Y-m-d') : null;
        

        $project_slug = \Str::slug($project->name);

        $public_path = public_path().'/';

        $folderPath = Document::SUBMITTALS."/";

        $folderPath .= $project_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        $submittal = Submittal::create($data);
       
        $document_type = DocumentType::where('name', DocumentType::SUBMITTAL)
                         ->first();

        $name = @$project->name.' '.@$request->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['project_id' => $project->id,
                    'document_type_id' => $document_type->id,
                    'submittal_id'  => $submittal->id
                     ],
                     ['name' => $name, 'slug' => $slug,
                      'submittal_id'  => $submittal->id,
                     'project_id'       => $project->id,
                     'document_type_id' => $document_type->id,
                     'subcontractor_id' => @$request->subcontractor_id
                     ]
                 );

        if($request->hasFile('sent_file')){

              $file = $request->file('sent_file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $slug.'-sent.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                                  'name' => $name. ' Sent',
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

            $submittal->update(['sent_file' => $fileName]);

            $document->files()->create($fileArr);
        }

        if($request->hasFile('recieved_file')){

              $file = $request->file('recieved_file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $slug.'-recieved.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                                  'name' => $name. ' Recieved',
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

            $submittal->update(['recieved_file' => $fileName]);

            $document->files()->create($fileArr);
        }

      return redirect(route('projects.show',['project' => $id]).'#submittal')->with('message', 'Submittal Created Successfully!');
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
        $submittal = Submittal::find($id);  

        $project = @$submittal->project;

        $project_slug = \Str::slug($project->name);

        $folderPath = Document::SUBMITTALS."/";

        $folderPath .= "$project_slug/";
        
        $submittal->recieved_file = @($submittal->recieved_file) ? $folderPath.$submittal->recieved_file : '';
        $submittal->sent_file = @($submittal->sent_file) ? $folderPath.$submittal->sent_file : '';

        $submittal->date_sent = @($submittal->date_sent) ? Carbon::parse($submittal->date_sent)->format('m-d-Y') : '' ;
        $submittal->date_recieved = @($submittal->date_recieved) ? Carbon::parse($submittal->date_recieved)->format('m-d-Y') : '' ;

        $subcontractors = Subcontractor::all();
        $statuses = Status::all(); 
        $ballInCourts = BallInCourt::all(); 
        $assignees = Assignee::all(); 
        $users = User::whereNotIn('id',[1])->get();
        $number = null;

        session()->flash('url', route('projects.show',
             ['project' => $submittal->project_id]).'?#submittal'); 

        return view('projects.includes.submittal-edit',compact('id','submittal', 'subcontractors',
          'project','statuses','ballInCourts','assignees','users','number'));

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

        $request->validate([
              'number' => 'required|unique:submittals,number,'.$id
        ]);

        $submittal = Submittal::find($id);  
        $data = $request->except('_token');

        $data['date_sent'] = ($request->filled('date_sent')) ? Carbon::createFromFormat('m-d-Y',$request->date_sent)->format('Y-m-d') : null;

        $data['date_recieved'] = ($request->filled('date_recieved')) ? Carbon::createFromFormat('m-d-Y',$request->date_recieved)->format('Y-m-d') : null;
        
         $project = @$submittal->project;

        $project_slug = \Str::slug($project->name);

        $public_path = public_path().'/';

        $folderPath = Document::SUBMITTALS."/";

        $folderPath .= $project_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

       
        $document_type = DocumentType::where('name', DocumentType::SUBMITTAL)
                         ->first();

        $name = @$project->name.' '.@$request->name;                
        $slug = @\Str::slug($name);                

         $document = $project->documents()
                   ->firstOrCreate(['project_id' => $project->id,
                    'document_type_id' => $document_type->id,
                    'submittal_id'  => $submittal->id
                     ],
                     ['name' => $name, 'slug' => $slug,
                       'submittal_id'  => $submittal->id,
                     'project_id'       => $project->id,
                     'document_type_id' => $document_type->id,
                     'subcontractor_id' => @$request->subcontractor_id
                     ]
                 );

        if($request->hasFile('sent_file')){
               @unlink($folderPath.'/'.$submittal->sent_file);
              $file = $request->file('sent_file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $slug.'-sent.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                                  'name' => $name. ' Sent',
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];
            $document->files()->create($fileArr);
            $data['sent_file'] = $fileName;
        }

        if($request->hasFile('recieved_file')){
              @unlink($folderPath.'/'.$submittal->recieved_file);
              $file = $request->file('recieved_file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $slug.'-recieved.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath, $fileName, 'doc_upload');

             $fileArr = ['file' => $fileName,
                                  'name' => $name. ' Recieved',
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];
             $document->files()->create($fileArr);
             $data['recieved_file'] = $fileName;
        }


        $submittal->update($data);


        return redirect(route('projects.show',['project' => $submittal->project_id]).'?#submittal')->with('message', 'Submittal Updated Successfully!');
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

         $submittal = Submittal::find($id);

         $project = @$submittal->project;

         $project_slug = \Str::slug($project->name);

         $public_path = public_path().'/';

         $folderPath = Document::SUBMITTALS."/";

         $folderPath .= "$project_slug/";

         $path = @public_path().'/'.$folderPath;

         $sent_file = @$submittal->sent_file;
         $recieved_file = @$submittal->recieved_file;
         
         $aPath = public_path().'/'. Document::SUBMITTALS."/".Document::ARCHIEVED; 
         \File::makeDirectory($aPath, $mode = 0777, true, true);

        @\File::copy($path.$sent_file, $aPath.'/'.$sent_file);
        @\File::copy($path.$recieved_file, $aPath.'/'.$recieved_file);

        @unlink($path.$sent_file);
        @unlink($path.$recieved_file);

         $project->documents()
                    ->where(['submittal_id' => $id])->delete();

         $submittal->delete();

        return redirect()->back()->with('message', 'Submittal Delete Successfully!');
    }


     public function destroyFile($id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

          $path = request()->path;

          $submittal = Submittal::find($id);

          $file = @end(explode('/', $path));

          $publicPath = public_path().'/';

          $aPath = $publicPath.Document::SUBMITTALS."/".Document::ARCHIEVED; 
          @\File::makeDirectory($aPath, $mode = 0777, true, true);
          @\File::copy($publicPath.$path, $aPath.'/'.$file);

          $docFile  = DocumentFile::whereFile($file)->firstOrFail();

          $coulumn = 'sent_file';

          $coulumn = ( $file == @$rfi->sent_file ) ? 'sent_file' : ( $file == @$rfi->recieved_file ? 'recieved_file' : $coulumn);  
          
          @$docFile->delete();  

          $submittal->update([$coulumn => '']);

          @unlink($path);

         return redirect()->back()->with('message', 'File Delete Successfully!');
    }


      public function sendMail(Request $request, $id){

       set_time_limit(0);
        $submittal = Submittal::find($id); 
        $slug = \Str::slug($submittal->name);
        $files = [ 
                  public_path(Document::SUBMITTALS.'/'.\Str::slug(@$submittal->project->name).'/'.$submittal->recieved_file) ,public_path(Document::SUBMITTALS.'/'.\Str::slug(@$submittal->project->name).'/'.$submittal->sent_file)
             ];

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
