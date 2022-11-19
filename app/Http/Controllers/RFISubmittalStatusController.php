<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RFISubmittalStatus;
use Gate;


class RFISubmittalStatusController extends Controller
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

         $statuses = RFISubmittalStatus::query();

         $orderBy = 'account_number';  
         $order ='ASC' ;
         
         if(request()->filled('order')){
            $orderBy = request()->filled('orderby') ? ( !in_array(request()->orderby, 
                ['account_number','name'] ) ? 'name' : request()->orderby ) : 'name';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
             : request()->order;
        }
        
         $statuses = $statuses->orderBy($orderBy,$order)->paginate((new RFISubmittalStatus())->perPage); 
         
         return view('rfi_submittal_statuses.index',compact('statuses'));
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

        return view('rfi_submittal_statuses.create');
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
              'name' => 'required|unique:rfi_submittal_statuses',
              'account_number' => 'required|unique:rfi_submittal_statuses',
        ]);

        $data['slug'] = \Str::slug($request->name);
            
        RFISubmittalStatus::create($data);

        return redirect('rfi-submittal/statuses')->with('message', 'RFI/Submittal Status Created Successfully!');
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

         $status = RFISubmittalStatus::find($id);
         return view('rfi_submittal_statuses.edit',compact('status'));
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
              'name' => 'required|unique:rfi_submittal_statuses,name,'.$id,
              'account_number' => 'required|unique:rfi_submittal_statuses,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);
         
         $status = RFISubmittalStatus::find($id);
         $slug = $data['slug'];
         $oldSlug = $status->slug;
        
         if(!$status){
            return redirect()->back();
         }
          

         $status->update($data);

        return redirect('rfi-submittal/statuses')->with('message', 'RFI/Submittal Status Updated Successfully!');
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

         $status = RFISubmittalStatus::find($id);

         $status->delete();       

        return redirect()->back()->with('message', 'RFI/Submittal Status Delete Successfully!');
    }
}
