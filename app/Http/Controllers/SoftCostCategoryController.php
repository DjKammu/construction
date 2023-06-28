<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SoftCostCategory;
use App\Models\Document;
use Gate;


class SoftCostCategoryController extends Controller
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

         $categories = SoftCostCategory::query();

         $orderBy = 'account_number';  
         $order ='ASC' ;
         
         if(request()->filled('order')){
            $orderBy = request()->filled('orderby') ? ( !in_array(request()->orderby, 
                ['account_number','name'] ) ? 'name' : request()->orderby ) : 'name';
            
            $order = !in_array(\Str::lower(request()->order), ['desc','asc'])  ? 'ASC' 
             : request()->order;
        }
        
         $categories = $categories->orderBy($orderBy,$order)->paginate((new SoftCostCategory())->perPage);

         return view('soft_cost_categories.index',compact('categories'));
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

        return view('soft_cost_categories.create');
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
              'name' => 'required|unique:soft_cost_categories',
              'account_number' => 'required|unique:soft_cost_categories',
        ]);

        $data['slug'] = \Str::slug($request->name);
            
        SoftCostCategory::create($data);

        return redirect('soft-cost/categories')->with('message', 'Soft Cost Category Created Successfully!');
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

         $type = SoftCostCategory::find($id);
         return view('soft_cost_categories.edit',compact('type'));
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
              'name' => 'required|unique:soft_cost_categories,name,'.$id,
              'account_number' => 'required|unique:soft_cost_categories,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);
         
         $category = SoftCostCategory::find($id);
         $slug = $data['slug'];
         $oldSlug = $category->slug;
        
         if(!$category){
            return redirect()->back();
         }

         $category->update($data);

        return redirect('soft-cost/categories')->with('message', 'Soft Cost Category Updated Successfully!');
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

         $category = SoftCostCategory::find($id);

         $category->delete();       

        return redirect()->back()->with('message', 'Soft Cost Category Delete Successfully!');
    }
}
