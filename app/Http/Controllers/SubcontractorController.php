<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subcontractor;
use App\Models\DocumentType;
use App\Models\ITBTracker;
use App\Models\Proposal;
use App\Models\Document;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Trade;
use App\Models\Bill;
use Gate;


class SubcontractorController extends Controller
{

    CONST DOC_UPLOAD = 'doc_upload';

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

         $subcontractors = Subcontractor::orderBy('name');

         if(request()->filled('s')){
            $searchTerm = request()->s;
            $subcontractors->where('name', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('office_phone', 'LIKE', "%{$searchTerm}%")
            ->orWhere('contact_name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('email_1', 'LIKE', "%{$searchTerm}%")
            ->orWhere('email_3', 'LIKE', "%{$searchTerm}%")
            ->orWhere('mobile', 'LIKE', "%{$searchTerm}%")
            ->orWhere('state', 'LIKE', "%{$searchTerm}%")
            ->orWhere('slug', 'LIKE', "%{$searchTerm}%")
            ->orWhere('city', 'LIKE', "%{$searchTerm}%")
            ->orWhere('zip', 'LIKE', "%{$searchTerm}%")
            ->orWhere('notes', 'LIKE', "%{$searchTerm}%");
         }  

        if(request()->filled('t')){
            $t = request()->t;
            $subcontractors->whereHas('trades', function($q) use ($t){
                $q->where('slug', $t);
            });
         } 

         $perPage = request()->filled('per_page') ? request()->per_page : (new Subcontractor())->perPage;

         $subcontractors = $subcontractors->paginate($perPage);

         $trades = Trade::all();

         return view('subcontractors.index',compact('subcontractors','trades'));
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

        $trades = Trade::all(); 

        return view('subcontractors.create',compact('trades'));
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
              'name' => 'required|unique:subcontractors'
        ]);

        $data['slug'] =  $slug =  \Str::slug($request->name);

        $data['image'] = '';    

        if($request->hasFile('image')){
               $image = $request->file('image');
               $imageName = $slug.'-'.time() . '.' . $image->getClientOriginalExtension();
              
               $data['image']  = $request->file('image')->storeAs(Subcontractor::SUBCONTRACTORS, 
                $imageName, 'public');
        }

        $subcontractor = Subcontractor::create($data);
        
        if($request->filled('trades')){            
           $subcontractor->trades()->sync($request->trades);
        } 


        // $path = public_path().'/property/' . $proprty_type->slug.'/'.$slug;
        // \File::makeDirectory($path, $mode = 0777, true, true);

        return redirect('subcontractors')->with('message', 'Subcontractor Created Successfully!');
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

         $subcontractor = Subcontractor::with('trades')->find($id);
         $trades = Trade::all();   
        
         return view('subcontractors.edit',compact('subcontractor','trades'));
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
              'name' => 'required|unique:subcontractors,name,'.$id
       ]);

        $date['slug'] = $slug = \Str::slug($request->name);
         
        $subcontractor = Subcontractor::find($id);
       
        if(!$subcontractor){
            return redirect()->back();
        }

        $data['image'] = $subcontractor->image;    


        if($request->hasFile('image')){
               $image = $request->file('image');
               $imageName = $slug.'-'.time() . '.' . $image->getClientOriginalExtension();
              
               $data['image']  = $request->file('image')->storeAs(Subcontractor::SUBCONTRACTORS, 
                $imageName, 'public');

                @unlink('storage/'.$subcontractor->image);
        }

        $subcontractor->update($data);
        
        $subcontractor->trades()->sync($request->trades,false); 

        if($request->filled(['with','what'])){
            $this->replceTrade($subcontractor);
        }


        // Proposal::where('subcontractor_id', $id)
        //          ->whereNotIn('trade_id',$request->trades)
        //          ->delete();    
 
        return redirect('subcontractors')->with('message','Subcontractor Updated Successfully!');
    }

     public function replceTrade($subcontractor){
         $id = $subcontractor->id;
         $request = request();
         $what = $request->what;
         $with = $request->with;
         $where = ['subcontractors_id' => $id, 
                    'trade_id' => $what];
         $update = ['trade_id' => $with];

         $proposalQuery = @Proposal::where(['subcontractor_id' => $id, 
                    'trade_id' => $what]);

         $project_ids = $proposalQuery->pluck('project_id');

         $proposal_ids = $proposalQuery->pluck('id');
         
         $subcontractor->trades()->detach($what); 
         $subcontractor->trades()->attach($with); 

         ITBTracker::where($where)
                 ->update($update); 
         $where = ['subcontractor_id' => $id, 
                    'trade_id' => $what];        
         Proposal::where($where)
                 ->update($update); 

         foreach ($project_ids as $key => $pr) {
                $project = Project::find($pr);
                @$project->trades()->sync($with,false);
         }   
         
         $public_path = public_path().'/';

         $whatTrade = Trade::find($what);
         $withTrade = Trade::find($with);

         foreach ($proposal_ids as $key => $pr) {

            $proposal = Proposal::find($pr);

            $project_slug = \Str::slug(@$proposal->project->name);

            $files = $proposal->files;

            $files = @array_filter(explode(',',$files));

            $dir = Document::PROPOSALS;
            $folderPath = $dir.'/'.$project_slug.'/'.$withTrade->slug;
            $oldFolderPath = $dir.'/'.$project_slug.'/'.$whatTrade->slug;

           \File::makeDirectory($folderPath, $mode = 0777, true, true);

            foreach ($files as $k => $file) {
                   @\File::copy($public_path.$oldFolderPath.'/'.$file,$public_path.$folderPath.'/'.$file); 
                    @\File::delete($public_path.$oldFolderPath.'/'.$file);
            }
         }

         $paymentQry = Payment::where($where);  
         $payment_ids = $paymentQry->pluck('id');
         $paymentQry->update($update);

         foreach ($payment_ids as $key => $pr) {

            $payment = Payment::find($pr);

            $project_slug = \Str::slug(@$payment->project->name);

            $file = $payment->file;
            $conditional_lien_release_file = $payment->conditional_lien_release_file;
            $unconditional_lien_release_file = $payment->unconditional_lien_release_file;

            $dir = Document::INVOICES;
            $folderPath = $dir.'/'.$project_slug.'/'.$withTrade->slug;
            $oldFolderPath = $dir.'/'.$project_slug.'/'.$whatTrade->slug;

           \File::makeDirectory($folderPath, $mode = 0777, true, true);

            @\File::copy($public_path.$oldFolderPath.'/'.$file,$public_path.$folderPath.'/'.$file); 
            @\File::delete($public_path.$oldFolderPath.'/'.$file);

            $dir = Document::LIEN_RELEASES;
            $folderPath = $dir.'/'.$project_slug.'/'.$withTrade->slug;
            $oldFolderPath = $dir.'/'.$project_slug.'/'.$whatTrade->slug;

           \File::makeDirectory($folderPath, $mode = 0777, true, true);

            @\File::copy($public_path.$oldFolderPath.'/'.$conditional_lien_release_file,$public_path.$folderPath.'/'.$conditional_lien_release_file); 
            @\File::copy($public_path.$oldFolderPath.'/'.$unconditional_lien_release_file,$public_path.$folderPath.'/'.$unconditional_lien_release_file); 
            @\File::delete($public_path.$oldFolderPath.'/'.$conditional_lien_release_file);
            @\File::delete($public_path.$oldFolderPath.'/'.$unconditional_lien_release_file);

         }


        $billQuery = Bill::where($where);

        $bill_ids = $billQuery->pluck('id');  

        $billQuery->update($update);

         foreach ($bill_ids as $key => $pr) {

            $bill = Bill::find($pr);

            $project_slug = \Str::slug(@$bill->project->name);

            $file = $bill->file;

            $dir = Document::BILLS;
            $folderPath = $dir.'/'.$project_slug.'/'.$withTrade->slug;
            $oldFolderPath = $dir.'/'.$project_slug.'/'.$whatTrade->slug;

           \File::makeDirectory($folderPath, $mode = 0777, true, true);

            @\File::copy($public_path.$oldFolderPath.'/'.$file,$public_path.$folderPath.'/'.$file); 
            @\File::delete($public_path.$oldFolderPath.'/'.$file);

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

        $subcontractor = Subcontractor::find($id);

         @unlink('storage/'.$subcontractor->image);

         $subcontractor->delete();

        return redirect()->back()->with('message', 'Subcontractor Delete Successfully!');
    }
}
