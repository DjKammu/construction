<?php

namespace App\Http\Controllers;

use App\Mail\MaitToSubcontractor;
use Illuminate\Validation\Rule; 
use App\Models\ProcurementLog;
use Illuminate\Http\Request;
use App\Models\Subcontractor;
use App\Models\PaymentStatus;
use App\Models\DocumentType;
use App\Models\DocumentFile;
use App\Models\Document;
use App\Models\Proposal;
use App\Models\Category;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\Trade;
use Gate;
use Carbon\Carbon;

use PDF;

class ProcurementLogController extends Controller
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

        $vendors = Vendor::orderBy('name')->get();

        $allTrades = Trade::orderBy('name')->get();
        $statuses = PaymentStatus::orderBy('name')->get(); 
        $subcontractors = Subcontractor::orderBy('name')->get();

        return view('projects.includes.logs-create',compact('project','id','statuses','vendors','allTrades','subcontractors'));
    }  



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $project_id)
    {
          if(Gate::denies('add')) {
               return abort('401');
        } 

         
        if($project_id == null){
            return redirect('/');
        }
        $data = $request->except('_token');
    
        $request->validate([
                   'date' => 'required|date_format:m-d-Y'
              ]
        );
     
        $data['project_id']  = (int) $project_id;

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d'); 

        $data['po_sent'] = ($request->filled('po_sent')) ? Carbon::createFromFormat('m-d-Y',$request->po_sent)->format('Y-m-d') : null;
        $data['date_shipped'] = ($request->filled('date_shipped')) ? Carbon::createFromFormat('m-d-Y',$request->date_shipped)->format('Y-m-d') : null;

        $data['tentative_date_delivery'] = ($request->filled('tentative_date_delivery')) ? Carbon::createFromFormat('m-d-Y',$request->tentative_date_delivery)->format('Y-m-d') : null;

        $data['date_received'] = ($request->filled('date_received')) ? Carbon::createFromFormat('m-d-Y',$request->date_received)->format('Y-m-d') : null;
        
        $project = Project::find($project_id);

        $project_slug = \Str::slug($project->name);
      
        $public_path = public_path().'/';

        $folderPath = Document::RECEIVED_SHIPMENTS."/";

        $folderPath .= $project_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        $data['received_shipment_attachment'] = '';

        $log = ProcurementLog::create($data);
       
        $document_type = DocumentType::where('name', DocumentType::RECEIVED_SHIPMENT)
                         ->first();

        $name = @$project->name.' '.@$document_type->name; 

        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['log_id' => $log->id,
                    'document_type_id' => $document_type->id
                     ],
                     ['name' => $name, 'slug' => $slug,
                     'log_id'       => $log->id,
                     'project_id'       => $project_id,
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


                    $fileName = $slug.'-'.@\Str::slug(DocumentType::RECEIVED_SHIPMENT).'-'.time().$key.'.'. $file->getClientOriginalExtension();
                    $file->storeAs($folderPath, $fileName, 'doc_upload');
                    $filesArr[] = $fileName; 

                     $fileArr[] = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

               }

            $document->files()->createMany($fileArr);
            $log->update(['received_shipment_attachment' => implode(',',$filesArr)]);
        }


        return redirect(route('projects.show',['project' => $project_id]).'#logs')->with('message', 'Log Created Successfully!');
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

        $log = ProcurementLog::find($id);  

        $log->date = @($log->date) ? Carbon::parse($log->date)->format('m-d-Y') : '' ;
        $log->po_sent = @($log->po_sent) ? Carbon::parse($log->po_sent)->format('m-d-Y') : '' ;
        $log->date_shipped = @($log->date_shipped) ? Carbon::parse($log->date_shipped)->format('m-d-Y') : '' ;
        $log->tentative_date_delivery = @($log->tentative_date_delivery) ? Carbon::parse($log->tentative_date_delivery)->format('m-d-Y') : '' ;
        $log->date_received = @($log->date_received) ? Carbon::parse($log->date_received)->format('m-d-Y') : '' ;

        $vendors = Vendor::orderBy('name')->get();
        $allTrades = Trade::orderBy('name')->get();
        $statuses = PaymentStatus::orderBy('name')->get(); 
        $subcontractors = Subcontractor::orderBy('name')->get();

         $filesCollection = ($log->received_shipment_attachment) ? @explode(',',$log->received_shipment_attachment) : [];

         $log->received_shipment_attachment = @collect($filesCollection)->map(function($file) use ($log){

            $project = @$log->project;

            $project_slug = \Str::slug($project->name);

            $folderPath = Document::RECEIVED_SHIPMENTS."/";

            $folderPath .= "$project_slug/";
            
            $file = @($file) ? $folderPath.$file : '' ;

            return $file;
           
         })->implode(',');

        return view('projects.includes.logs-edit',compact('log','subcontractors','vendors','allTrades','statuses'));
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
     jmmmmmjjjjjjj* Update the specified resource in storage.
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

  
        $log = ProcurementLog::find($id);

        $request->validate([
                 'date' => 'required|date_format:m-d-Y'
            ]
        );
   

        $data['date'] = ($request->filled('date')) ? Carbon::createFromFormat('m-d-Y',$request->date)->format('Y-m-d') : date('Y-m-d'); 

        $data['po_sent'] = ($request->filled('po_sent')) ? Carbon::createFromFormat('m-d-Y',$request->po_sent)->format('Y-m-d') : null;
        $data['date_shipped'] = ($request->filled('date_shipped')) ? Carbon::createFromFormat('m-d-Y',$request->date_shipped)->format('Y-m-d') : null;

        $data['tentative_date_delivery'] = ($request->filled('tentative_date_delivery')) ? Carbon::createFromFormat('m-d-Y',$request->tentative_date_delivery)->format('Y-m-d') : null;

        $data['date_received'] = ($request->filled('date_received')) ? Carbon::createFromFormat('m-d-Y',$request->date_received)->format('Y-m-d') : null;
        
        $project = @$log->project;


        $project_slug = \Str::slug($project->name);
      
        $public_path = public_path().'/';

        $folderPath = Document::RECEIVED_SHIPMENTS."/";

        $folderPath .= $project_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);
       
        $document_type = DocumentType::where('name', DocumentType::RECEIVED_SHIPMENT)
                         ->first();

        $name = @$project->name.' '.@$document_type->name; 

        $slug = @\Str::slug($name);                

        $document = $project->documents()
                   ->firstOrCreate(['log_id' => $log->id,
                    'document_type_id' => $document_type->id
                     ],
                     ['name' => $name, 'slug' => $slug,
                     'log_id'       => $log->id,
                     'project_id'       => $log->project_id,
                     'document_type_id' => $document_type->id
                     ]
                 );

        if($request->hasFile('files')){

             $filesArr = @array_filter(explode(',',$log->received_shipment_attachment));
             
             $files = $request->file('files');
             $date  = date('d');
             $month = date('m');
             $year  = date('Y');

             foreach ($files as $key => $file) {

                    $fileName = $slug.'-'.@\Str::slug(DocumentType::RECEIVED_SHIPMENT).'-'.time().$key.'.'. $file->getClientOriginalExtension();
                    $file->storeAs($folderPath, $fileName, 'doc_upload');
                    $filesArr[] = $fileName; 

                     $fileArr[] = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

               }

            $document->files()->createMany($fileArr);
            $data['received_shipment_attachment'] = implode(',',$filesArr);
        }

        $log->update($data);

        
        return redirect(route('projects.show',['project' => $log->project_id]).'?#logs')->with('message', 'Log Updated Successfully!');
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
          
         $log = ProcurementLog::find($id);
          
         $project = @$log->project;

         $project_slug = \Str::slug($project->name);

         $public_path = public_path().'/';

         $folderPath = Document::RECEIVED_SHIPMENTS."/";

         $folderPath .= "$project_slug/";

         $path = @public_path().'/'.$folderPath;

         $files = @explode(',',$log->received_shipment_attachment);

         $aPath = public_path().'/'. Document::RECEIVED_SHIPMENTS."/".Document::ARCHIEVED; 
         \File::makeDirectory($aPath, $mode = 0777, true, true);
         
         foreach (@$files as $key => $file) {
            @\File::copy($path.$file, $aPath.'/'.$file);
            @unlink($path.$file);
         }

         $project->documents()
                    ->where(['log_id' => $id])->delete();

         $log->delete();

        return redirect()->back()->with('message', 'Log Delete Successfully!');
    }


     public function destroyFile( $id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

          $path = request()->path;

          $log = ProcurementLog::find($id);

          $file = @end(explode('/', $path));

          $publicPath = public_path().'/';

          $folder = Document::RECEIVED_SHIPMENTS;
        
          $aPath = $publicPath.$folder."/".Document::ARCHIEVED;

          @\File::makeDirectory($aPath, $mode = 0777, true, true);

           @\File::copy($publicPath.$path, $aPath.'/'.$file);

          
          $files = @array_filter(explode(',',$log->received_shipment_attachment));

          if (($key = array_search($file, $files)) !== false) {
              unset($files[$key]);
          }

          $files = implode(',', $files); 

          $docFile  = DocumentFile::whereFile($file)->firstOrFail();
          $docFile->delete();
      
          $log->update(['received_shipment_attachment' => $files]);

          @unlink($path);

         return redirect()->back()->with('message', 'File Delete Successfully!');
    }

    public function downloadPDF($id,$view = false){


        $project = Project::find($id); 
        $logs = $project->logs()->get();

        $pdf = PDF::loadView('projects.includes.logs-pdf',
          ['logs' => $logs]
        );

        $slug = \Str::slug($project->name);

        if($view){
       //  return $pdf->stream('project_'.$slug .'_ffe_budget.pdf');
         return $pdf->setPaper('a4')->output();
        }

        return $pdf->download($slug.'-project-logs.pdf');

    }

    public function sendMail(Request $request, $id){

       set_time_limit(0);
        $project = Project::find($id); 
         $slug = \Str::slug($project->name);

        $ccUsers = ($request->filled('cc')) ? explode(',',$request->cc) : [];
        $bccUsers = ($request->filled('cc')) ? explode(',',$request->bcc) : [];

        $data = [
          'heading' => '',
          'plans' => '',
          'file' => '',
          'subject' => $request->subject,
          'content' => $request->message,
        ];
       

        $pdffile = $this->downloadPDF($id,true);

        $data['pdffile'] = $pdffile;
        $data['fileName'] = $slug.'log.pdf';
        
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
