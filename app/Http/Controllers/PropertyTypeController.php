<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyType;
use App\Models\Document;
use Gate;


class PropertyTypeController extends Controller
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

         $propertyTypes = PropertyType::orderBy('account_number');

         $propertyTypes = $propertyTypes->paginate((new PropertyType())->perPage); 
         
         return view('property_types.index',compact('propertyTypes'));
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

        return view('property_types.create');
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
              'name' => 'required|unique:property_types',
              'account_number' => 'required|unique:property_types',
        ]);

        $data['slug'] = \Str::slug($request->name);
            
        PropertyType::create($data);

        // $path = public_path().'/'.Document::PROJECT.'/' . $data['slug'];
        // \File::makeDirectory($path, $mode = 0777, true, true);

        return redirect('property-types')->with('message', 'Property Type Created Successfully!');
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

         $type = PropertyType::find($id);
         return view('property_types.edit',compact('type'));
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
              'name' => 'required|unique:project_types,name,'.$id,
              'account_number' => 'required|unique:project_types,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);
         
         $type = PropertyType::find($id);
         $slug = $data['slug'];
         $oldSlug = $type->slug;
        
         if(!$type){
            return redirect()->back();
         }
          

        // if($slug != $oldSlug)
        //  {
        //    $path = public_path().'/'.Document::PROJECT;
        //    @rename($path.$oldSlug, $path.$slug);
        //  }

         $type->update($data);

        return redirect('property-types')->with('message', 'Property Type Updated Successfully!');
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

         $propertyType = ProjectType::find($id);
         // $path = public_path().'/'. Document::PROJECT.'/'; 
         // @\File::copyDirectory($path.$propertyType->slug, $path.Document::ARCHIEVED);
         // @\File::deleteDirectory($path.$propertyType->slug);

         $propertyType->delete();       

        return redirect()->back()->with('message', 'Property Type Delete Successfully!');
    }
}
