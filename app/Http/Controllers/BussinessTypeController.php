<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProprtyType;
use App\Models\BussinessType;
use Gate;


class BussinessTypeController extends Controller
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

         $bussinessTypes = BussinessType::orderBy('account_number');

         $bussinessTypes = $bussinessTypes->paginate((new BussinessType())->perPage); 
         
         return view('bussiness_types.index',compact('bussinessTypes'));
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

        return view('bussiness_types.create');
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
              'name' => 'required|unique:bussiness_types',
              'account_number' => 'required|unique:bussiness_types',
        ]);

        $data['slug'] = \Str::slug($request->name);
            
        BussinessType::create($data);

        return redirect('bussiness-types')->with('message', 'Bussiness Type Created Successfully!');
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

         $type = BussinessType::find($id);
         return view('bussiness_types.edit',compact('type'));
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
              'name' => 'required|unique:bussiness_types,name,'.$id,
              'account_number' => 'required|unique:bussiness_types,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);

        $type = BussinessType::find($id);
        
         if(!$type){
            return redirect()->back();
         }
          
         $type->update($data);

        return redirect('bussiness-types')->with('message', 'Bussiness Type Updated Successfully!');
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

         BussinessType::find($id)->delete();

        return redirect()->back()->with('message', 'Bussiness Type Delete Successfully!');
    }
}
