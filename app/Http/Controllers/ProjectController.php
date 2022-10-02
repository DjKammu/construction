<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\Document;
use App\Models\ProjectType;
use App\Models\PropertyType;
use App\Models\Project;
use App\Models\Subcontractor;
use App\Models\Proposal;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\Payment;
use App\Models\Status;
use App\Models\Trade;
use Carbon\Carbon;
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

          if(request()->filled('st')){
            $st = request()->st;
            // $projects->where('status', $st);
            $projects->whereHas('status', function($q) use ($st){
                $q->where('id', $st);
            });

         }
        if(request()->filled('pr')){
            $pr = request()->pr;
            $projects->where('property_type_id', $pr);
         } 

         $propertyTypes = PropertyType::orderBy('name')->get(); 
         $projectTypes = ProjectType::orderBy('name')->get(); 
         $statuses = Status::orderBy('name')->get(); 

         $perPage = request()->filled('per_page') ? request()->per_page : (new Project())->perPage;

         $projects = $projects->orderBy('name')->paginate($perPage);

         return view('projects.index',compact('projects','projectTypes','propertyTypes','statuses'));
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
        $statuses = Status::orderBy('name')->get(); 
        $propertyTypes = PropertyType::orderBy('name')->get(); 
        $projectTypes = ProjectType::orderBy('name')->get(); 

        return view('projects.create',compact('projectTypes','propertyTypes','statuses'));
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
        // $data['status'] = Project::ACTIVE_STATUS;

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
         $statuses = Status::orderBy('name')->get(); 
         $propertyTypes = PropertyType::orderBy('name')->get();
         $projectTypes = ProjectType::orderBy('name')->get();
         $projects = Project::orderBy('name')->get()->except($id);
         $project = Project::find($id);
         $documentTypes = DocumentType::orderBy('name')->get();
         $subcontractors = Subcontractor::orderBy('name')->get();
         $vendors = Vendor::all();
         $documents = $project->documents();
         $trades = $project->trades()->orderBy('name')->get();

         $payments = $project->payments();
         $rfis = $project->rfis();
         $submittals = $project->submittals();

          if(request()->filled('payment_subcontractor')){
                $subcontractor = request()->payment_subcontractor;
                $payments->where('subcontractor_id', $subcontractor);
         } 

         if(request()->filled('payment_vendor')){
                $payment_vendor = request()->payment_vendor;
                $payments->where('vendor_id', $payment_vendor);
         } 

         if(request()->filled('payment_trade')){
                $payment_trade = request()->payment_trade;
                $payments->where('trade_id', $payment_trade);
         } 

         if(request()->filled('payment_status')){
                $payment_status = request()->payment_status;
                $payments->where('status', $payment_status);
         } 

         if(request()->filled('rfi_subcontractor')){
                $subcontractor = request()->rfi_subcontractor;

                $rfis->whereHas('subcontractor', function($q) use ($subcontractor){
                    $q->where('id', $subcontractor);
                });
         }

          if(request()->filled('rfi_status')){
                $status = request()->rfi_status;

                $rfis->whereHas('status', function($q) use ($status){
                    $q->where('id', $status);
                });
         } 

        if(request()->filled('start') && request()->filled('end')){
                 $start = Carbon::parse(request()->start)->format('Y-m-d'); 
                 $end = Carbon::parse(request()->end)->format('Y-m-d'); 
                 $rfis->whereRaw("date_sent >=  date('$start')")
                      ->whereRaw("date_recieved <=  date('$end')");
                
         } 


          if(request()->filled('submittal_subcontractor')){
                $subcontractor = request()->submittal_subcontractor;

                $submittals->whereHas('subcontractor', function($q) use ($subcontractor){
                    $q->where('id', $subcontractor);
                });
         }

          if(request()->filled('submittal_status')){
                $status = request()->submittal_status;

                $submittals->whereHas('status', function($q) use ($status){
                    $q->where('id', $status);
                });
         } 

        if(request()->filled('submittal_start') && request()->filled('submittal_end')){
                 $start = Carbon::parse(request()->submittal_start)->format('Y-m-d'); 
                 $end = Carbon::parse(request()->submittal_end)->format('Y-m-d'); 
                 $submittals->whereRaw("date_sent >=  date('$start')")
                      ->whereRaw("date_recieved <=  date('$end')");
                
         } 

         $orderBy = 'created_at';  
         $orderByRFI = 'created_at';  
         $orderBySubmittal = 'created_at';  
         $order ='DESC' ;
         $orderRFI ='DESC' ;
         $orderSubmittal ='DESC' ;
                    
        if(request()->filled('order')){
            $orderBy = request()->filled('orderby') ? ( !in_array(request()->orderby, 
                ['date','invoice_number','created_at'] ) ? 'created_at' : request()->orderby ) : 'created_at';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
             : request()->order;
        }

        if(request()->filled('orderRFI')){
            $orderByRFI = request()->filled('orderbyRFI') ? ( !in_array(request()->orderbyRFI, 
                ['number','date_sent','date_recieved'] ) ? 'created_at' : request()->orderbyRFI ) : 'created_at';
            
            $orderRFI = !in_array(\Str::lower(request()->orderRFI), ['desc','asc'])  ? 'ASC' 
             : request()->orderRFI;
        }

        if(request()->filled('orderSubmittal')){
            $orderBySubmittal = request()->filled('orderBySubmittal') ? ( !in_array(request()->orderBySubmittal, ['number','date_sent','date_recieved'] ) ? 'created_at' : request()->orderBySubmittal ) : 'created_at';
            
            $orderSubmittal = !in_array(\Str::lower(request()->orderSubmittal), ['desc','asc'])  ? 'ASC' 
             : request()->orderSubmittal;
        }

         $payments = $payments->orderBy($orderBy, $order)->get();

         $rfis = $rfis->orderBy($orderByRFI, $orderRFI)->get();
         $submittals = $submittals->orderBy($orderBySubmittal, $orderSubmittal)->get();

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

        
         $trade = @$trades->first()->id;
        
         if(request()->filled('trade')){
                $trade = request()->trade;  
         } 
        
         $awarded = @$project->proposals()->IsAwarded()->exists();

         $proposalQuery = @$project->proposals();

         $awardedProposals = $proposalQuery->IsAwarded()->get();

         $paymentTrades = @$awardedProposals->map(function($prpsl){
                 return $prpsl->trade;
         })->unique();

         if(!@$project->proposals()->exists()){
             $paymentTrades = Trade::all();
         }

         $paymentSubcontractors = @$awardedProposals->map(function($prpsl){
                 return $prpsl->subcontractor;
         })->unique();
         
         if(request()->filled('proposal_trade')){
                $proposal_trade = request()->proposal_trade;
                $proposalsIds = @$project->proposals()->trade($proposal_trade)->pluck('id');
                $documents->whereIn('proposal_id', $proposalsIds);
         }
    
         $allProposals = @$project->proposals()->get();
         $proposals = @$project->proposals()->trade($trade)->get();
              
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
            
            if($doc->proposal_id){
                 $proposal = Proposal::find($doc->proposal_id);
                 $trade_slug = @\Str::slug($proposal->trade->name);
                 $folderPath = ($doc->document_type->name == DocumentType::INVOICE) ? Document::INVOICES."/" : ( $doc->document_type->name == DocumentType::LIEN_RELEASE ?  Document::LIEN_RELEASES."/" :   Document::PROPOSALS."/");
                 if($doc->document_type->name == DocumentType::LIEN_RELEASE && $doc->payment_id){
                         $payment_id = Payment::find($doc->payment_id);
                         $trade_slug = @\Str::slug($payment_id->trade->name);
                 }
                 $folderPath .= "$project_slug/$trade_slug/";
            }

            else if($doc->document_type->name == DocumentType::RFI ){
                 $folderPath = Document::RFIS."/";
                 $folderPath .= "$project_slug/";
            }
             else if($doc->document_type->name == DocumentType::SUBMITTAL ){
                 $folderPath = Document::SUBMITTALS."/";
                 $folderPath .= "$project_slug/";
            }

            $files = $doc->files();

            $file =  ($files->count() == 1) ? $files->pluck('file')->first() : '';

            $doc->file = ($file  ? asset($folderPath.$file) : '') ;

            return $doc->file;
           
         });
       

          $proposals->filter(function($proposal){

            $project = @$proposal->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($proposal->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $folderPath = Document::PROPOSALS."/";

            $folderPath .= "$project_slug/$trade_slug/";
            
            $files = $proposal->files;

            $files = @array_filter(explode(',',$files));

            $filesArr = [];
            
            if(!empty($files)){
               foreach (@$files as $key => $file) {
                   $filesArr[] = asset($folderPath.$file);
                }  
            } 

            $proposal->files = @($filesArr) ? @implode(',',$filesArr) : '' ;

            return $proposal->files;
           
         });


          $payments->filter(function($payment){

            $project = @$payment->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($payment->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $folderPath = Document::INVOICES."/";

            if(@!$trade_slug){
                 $vendor  = Vendor::find($payment->vendor_id);
                 $trade_slug = @$vendor->slug;
            }

            $folderPath .= "$project_slug/$trade_slug/";

            $folderPath2 = Document::LIEN_RELEASES."/";
            
            $folderPath2 .= "$project_slug/$trade_slug/";

        
            $payment->file = @($payment->file) ? asset($folderPath.$payment->file) : '' ;
            $payment->conditional_lien_release_file = @($payment->conditional_lien_release_file) ? asset($folderPath2.$payment->conditional_lien_release_file) : '' ;
            $payment->unconditional_lien_release_file = @($payment->unconditional_lien_release_file) ? asset($folderPath2.$payment->unconditional_lien_release_file) : '' ;

            $payment->remaining = (new PaymentController)->proposalDueAmount($payment->proposal,$payment->id);

            return $payment->file;
           
         }); 

          $rfis->filter(function($rfi){

            $project = @$rfi->project;

            $project_slug = \Str::slug($project->name);

            $folderPath = Document::RFIS."/";

            $folderPath .= "$project_slug/";
        
            $rfi->sent_file = @($rfi->sent_file) ? asset($folderPath.$rfi->sent_file) : '' ;
            $rfi->recieved_file = @($rfi->recieved_file) ? asset($folderPath.$rfi->recieved_file) : '' ;
           
         });

          $submittals->filter(function($submittal){

            $project = @$submittal->project;

            $project_slug = \Str::slug($project->name);

            $folderPath = Document::SUBMITTALS."/";

            $folderPath .= "$project_slug/";
        
            $submittal->sent_file = @($submittal->sent_file) ?
                          asset($folderPath.$submittal->sent_file) : '' ;
            $submittal->recieved_file = @($submittal->recieved_file) ? 
                          asset($folderPath.$submittal->recieved_file) : '' ;
           
         });

         $catids = @($trades->pluck('category_id'))->unique();

         $categories = $paymentCategories = Category::whereIn('id',$catids)->get(); 

         $pTrades =  [];
         
         $trade_ids = @$project->payments->whereNotNull('trade_id')
                       ->pluck('trade_id');  
         $pTrades = Trade::whereIn('id',$trade_ids)->get();   

         $prTrades = $trades;

         if($pTrades){
            $trades = $trades->merge($pTrades);
         }

         if($paymentCategories->count() == 0){   
              $catids = @($pTrades->pluck('category_id'))->unique();
              $paymentCategories = Category::whereIn('id',$catids)->get(); 
         }

         $subcontractorsCount = @$project->proposals()
                                  ->withCount('subcontractor')
                                 ->orderBy('subcontractor_count', 'DESC')
                                  ->pluck('subcontractor_count')->max(); 
           
         return view('projects.edit',compact('projectTypes','propertyTypes','project','documentTypes','documents','subcontractors','vendors','trades','projects','trade','proposals','awarded',
            'categories','subcontractorsCount','allProposals','payments','paymentTrades',
            'paymentSubcontractors','paymentCategories','pTrades','prTrades','statuses','rfis',
            'submittals'));
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
                  $path = public_path().'/'.Document::INVOICES."/";
                  @rename($path.$oldSlug, $path.$slug); 
                  $path = public_path().'/'.Document::PROPOSALS."/";
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

    
    public function getAttachment(Request $request, $id){

     $project = Project::find($id);
     $attachment = $project->attachment;

     $fileInfo = pathinfo($attachment);
     $extension = $fileInfo['extension'];

     if(in_array(\Str::lower($extension),['doc','docx','docm','dot',
    'dotm','dotx'])){
         $extension = 'word'; 
     }
    else if(in_array(\Str::lower($extension),['csv','dbf','dif','xla',
        'xls','xlsb','xlsm','xlsx','xlt','xltm','xltx'])){
         $extension = 'excel'; 
    }

    return response()->json(
           [
            'status' => 200,
            'message' => true,
            'URL'     => url(\Storage::url($attachment)),
            'extension'  => $extension
           ]
        );

    } 

    public function uploadAttachment(Request $request, $id){

        $project = Project::find($id);
        if($request->hasFile('attachment')){
               $attachment = $request->file('attachment');
               $attachmentName = \Str::slug($attachment->getClientOriginalName()).'-'.time() . '.' . $attachment->getClientOriginalExtension();
               $path = Document::PROJECTS.'/'.Document::ATTACHMENTS;
               $data['attachment']  = $request->file('attachment')->storeAs($path, $attachmentName,
                'public');
               @unlink('storage/'.$project->attachment);
        }

        $project->update($data);

        return response()->json(
           [
            'status' => 200,
            'message' => 'Attachment Uploaded Successfully!'
           ]
        );
    }

}
