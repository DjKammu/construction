<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Project;
use App\Models\Proposal;
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

         $trades = Trade::query();

         if(request()->filled('s')){
            $searchTerm = request()->s;
            $trades->where('name', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('account_number', 'LIKE', "%{$searchTerm}%");
         } 


         $orderBy = 'account_number';  
         $order ='ASC' ;
         
         if(request()->filled('order')){
            $orderBy = request()->filled('orderby') ? ( !in_array(request()->orderby, 
                ['account_number','name'] ) ? 'name' : request()->orderby ) : 'name';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
             : request()->order;
        }
        
         $trades = $trades->orderBy($orderBy,$order)->paginate((new Trade())->perPage); 
         
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

        $categories =  Category::orderBy('name')->get();

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
              'category_id'    => 'required|exists:categories,id'
        ]);

        $data['slug'] = \Str::slug($request->name);


        if($request->hasFile('scope')){
           $scope = $request->file('scope');
           $scopeName = $data['slug'].'-'.time() . '.' . $scope->getClientOriginalExtension();
           $data['scope']  = $request->file('scope')->storeAs(Trade::TRADES, 
            $scopeName, 'public');
        }
            
        Trade::create($data);

        return redirect('trades')->with('message', 'Trade Created Successfully!');
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
         $categories =  Category::orderBy('name')->get();
         
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
              'category_id'    => 'required|exists:categories,id'
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
         })->orderBy('name')->get();

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
        
        // $project->trades()->attach($request->trade_id); 
        $project->trades()->sync($request->trade_id,false); 

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
