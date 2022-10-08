<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FFECategory;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\FFETrade;
use Gate;


class FFETradeController extends Controller
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

         $trades = FFETrade::orderBy('account_number');

         $trades = $trades->paginate((new FFETrade())->perPage); 
         
         return view('ffe_trades.index',compact('trades'));
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

        $categories =  FFECategory::all();

        return view('ffe_trades.create',compact('categories'));
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
              'name' => 'required|unique:f_f_e_trades',
              'account_number' => 'required|unique:f_f_e_trades',
              'category_id'    => 'required|exists:f_f_e_categories,id'
        ]);

        $data['slug'] = \Str::slug($request->name);


        if($request->hasFile('scope')){
           $scope = $request->file('scope');
           $scopeName = $data['slug'].'-'.time() . '.' . $scope->getClientOriginalExtension();
           $data['scope']  = $request->file('scope')->storeAs(FFETrade::FEE_TRADES, 
            $scopeName, 'public');
        }
            
        FFETrade::create($data);

        return redirect('ffe/trades')->with('message', 'FFE Trade Created Successfully!');
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

         $trade = FFETrade::find($id);
         $categories =  FFECategory::all();
         
         return view('ffe_trades.edit',compact('trade','categories'));
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
              'name' => 'required|unique:f_f_e_trades,name,'.$id,
              'account_number' => 'required|unique:f_f_e_trades,account_number,'.$id,
              'category_id'    => 'required|exists:f_f_e_categories,id'
        ]);

        $data['slug'] = \Str::slug($request->name);

        $trade = FFETrade::find($id);

        if($request->hasFile('scope')){
           $scope = $request->file('scope');
           $scopeName = $data['slug'].'-'.time() . '.' . $scope->getClientOriginalExtension();
           $data['scope']  = $request->file('scope')->storeAs(FFETrade::FEE_TRADES, 
            $scopeName, 'public');

            @unlink('storage/'.$trade->scope);
        }
            

        
         if(!$trade){
            return redirect()->back();
         }
          
         $trade->update($data);

        return redirect('ffe/trades')->with('message', 'FFE Trade Updated Successfully!');
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
         $trade = FFETrade::find($id);

         @unlink('storage/'.$trade->scope);

         $trade->delete();

        return redirect()->back()->with('message', 'FFE Trade Delete Successfully!');
    }



      /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createProjectTrade($id)
    {
        if(Gate::denies('add')) {
               return abort('401');
         } 
        $project = Project::find($id);

        $trades =  Trade::whereDoesntHave("projects", function($q) use($id){
            $q->where("project_id",$id);
          })->get();

        return view('projects.includes.trades-create',compact('trades','project'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProjectTrade(Request $request,$id)
    {
          if(Gate::denies('add')) {
               return abort('401');
        } 

        $data = $request->except('_token');

        $request->validate([
              'trade_id' => 'required|exists:trades,id'
        ]);
            
        $project = Project::find($id);

        
        if(!$project){
            return redirect()->back();
        }          
        
        $project->trades()->attach($request->trade_id); 

        return redirect(route('projects.show',['project' => $id]).'#trades')->with('message', 'Trade Assigned Successfully!');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMultipleProjectTrade(Request $request,$id)
    {
          if(Gate::denies('add')) {
               return abort('401');
        } 

        $data = $request->except('_token');

        $request->validate([
              'project_id' => 'required|exists:projects,id'
        ]);
            
        $project = Project::find($id);
        
        if(!$project){
            return redirect()->back();
        }  

        $selectedProject = Project::find($data['project_id']);

        $selectedTrades = @$selectedProject->trades()->pluck('trade_id');
   
        @$project->trades()->sync($selectedTrades,false); 

        return redirect(route('projects.show',['project' => $id]).'#trades')->with('message', 'Trades Assigned Successfully!');
    }


      /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyProjectTrade($project_id, $id)
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

         $project = Project::find($project_id);

         $trade = $project->trades()
                  ->where('trade_id',$id)
                  ->firstOrFail()->pivot;     

         @$trade->delete();
         
         Proposal::where([
          ['project_id', $project_id],
          ['trade_id' , $id]
         ])->delete();    

        return redirect(route('projects.show',['project' => $project_id]).'#trades')->with('message', 'Trade Delete Successfully!');
    }
}
