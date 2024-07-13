<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FFEVendor;
use App\Models\FFETrade;
use Gate;


class FFEVendorController extends Controller
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

         $vendors = FFEVendor::orderBy('name');

         if(request()->filled('s')){
            $searchTerm = request()->s;
            $vendors->where('name', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('contact_name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('email', 'LIKE', "%{$searchTerm}%")
            ->orWhere('state', 'LIKE', "%{$searchTerm}%")
            ->orWhere('slug', 'LIKE', "%{$searchTerm}%")
            ->orWhere('city', 'LIKE', "%{$searchTerm}%")
            ->orWhere('zip', 'LIKE', "%{$searchTerm}%")
            ->orWhere('notes', 'LIKE', "%{$searchTerm}%");
         }  

       
         $perPage = request()->filled('per_page') ? request()->per_page : (new FFEVendor())->perPage;

         $vendors = $vendors->paginate($perPage);

        $trades = FFETrade::orderBy('name')->get();

         return view('ffe_vendors.index',compact('vendors','trades'));
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
        $trades = FFETrade::orderBy('name')->get();
        return view('ffe_vendors.create',compact('trades'));
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
              'name' => 'required|unique:f_f_e_vendors'
        ]);

        $data['slug'] =  $slug =  \Str::slug($request->name);

        $data['photo'] = '';    

        if($request->hasFile('photo')){
               $photo = $request->file('photo');
               $photoName = $slug.'-'.time() . '.' . $photo->getClientOriginalExtension();
              
               $data['photo']  = $request->file('photo')->storeAs(FFEVendor::FFES.'/'.FFEVendor::VENDORS, $photoName, 'public');
        }

        $vendors = FFEVendor::create($data);

         if($request->filled('trades')){            
           $vendors->trades()->sync($request->trades);
        } 


        return redirect('ffe/vendors')->with('message', 'FFE Vendor Created Successfully!');
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

         $vendor = FFEVendor::find($id);
          $trades = FFETrade::orderBy('name')->get();
         return view('ffe_vendors.edit',compact('vendor','trades'));
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
              'name' => 'required|unique:f_f_e_vendors,name,'.$id
       ]);

        $date['slug'] = $slug = \Str::slug($request->name);
         
        $vendor = FFEVendor::find($id);
       
        if(!$vendor){
            return redirect()->back();
        }

        $data['photo'] = $vendor->photo;    


        if($request->hasFile('photo')){
               $photo = $request->file('photo');
               $photoName = $slug.'-'.time() . '.' . $photo->getClientOriginalExtension();
              
               $data['photo']  = $request->file('photo')->storeAs(FFEVendor::FFES.'/'.FFEVendor::VENDORS, $photoName, 'public');

              @unlink('storage/'.$vendor->photo);
        }

        $vendor->update($data);

        $vendor->trades()->sync($request->trades); 
        
 
        return redirect('ffe/vendors')->with('message','FFE Vendor Updated Successfully!');
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

         $vendor = FFEVendor::find($id);

         @unlink('storage/'.$vendor->photo);

         $vendor->delete();

        return redirect()->back()->with('message', 'FFE Vendor Delete Successfully!');
    }
}
