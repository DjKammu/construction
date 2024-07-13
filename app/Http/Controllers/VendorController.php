<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\VendorMaterial;
use App\Models\DocumentType;
use App\Models\Trade;
use Gate;


class VendorController extends Controller
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

         $vendors = Vendor::orderBy('name');

         if(request()->filled('s')){
            $searchTerm = request()->s;
            $vendors->where('name', 'LIKE', "%{$searchTerm}%") 
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
            $vendors->whereHas('trades', function($q) use ($t){
                $q->where('slug', $t);
            });
         } 
         

         $perPage = request()->filled('per_page') ? request()->per_page : (new Vendor())->perPage;

         $vendors = $vendors->paginate($perPage);

         return view('vendors.index',compact('vendors'));
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

        return view('vendors.create');
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
              'name' => 'required|unique:vendors'
        ]);

        $data['slug'] =  $slug =  \Str::slug($request->name);

        $vendor = Vendor::create($data);

        return redirect('vendors')->with('message', 'Vendor Created Successfully!');
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

         $vendor = Vendor::find($id);
        
         return view('vendors.edit',compact('vendor'));
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
              'name' => 'required|unique:vendors,name,'.$id
       ]);

        $date['slug'] = $slug = \Str::slug($request->name);
         
        $vendor = Vendor::find($id);
       
        if(!$vendor){
            return redirect()->back();
        }

        $vendor->update($data);

        $materials = $data['materials'];

        $vendorMaterial = $vendor->materials();
        $materialIds = $vendorMaterial->pluck('id')->toArray();
        $inputIds = @$materials['id'] ? $materials['id'] : [];
        $needToDeleted = array_diff($materialIds, $inputIds);
         //dd($needToDeleted);
        @$vendor->materials()->whereIn('id',@$needToDeleted)->delete();

        if(array_filter($materials['account_number']) && count($materials['account_number']) > 0){
            foreach (@array_filter($materials['account_number']) as $key => $account_number) {
             
                $vendorMaterial->updateOrCreate([
                    'id'   => @$materials['id'][$key]
                ],[
                    'account_number'   => $account_number,
                    'name'             => $materials['name'][$key]
                ]); 

            }
          
        }
        
        return redirect('vendors')->with('message','Vendor Updated Successfully!');
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

         $vendor = Vendor::find($id);

         $vendor->delete();

        return redirect()->back()->with('message', 'Vendor Delete Successfully!');
    }

   public function getMaterials(Request $request){
        $vendor_id = $request->vendor_id;
        $all = VendorMaterial::where('vendor_id',$vendor_id)
              ->orderBy('name')->get();
        
        return response()->json($all); 

    }



}
