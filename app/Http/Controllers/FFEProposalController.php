<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Project;
use App\Models\FFETrade;
use App\Models\Document;
use App\Models\FFEProposal;
use App\Models\FFEVendor;
use App\Models\DocumentType;
use Gate;

class FFEProposalController extends Controller
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
    public function create(Request $request, $id, $trade_id)
    {
        if(Gate::denies('add')) {
               return abort('401');
         }

        $trade = FFETrade::find($trade_id);  
        $project  = Project::find($id);  

        if(!$trade || !$project){
            return redirect()->back();
        }

        $ffe_vendors = @$trade->ffe_vendors()
                  ->whereDoesntHave('ffe_proposals', function($q) use($trade_id,$id){
                    $q->where("f_f_e_trade_id",$trade_id);
                    $q->where("project_id",$id);
                  })->orderBy('name')->get();

        return view('projects.ffe.proposals-create',compact('project','ffe_vendors'));
    }  



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id, $trade_id)
    {
          if(Gate::denies('add')) {
               return abort('401');
        } 
        
        $trade = FFETrade::find($trade_id);  
        $project  = Project::find($id);  
         
        if(!$trade || !$project){
            return redirect('/');
        }

        $data = $request->except('_token');

        $proposal = FFEProposal::where('f_f_e_vendor_id',$request->f_f_e_vendor_id)
              ->where(
                function($query) use ($id,$trade_id){
                    (new FFEProposal)->scopeHaveProposal($query,$id,$trade_id);
              })->exists();
        

       if($proposal){
         return redirect(route('ffe.index',['project' => $id]).'?trade='.$trade_id.'#proposals')->withErrors(['Already FFE Proposal exists for this subcontractor!']);
       }

        $request->validate([
              'f_f_e_vendor_id' => ['required',
              'exists:f_f_e_vendors,id'],
              // 'labour_cost' => 'required',
              // 'material' => 'required',
              // 'subcontractor_price' => 'required'
          ]
      );

        $data['labour_cost'] = $data['labour_cost'] ?? 0;
        $data['material']    = $data['material'] ?? 0;
        $data['subcontractor_price'] = $data['subcontractor_price'] ?? 0;

        $data['project_id'] = $id;
        $data['f_f_e_trade_id'] = $trade_id;
 
        $project = Project::find($id);

        $project_slug = \Str::slug($project->name);

        $trade = FFETrade::find($trade_id);

        $trade_slug = @$trade->slug;

        $ffe_vendor = FFEVendor::find($request->f_f_e_vendor_id);

        $ffe_vendor_slug = $ffe_vendor->slug;

        $public_path = public_path().'/';

        $folderPath = Document::FFE_PROPOSALS."/";

        $folderPath .= $project_slug.'/'.$trade_slug;

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        $data['files'] = '';

        $proposal = FFEProposal::create($data);

        $document_type = DocumentType::where('name', DocumentType::BID)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$proposal->vendor->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                    ->firstOrCreate(['ffe_proposal_id' => $proposal->id],
                       ['name' => $name, 'slug' => $slug,
                       'ffe_proposal_id'      => $proposal->id,
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

                    $fileName = $ffe_vendor_slug.'-'.time().$key.'.'. $file->getClientOriginalExtension();
                    $file->storeAs($folderPath, $fileName, 'doc_upload');
                     $filesArr[] = $fileName; 

                     $fileArr[] = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

               }
            $document->files()->createMany($fileArr);
            $proposal->update(['files' => implode(',',$filesArr)]);
        }

        

        return redirect(route('ffe.index',['project' => $id]).'?trade='.$trade_id.'#proposals')->with('message', 'FFE Proposal Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($project, $id)
    {
         if(Gate::denies('edit')) {
           return abort('401');
        } 

        $proposal = FFEProposal::find($id);  
        $ffe_vendor = @$proposal->vendor;

        $filesCollection = ($proposal->files) ? @explode(',',$proposal->files) : [];

        $proposal->files = @collect($filesCollection)->map(function($file) use ($proposal){

            $project = @$proposal->project;

            $project_slug = \Str::slug($project->name);

            $trade_slug = @\Str::slug($proposal->trade->name);

            $project_type_slug = @$project->project_type->slug;

            $folderPath = Document::FFE_PROPOSALS."/";

            $folderPath .= "$project_slug/$trade_slug/";
            
            $file = @($file) ? $folderPath.$file : '' ;

            return $file;
           
         })->implode(',');

         
         session()->flash('url', route('ffe.index',['project' => $proposal->project_id]).'?trade='.$proposal->trade_id.'#proposals'); 

        return view('projects.ffe.proposals-edit',compact('ffe_vendor','proposal'));
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
    public function update(Request $request, $project, $id)
    {
        if(Gate::denies('update')) {
               return abort('401');
        } 

        $data = $request->except('_token');

        $request->validate([
              // 'labour_cost' => 'required',
              // 'material' => 'required',
              // 'subcontractor_price' => 'required'
          ]
       );
        
        $data['labour_cost'] = $data['labour_cost'] ?? 0;
        $data['material']    = $data['material'] ?? 0;
        $data['subcontractor_price'] = $data['subcontractor_price'] ?? 0;

        $proposal = FFEProposal::find($id);

        $project = @$proposal->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @$proposal->trade->slug;

        $ffe_vendor_slug = @$proposal->vendor->slug;

        $public_path = public_path().'/';

        $folderPath = Document::FFE_PROPOSALS."/";

        $folderPath .= $project_slug.'/'.$trade_slug;
        
        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);
         $document_type = DocumentType::where('name', DocumentType::BID)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$proposal->subcontractor->name;                 
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                    ->firstOrCreate(['ffe_proposal_id' => $id],
                       ['name' => $name, 'slug' => $slug,
                       'ffe_proposal_id'      => $id,
                       'document_type_id' => $document_type->id
                       ]
                   );


        if($request->hasFile('files')){
             $filesArr = @array_filter(explode(',',$proposal->files));
             
             $files = $request->file('files');
             $date  = date('d');
             $month = date('m');
             $year  = date('Y');

             foreach ($files as $key => $file) {

                   $fileName = $ffe_vendor_slug.'-'.time().$key.'.'. $file->getClientOriginalExtension();
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

        $proposal->update($data);

        $changeOrders = $request->change_orders;
        $typesArr = @$changeOrders['type'];
        $priceArr = @$changeOrders['subcontractor_price'] ?? [];
        $notesArr = @$changeOrders['notes'];
        $idArr    = @$changeOrders['id'];

        $changeOrdersIds = @$proposal->changeOrders->pluck('id')->toArray();

        foreach (@$priceArr as $key => $price) {
           $id = @$idArr[$key] ?? 0; 
           $proposal->changeOrders()->updateOrCreate(
            ['id' => $id],
            [ 'subcontractor_price' => $price,
              'type' => $typesArr[$key],
              'notes'=> $notesArr[$key]
             ]
           );
        }

        $reqChangeOrdersIds = (@$request->change_orders['id']) ? @$request->change_orders['id'] : [];

        $notIds = @array_diff($changeOrdersIds, $reqChangeOrdersIds); 

        if($notIds)
        { 
            @$proposal->changeOrders()->whereIn('id', $notIds)->delete();
        }
      
        return redirect(route('ffe.index',['project' => $proposal->project_id]).'?trade='.$proposal->trade_id.'#proposals')->with('message', 'FFE Proposal Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($project, $id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

         $proposal = FFEProposal::find($id);

         $project = @$proposal->project;

         $project_slug = \Str::slug($project->name);

         $trade_slug = @\Str::slug($proposal->trade->name);

         $public_path = public_path().'/';

         $folderPath = Document::FFE_PROPOSALS."/";

         $folderPath .= "$project_slug/$trade_slug/";

         $path = @public_path().'/'.$folderPath;

         $files = @explode(',',$proposal->files);

         $aPath = public_path().'/'. Document::FFE_PROPOSALS."/".Document::ARCHIEVED; 
         \File::makeDirectory($aPath, $mode = 0777, true, true);
         
         foreach (@$files as $key => $file) {
            @\File::copy($path.$file, $aPath.'/'.$file);
            @unlink($path.$file);
         }

         $project->documents()
                    ->where(['ffe_proposal_id' => $id])->delete();

         $proposal->delete();

        return redirect()->back()->with('message', 'Proposal Delete Successfully!');
    }



      /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function award($project, $id, $award)
    {
        if(Gate::denies('update')) {
               return abort('401');
         } 
         
        $proposal = FFEProposal::find($id);

        $isAwarded = $proposal->HaveProposal($proposal->project_id, $proposal->trade_id)
                    ->IsAwarded()->exists();

         if($isAwarded && ($award == FFEProposal::RETRACTED)){
           return redirect(route('ffe.index',['project' => $proposal->project_id]).'#proposals')->withErrors('Proposal already awarded for this trade!');
         }

       $award =  ($award == FFEProposal::AWARDED) ? FFEProposal::RETRACTED : FFEProposal::AWARDED;

       $proposal->update(['awarded' => $award]);
        
       $awardMsg = ($award == FFEProposal::AWARDED) ? FFEProposal::AWARDED_TEXT : FFEProposal::RETRACTED_TEXT ; 

      return redirect(route('ffe.index',['project' => $proposal->project_id]).'?trade='.$proposal->trade_id.'#proposals')->with('message', 'Proposal '.$awardMsg.' 
        Successfully!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request,$project, $id)
    {
          if(Gate::denies('update')) {
               return abort('401');
        } 


        $data = $request->except('_token');

        $proposal = FFEProposal::find($id);

        $project = @$proposal->project;

        $project_slug = \Str::slug($project->name);

        $trade_slug = @\Str::slug($proposal->trade->name);

        $ffe_vendor_slug = @\Str::slug($proposal->vendor->name);

        $public_path = public_path().'/';

        $folderPath = Document::FFE_PROPOSALS."/";

        $folderPath .= $project_slug.'/'.$trade_slug;
        
        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        $document_type = DocumentType::where('name', DocumentType::BID)
                         ->first();

        $name = @$project->name.' '.@$document_type->name.' '.@$proposal->subcontractor->name;                
        $slug = @\Str::slug($name);                

        $document = $project->documents()
                    ->firstOrCreate(['ffe_proposal_id' => $id],
                       ['name' => $name, 'slug' => $slug,
                       'ffe_proposal_id'      => $id,
                       'document_type_id' => $document_type->id,
                       'subcontractor_id' => @$proposal->subcontractor->id
                       ]
                   );

        if($request->hasFile('file')){
              $filesArr = @array_filter(explode(',',$proposal->files));
              $file = $request->file('file');

              $date  = date('d');
              $month = date('m');
              $year  = date('Y');

             $fileName = $ffe_vendor_slug.'-'.time().'.'. $file->getClientOriginalExtension();
             $file->storeAs($folderPath, $fileName, 'doc_upload');

             $filesArr[] = $fileName; 
            
             $fileArr = ['file' => $fileName,
                                  'name' => $name,
                                  'date' => $date,'month' => $month,
                                  'year' => $year
                                  ];

            $proposal->update(['files' => implode(',',$filesArr)]);

            $document->files()->create($fileArr);
        }
        

        return redirect(route('ffe.index',['project' => $proposal->project_id]).'?trade='.$proposal->trade_id.'#proposals')->with('message', 'File added Successfully!');
    }
    


     public function destroyFile($project, $id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

          $path = request()->path;

          $proposal = FFEProposal::find($id);

          $file = @end(explode('/', $path));

          $publicPath = public_path().'/';


          $aPath = $publicPath.Document::FFE_PROPOSALS."/".Document::ARCHIEVED; 

          @\File::makeDirectory($aPath, $mode = 0777, true, true);

          @\File::copy($publicPath.$path, $aPath.'/'.$file);

          $files = @array_filter(explode(',',$proposal->files));

          if (($key = array_search($file, $files)) !== false) {
              unset($files[$key]);
          }

          $files = implode(',', $files); 

          $documents = @$proposal->project->documents()
                       ->where('ffe_proposal_id',$id)->first();
          $docFiles = @$documents->files()->whereFile($file)->delete();             

          $proposal->update(['files' => $files]);

          @unlink($path);

         return redirect()->back()->with('message', 'File Delete Successfully!');
    }
}
