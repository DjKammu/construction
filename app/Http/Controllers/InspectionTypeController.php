<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InspectionType;
use App\Models\Document;
use Gate;


class InspectionTypeController extends Controller
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

         $inspetionTypes = InspectionType::query();

         $orderBy = 'account_number';  
         $order ='ASC' ;
         
         if(request()->filled('order')){
            $orderBy = request()->filled('orderby') ? ( !in_array(request()->orderby, 
                ['account_number','name'] ) ? 'name' : request()->orderby ) : 'name';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
             : request()->order;
        }
        
         $inspetionTypes = $inspetionTypes->orderBy($orderBy,$order)->paginate((new InspectionType())->perPage); 
         
         return view('inspection_types.index',compact('inspetionTypes'));
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

        return view('inspection_types.create');
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
              'name' => 'required|unique:inspection_types',
              'account_number' => 'required|unique:inspection_types',
        ]);

        $data['slug'] = \Str::slug($request->name);
            
        InspectionType::create($data);
        return redirect('inspection-types')->with('message', 'Inspection Type Created Successfully!');
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

         $type = InspectionType::find($id);
         return view('inspection_types.edit',compact('type'));
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
              'name' => 'required|unique:inspection_types,name,'.$id,
              'account_number' => 'required|unique:inspection_types,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);
         
         $type = InspectionType::find($id);
         $slug = $data['slug'];
         $oldSlug = $type->slug;
        
         if(!$type){
            return redirect()->back();
         }
         

         $type->update($data);

        return redirect('inspection-types')->with('message', 'Inspection Type Updated Successfully!');
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

         $inspetionType = InspectionType::find($id);
         $inspetionType->delete();       

        return redirect()->back()->with('message', 'Inspection Type Successfully!');
    }
}
