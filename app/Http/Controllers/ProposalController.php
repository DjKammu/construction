<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule; 
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Project;
use App\Models\Trade;
use App\Models\Document;
use App\Models\Proposal;
use Gate;


class ProposalController extends Controller
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

        $trade = Trade::find($trade_id);  
        $project  = Project::find($id);  
         
        if(!$trade || !$project){
            return redirect()->back();
        }

        $subcontractors = @$trade->subcontractors()
                  ->whereDoesntHave('proposals', function($q) use($trade_id){
                    $q->where("trade_id",$trade_id);
                  })->get();

        return view('projects.includes.proposals-create',compact('subcontractors'));
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
        
        $trade = Trade::find($trade_id);  
        $project  = Project::find($id);  
         
        if(!$trade || !$project){
            return redirect('/');
        }

        $data = $request->except('_token');

        $proposal = Proposal::where('subcontractor_id',$request->subcontractor_id)
              ->where(
                function($query) use ($id,$trade_id){
                    (new Proposal)->scopeHaveProposal($query,$id,$trade_id);
              })->exists();
        

       if($proposal){
         return redirect(route('projects.show',['project' => $id]).'?trade='.$trade_id.'#proposals')->withErrors(['Already Proposal exists for this subcontractor!']);
       }

        $request->validate([
              'subcontractor_id' => ['required',
              'exists:subcontractors,id'],
              'labour_cost' => 'required',
              'material' => 'required',
              'subcontractor_price' => 'required'
          ]
      );

        $data['project_id'] = $id;
        $data['trade_id'] = $trade_id;

        Proposal::create($data);

        return redirect(route('projects.show',['project' => $id]).'?trade='.$trade_id.'#proposals')->with('message', 'Proposal Created Successfully!');
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
        $proposal = Proposal::find($id);  
        $subcontractor = @$proposal->subcontractor;
        return view('projects.includes.proposals-edit',compact('subcontractor','proposal'));
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
              'labour_cost' => 'required',
              'material' => 'required',
              'subcontractor_price' => 'required'
          ]
       );

        $proposal = Proposal::find($id);

        $proposal->update($data);

        return redirect(route('projects.show',['project' => $proposal->project_id]).'?trade='.$proposal->trade_id.'#proposals')->with('message', 'Proposal Updated Successfully!');
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

         $proposal = Proposal::find($id);

         $project = @$proposal->project;

        $project_slug = \Str::slug($project->name);

        $project_type = @$project->project_type;

        $project_type_slug = @$project_type->slug; 

        $trade_slug = @\Str::slug($proposal->trade->name);

        $public_path = public_path().'/';

        $folderPath = Document::PROPOSALS."/";

        $project_type_slug = ($project_type_slug) ? $project_type_slug : Document::ARCHIEVED;

         $folderPath .= "$project_type_slug/$project_slug/$trade_slug/";

         $path = @public_path().'/'.$folderPath;

         $files = @explode(',',$proposal->files);

         // $aPath = public_path().'/'. Document::PROPOSALS."/".Document::ARCHIEVED.'/'. Document::DOCUMENTS; 
         // \File::makeDirectory($aPath, $mode = 0777, true, true);
         
         foreach (@$files as $key => $file) {
            // @\File::copy($path.$file->file, $aPath.'/'.$file->file);
            @unlink($path.$file);
         }

         $proposal->delete();

        return redirect()->back()->with('message', 'Proposal Delete Successfully!');
    }



      /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function award($id, $award)
    {
        if(Gate::denies('update')) {
               return abort('401');
         } 
         
        $proposal = Proposal::find($id);

        $isAwarded = $proposal->HaveProposal($proposal->project_id, $proposal->trade_id)
                    ->IsAwarded()->exists();

         if($isAwarded && ($award == Proposal::RETRACTED)){
           return redirect(route('projects.show',['project' => $proposal->project_id]).'#proposals')->withErrors('Proposal already awarded for this trade!');
         }

       $award =  ($award == Proposal::AWARDED) ? Proposal::RETRACTED : Proposal::AWARDED;

       $proposal->update(['awarded' => $award]);
        
       $awardMsg = ($award == Proposal::AWARDED) ? Proposal::AWARDED_TEXT : Proposal::RETRACTED_TEXT ; 

      return redirect(route('projects.show',['project' => $proposal->project_id]).'?trade='.$proposal->trade_id.'#proposals')->with('message', 'Proposal '.$awardMsg.' 
        Successfully!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request,$id)
    {
          if(Gate::denies('update')) {
               return abort('401');
        } 

        $data = $request->except('_token');

        $proposal = Proposal::find($id);

        $project = @$proposal->project;

        $project_slug = \Str::slug($project->name);

        $project_type = @$project->project_type;

        $project_type_slug = @$project_type->slug; 

        $trade_slug = @\Str::slug($proposal->trade->name);

        $public_path = public_path().'/';

        $folderPath = Document::PROPOSALS."/";

        $project_type_slug = ($project_type_slug) ? $project_type_slug : Document::ARCHIEVED;

        $folderPath .= $project_type_slug.'/'.$project_slug.'/'.$trade_slug;
        

        \File::makeDirectory($public_path.$folderPath, $mode = 0777, true, true);

        if($request->hasFile('file')){
             $filesArr = @array_filter(explode(',',$proposal->files));

             $file = $request->file('file');
             $fileName = $trade_slug.'-'.time().'.'. $file->getClientOriginalExtension();
              $file->storeAs($folderPath, $fileName, 'doc_upload');

            $filesArr[] = $fileName; 
        
            $proposal->update(['files' => implode(',',$filesArr)]);
        }


        return redirect(route('projects.show',['project' => $proposal->project_id]).'?trade='.$proposal->trade_id.'#proposals')->with('message', 'File added Successfully!');
    }

}
