<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Gate;

class RoleController extends Controller
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
         //  if(Gate::denies('view')) {
         //       return abort('401');
         // }

         $roles = Role::all(); 
         return view('roles.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if(Gate::denies('add')) {
        //        return abort('401');
        // } 

        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if(Gate::denies('add')) {
        //        return abort('401');
        // } 

        $data = $request->except('_token');

        $request->validate([
              'name' => 'required',
              'permissions' => 'required|array',
        ]);

        $data['slug'] = \Str::slug($request->name);

        $data['permissions'] = @implode(",",$request->permissions);
     
        Role::create($data);

        return redirect('roles')->with('message', 'Role Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
          // if(Gate::denies('edit')) {
          //      return abort('401');
          // } 

         $role = Role::find($id);
         return view('roles.edit',compact('role'));
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
        // if(Gate::denies('update')) {
        //        return abort('401');
        //   } 

        $data = $request->except('_token');

        $request->validate([
              'name' => 'required',
        ]);

        $data['slug'] = \Str::slug($request->name);

        $data['permissions'] = @implode(",",$request->permissions);

         $role = Role::find($id);

         if(!$role){
            return redirect()->back();
         }
          
        $role->update($data);
          
        return redirect('roles')->with('message', 'Role Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //  if(Gate::denies('delete')) {
        //        return abort('401');
        // } 

         Role::find($id)->delete();

        return redirect()->back()->with('message', 'Role Deleted Successfully!');
    }
}
