<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subcontractor;
use App\Models\DocumentType;
use App\Models\Proposal;
use App\Models\Trade;
use Gate;


class SubcontractorController extends Controller
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
        
        $subcontractor->trades()->sync($request->trades); 

        Proposal::where('subcontractor_id', $id)
                 ->whereNotIn('trade_id',$request->trades)
                 ->delete();    
 
        return redirect('subcontractors')->with('message','Subcontractor Updated Successfully!');
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
