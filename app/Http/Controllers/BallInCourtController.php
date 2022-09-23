<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BallInCourt;
use Gate;


class BallInCourtController extends Controller
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

         $ballInCourts = BallInCourt::orderBy('account_number');

         $ballInCourts = $ballInCourts->paginate((new BallInCourt())->perPage); 
         
         return view('ball_in_courts.index',compact('ballInCourts'));
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

        return view('ball_in_courts.create');
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
              'name' => 'required|unique:ball_in_courts',
              'account_number' => 'required|unique:ball_in_courts',
        ]);

        $data['slug'] = \Str::slug($request->name);
            
        BallInCourt::create($data);
        return redirect('ball_in_courts')->with('message', 'Ball In Court Created Successfully!');
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

         $ballInCourt = BallInCourt::find($id);
         return view('ball_in_courts.edit',compact('ballInCourt'));
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
              'name' => 'required|unique:ball_in_courts,name,'.$id,
              'account_number' => 'required|unique:ball_in_courts,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);
         
         $ballInCourt = BallInCourt::find($id);
         $slug = $data['slug'];
         $oldSlug = $ballInCourt->slug;
        
         if(!$ballInCourt){
            return redirect()->back();
         }
          
         $ballInCourt->update($data);

        return redirect('ball_in_courts')->with('message', 'Ball In Court Updated Successfully!');
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

         $ballInCourt = BallInCourt::find($id);

         $ballInCourt->delete();       

        return redirect()->back()->with('message', 'Ball In Court Delete Successfully!');
    }
}
