<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectPropertyGroup;
use App\Models\PropertyGroup;
use App\Models\DocumentType;
use App\Models\Proposal;
use App\Models\PropertyType;
use Gate;


class PropertyGroupController extends Controller
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

         $property_groups = PropertyGroup::query();

         $orderBy = 'account_number';  
         $order ='ASC' ;
         
         if(request()->filled('order')){
            $orderBy = request()->filled('orderby') ? ( !in_array(request()->orderby, 
                ['account_number','name'] ) ? 'name' : request()->orderby ) : 'name';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
             : request()->order;
        }
        
         if(request()->filled('s')){
            $searchTerm = request()->s;
            $property_groups->where('name', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('account_number', 'LIKE', "%{$searchTerm}%");
         }  

     

         $perPage = request()->filled('per_page') ? request()->per_page : (new PropertyGroup())->perPage;

         $property_groups = $property_groups->orderBy($orderBy,$order)->paginate($perPage);

         return view('property_groups.index',compact('property_groups'));
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

        $properties = PropertyType::whereNull('property_group_id')->get(); 

        return view('property_groups.create',compact('properties'));
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
              'name' => 'required|unique:property_groups',
              'account_number' => 'sometimes|unique:property_groups',
        ]);

        $data['slug'] =  $slug =  \Str::slug($request->name);

        $propertyGroup = PropertyGroup::create($data);
        
        if($request->filled('properties')){
            $properties = $request->properties;
            PropertyType::whereIn('id',$properties)
             ->update(['property_group_id' => $propertyGroup->id]);
        } 

        return redirect('property-groups')->with('message', 'Property Group Created Successfully!');
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

         $propertyGroup = PropertyGroup::find($id);
         $properties = PropertyType::whereNull('property_group_id')
                        ->orWhere('property_group_id', $id)->get();             
        
         return view('property_groups.edit',compact('propertyGroup','properties'));
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
              'name' => 'required|unique:property_groups,name,'.$id,
              'account_number' => 'sometimes|unique:property_groups,account_number,'.$id,
        ]);


        $date['slug'] = $slug = \Str::slug($request->name);
         
        $propertyGroup = PropertyGroup::find($id);
       
        $propertyGroup->update($data);
        
       if($request->filled('properties')){
        $properties = $request->properties;
             PropertyType::whereIn('id',$properties)
             ->update(['property_group_id' => $propertyGroup->id]);
        }else{
            $propertyGroup->properties()->update(['property_group_id' => null]);
        }  


 
        return redirect('property-groups')->with('message','Property Group Updated Successfully!');
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

         $propertyGroup = PropertyGroup::find($id);
         $propertyGroup->delete();

        return redirect()->back()->with('message', 'Property Group Delete Successfully!');
    }
}
