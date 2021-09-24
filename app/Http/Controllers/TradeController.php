<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Trade;
use Gate;


class TradeController extends Controller
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

         $trades = Trade::orderBy('account_number');

         $trades = $trades->paginate((new Trade())->perPage); 
         
         return view('trades.index',compact('trades'));
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

        $categories =  Category::all();

        return view('trades.create',compact('categories'));
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
              'name' => 'required|unique:trades',
              'account_number' => 'required|unique:trades',
        ]);

        $data['slug'] = \Str::slug($request->name);


        if($request->hasFile('scope')){
           $scope = $request->file('scope');
           $scopeName = $data['slug'].'-'.time() . '.' . $scope->getClientOriginalExtension();
           $data['scope']  = $request->file('scope')->storeAs(Trade::TRADES, 
            $scopeName, 'public');
        }
            
        Trade::create($data);

        return redirect('trades')->with('message', 'Bussiness Type Created Successfully!');
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

         $trade = Trade::find($id);
         $categories =  Category::all();
         
         return view('trades.edit',compact('trade','categories'));
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
              'name' => 'required|unique:trades,name,'.$id,
              'account_number' => 'required|unique:trades,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);

        $trade = Trade::find($id);

        if($request->hasFile('scope')){
           $scope = $request->file('scope');
           $scopeName = $data['slug'].'-'.time() . '.' . $scope->getClientOriginalExtension();
           $data['scope']  = $request->file('scope')->storeAs(Trade::TRADES, 
            $scopeName, 'public');

            @unlink('storage/'.$trade->scope);
        }
            

        
         if(!$trade){
            return redirect()->back();
         }
          
         $trade->update($data);

        return redirect('trades')->with('message', 'Trade Updated Successfully!');
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
         $trade = Trade::find($id);

         @unlink('storage/'.$trade->scope);

         $trade->delete();

        return redirect()->back()->with('message', 'Trade Delete Successfully!');
    }
}
