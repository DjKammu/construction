<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FFECategory;
use App\Models\Document;
use Gate;


class FFECategoryController extends Controller
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

         $categories = FFECategory::orderBy('account_number');

         $categories = $categories->paginate((new FFECategory())->perPage); 
         
         return view('ffe_categories.index',compact('categories'));
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

        return view('ffe_categories.create');
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
              'name' => 'required|unique:f_f_e_categories',
              'account_number' => 'required|unique:f_f_e_categories',
        ]);

        $data['slug'] = \Str::slug($request->name);
            
        FFECategory::create($data);

        return redirect('ffe/categories')->with('message', 'FFE Category Created Successfully!');
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

         $type = FFECategory::find($id);
         return view('ffe_categories.edit',compact('type'));
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
              'name' => 'required|unique:f_f_e_categories,name,'.$id,
              'account_number' => 'required|unique:f_f_e_categories,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);
         
         $category = FFECategory::find($id);
         $slug = $data['slug'];
         $oldSlug = $category->slug;
        
         if(!$category){
            return redirect()->back();
         }

         $category->update($data);

        return redirect('ffe/categories')->with('message', 'FFE Category Updated Successfully!');
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

         $category = FFECategory::find($id);

         $category->delete();       

        return redirect()->back()->with('message', 'FFE Category Delete Successfully!');
    }
}
