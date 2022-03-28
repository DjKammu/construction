<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Document;
use Gate;


class ITBTrackerController extends Controller
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

         $projects = Project::query();

         $projects = $projects->get(); 

         $projectId = (request()->filled('p')) ? request()->p : $projects->first()->id;

         $project = Project::find($projectId);

         $trades = $project->trades()->get();

         // $trades = $trades->filter(function($trade){
             
         //    $trade->email_sent = false; 
         //    $trade->bid_recieved = false; 
         //    $trade->contract_signed = false; 
         //    return $trade;

         // });
         
         return view('itb_tracker.index',compact('projects','projectId','trades'));
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

        return view('categories.create');
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
              'name' => 'required|unique:categories',
              'account_number' => 'required|unique:categories',
        ]);

        $data['slug'] = \Str::slug($request->name);
            
        Category::create($data);

        // $path = public_path().'/'.Document::PROPERTY.'/' . $data['slug'];
        // \File::makeDirectory($path, $mode = 0777, true, true);

        return redirect('categories')->with('message', 'Category Created Successfully!');
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

         $type = Category::find($id);
         return view('categories.edit',compact('type'));
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
              'name' => 'required|unique:categories,name,'.$id,
              'account_number' => 'required|unique:categories,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);
         
         $category = Category::find($id);
         $slug = $data['slug'];
         $oldSlug = $category->slug;
        
         if(!$category){
            return redirect()->back();
         }
          

        // if($slug != $oldSlug)
        //  {
        //    $path = public_path().'/'.Document::PROPERTY;
        //    @rename($path.$oldSlug, $path.$slug);
        //  }

         $category->update($data);

        return redirect('categories')->with('message', 'Category Updated Successfully!');
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

         $category = Category::find($id);
         // $path = public_path().'/'. Document::PROPERTY.'/'; 
         // @\File::copyDirectory($path.$project_type->slug, $path.Document::ARCHIEVED);
         // @\File::deleteDirectory($path.$project_type->slug);

         $category->delete();       

        return redirect()->back()->with('message', 'Category Delete Successfully!');
    }
}
