<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignee;
use Gate;


class AssigneeController extends Controller
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

         $assignees = Assignee::query();

         $orderBy = 'account_number';  
         $order ='ASC' ;
         
         if(request()->filled('order')){
            $orderBy = request()->filled('orderby') ? ( !in_array(request()->orderby, 
                ['account_number','name'] ) ? 'name' : request()->orderby ) : 'name';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
             : request()->order;
         }
        
         $assignees = $assignees->orderBy($orderBy,$order)->paginate((new Assignee())->perPage); 
         
         return view('assignees.index',compact('assignees'));
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

        return view('assignees.create');
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
              'name' => 'required|unique:assignees',
              'account_number' => 'required|unique:assignees',
        ]);

        $data['slug'] = \Str::slug($request->name);
            
        Assignee::create($data);

        // $path = public_path().'/'.Document::PROJECT.'/' . $data['slug'];
        // \File::makeDirectory($path, $mode = 0777, true, true);

        return redirect('assignees')->with('message', 'Assignee Created Successfully!');
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

         $assignee = Assignee::find($id);
         return view('assignees.edit',compact('assignee'));
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
              'name' => 'required|unique:assignees,name,'.$id,
              'account_number' => 'required|unique:assignees,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);
         
         $assignee = Assignee::find($id);
         $slug = $data['slug'];
         $oldSlug = $assignee->slug;
        
         if(!$assignee){
            return redirect()->back();
         }
          
         $assignee->update($data);

        return redirect('assignees')->with('message', 'Assignee Updated Successfully!');
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

         $assignee = Assignee::find($id);

         $assignee->delete();       

        return redirect()->back()->with('message', 'Assignee Delete Successfully!');
    }
}
