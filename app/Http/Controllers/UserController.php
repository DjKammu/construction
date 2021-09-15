<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
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
         $users = User::paginate((new User)->perPage); 
         return view('users.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $roles = Role::orderBy('name')->pluck('name', 'id');
          return view('users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');

        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|exists:roles,id', // validating role
        ]);

        $data['password'] = \Hash::make($data['password']);
     
        $user = User::create($data);

        $user->roles()->sync($data['role']);


        return redirect('users')->with('message', 'User Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $user = User::find($id);
         $roles = Role::orderBy('name')->pluck('name', 'id');
         $selectedRole = ($uRoles = $user->roles()->first()) ? $uRoles->id : 0;
         return view('users.edit',compact('user','roles','selectedRole'));
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

        $data = $request->except(['_token','password','password_confirmation']);
    
        $request->validate([
              'name' => 'required|max:255',
              'email' => 'required|email|max:255|unique:users,id,:id',
              'password' => 'nullable|sometimes|min:6|confirmed|required_with:password',
              'role' => 'required|exists:roles,id', // validating role
        ]);
       
        ($request->filled('password')) ? $data['password'] = \Hash::make($request->password) 
        : '';

         $user = User::find($id);

         if(!$user){
            return redirect()->back();
         }
          
        $user->update($data);

        $user->roles()->sync($data['role']);
          
        return redirect('users')->with('message', 'User Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         User::find($id)->delete();

        return redirect()->back()->with('message', 'User Deleted Successfully!');
    }
}
