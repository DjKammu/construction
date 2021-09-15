<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\Document;
use App\Models\ProprtyType;
use App\Models\Property;
use App\Models\Tenant;
use Gate;


class PropertyController extends Controller
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

         $properties = Property::query();

         if(request()->filled('s')){
            $searchTerm = request()->s;
            $properties->where('property_name', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('property_address', 'LIKE', "%{$searchTerm}%")
            ->orWhere('city', 'LIKE', "%{$searchTerm}%")
            ->orWhere('state', 'LIKE', "%{$searchTerm}%")
            ->orWhere('country', 'LIKE', "%{$searchTerm}%")
            ->orWhere('zip_code', 'LIKE', "%{$searchTerm}%")
            ->orWhere('notes', 'LIKE', "%{$searchTerm}%");
         }  

         if(request()->filled('p')){
            $p = request()->p;
            $properties->whereHas('proprty_type', function($q) use ($p){
                $q->where('slug', $p);
            });
         } 
         
         $propertyTypes = ProprtyType::all(); 

         $perPage = request()->filled('per_page') ? request()->per_page : (new Property())->perPage;

         $properties = $properties->paginate($perPage);

         return view('properties.index',compact('properties','propertyTypes'));
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

        $propertyTypes = ProprtyType::all(); 

        return view('properties.create',compact('propertyTypes'));
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
              'property_name' => 'required|unique:properties',
              'proprty_type_id' => 'required|exists:proprty_types,id'
        ]);

        $slug = \Str::slug($request->property_name);

        $data['photo'] = '';    

        if($request->hasFile('photo')){
               $photo = $request->file('photo');
               $photoName = $slug.'-'.time() . '.' . $photo->getClientOriginalExtension();
              
               $data['photo']  = $request->file('photo')->storeAs('properties', $photoName, 'public');
        }

        $property = Property::create($data);

        $proprty_type = ProprtyType::find($data['proprty_type_id']);

        $property->proprty_type()->associate($proprty_type);

        $property->save();

        $path = public_path().'/property/' . $proprty_type->slug.'/'.$slug;
        \File::makeDirectory($path, $mode = 0777, true, true);

        return redirect('properties')->with('message', 'Property Created Successfully!');
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

         $propertyTypes = ProprtyType::all();
         $property = Property::find($id);
         $documentTypes = DocumentType::all();
         $documents = $property->documents();
         $tenants = Tenant::all();

         if(request()->filled('s')){
            $searchTerm = request()->s;
            $documents->where('name', 'LIKE', "%{$searchTerm}%") 
            ->orWhere('slug', 'LIKE', "%{$searchTerm}%");
         }  

         if(request()->filled('document_type')){
                $document_type = request()->document_type;
                $documents->whereHas('document_type', function($q) use ($document_type){
                    $q->where('slug', $document_type);
                });
         }

         if(request()->filled('tenant')){
                $tenant = request()->tenant;
                $documents->where('tenant_id', $tenant);
         } 
              
         $perPage = request()->filled('per_page') ? request()->per_page : (new Property())->perPage;

         $documents = $documents->with('document_type')
                    ->paginate($perPage);

        $documents->filter(function($doc){

            $property = $doc->property()->first(); 

            $property_slug = \Str::slug($property->property_name);

            $document_type = $doc->document_type()->pluck('slug')->first();

            $property_type_slug = @ProprtyType::find($property->proprty_type_id)->slug;

             $folderPath = Document::PROPERTY."/";

            $property_type_slug = ($property_type_slug) ? $property_type_slug : Document::ARCHIEVED;

            $folderPath .= "$property_type_slug/$property_slug/$document_type/";
            
            $files = $doc->files();

            $file =  ($files->count() == 1) ? $files->pluck('file')->first() : '';

            $doc->file = ($file  ? asset($folderPath.$file) : '') ;

            return $doc->file;
           
         });


         return view('properties.edit',compact('propertyTypes','property',
            'documentTypes','documents','tenants'));
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
              'property_name' => 'required|unique:properties,property_name,'.$id,
              'proprty_type_id' => 'required|exists:proprty_types,id'
       ]);

        $slug = \Str::slug($request->property_name);
         
        $property = Property::find($id);
        $oldSlug = \Str::slug($property->property_name);

        if(!$property){
            return redirect()->back();
        }

        $data['photo'] = $property->photo;    


        if($request->hasFile('photo')){
               $photo = $request->file('photo');
               $photoName = $slug.'-'.time() . '.' . $photo->getClientOriginalExtension();
              
               $data['photo']  = $request->file('photo')->storeAs('properties', $photoName, 'public');
        }
        
        $oldProprty_type = ProprtyType::find($property->proprty_type_id);
        $proprty_type = ProprtyType::find($request->proprty_type_id);

        if(!$oldProprty_type){

                $public_path = public_path().'/'.Document::PROPERTY.'/';
                $folderPath =  $proprty_type->slug.'/'.$oldSlug;
                $oldFolderPath = Document::ARCHIEVED.'/'.$oldSlug; 
               \File::copyDirectory($public_path.$oldFolderPath,$public_path.$folderPath); 
               \File::deleteDirectory($public_path.$oldFolderPath);
               
               if($slug  != $oldSlug){
                  $path = public_path().'/'.Document::PROPERTY.'/'.@$proprty_type->slug.'/';
                  @rename($path.$oldSlug, $path.$slug); 
               }

        }
        elseif((@$oldProprty_type->id != $request->proprty_type_id) || 
            ($slug != $oldSlug)){
             
             if($slug  != $oldSlug){
                 $path = public_path().'/'.Document::PROPERTY.'/'.@$oldProprty_type->slug.'/';
                 @rename($path.$oldSlug, $path.$slug); 
             }


             if(@$oldProprty_type->id != $request->proprty_type_id)
             { 
               $path = public_path().'/'.Document::PROPERTY.'/';
               $propertyDir  = ($slug  != $oldSlug) ? $slug : $oldSlug;
                \File::copyDirectory($path.@$oldProprty_type->slug.'/'.$propertyDir,
                 $path.$proprty_type->slug.'/'.$propertyDir); 
               \File::deleteDirectory($path.@$oldProprty_type->slug.'/'.$propertyDir);
             }
        }

         $property->update($data);

        return redirect('properties')->with('message', 'Property Updated Successfully!');
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

         $property = Property::find($id);
         $proprty_slug = \Str::slug($property->property_name);
         $proprty_type = @ProprtyType::find($property->proprty_type_id);

         $proprty_type_slug = @$proprty_type->slug;

         $public_path = public_path().'/';

         $folderPath = Document::PROPERTY."/";

         $proprty_type_slug = ($proprty_type_slug) ? $proprty_type_slug : Document::ARCHIEVED; 

         $folderPath .= "$proprty_type_slug/$proprty_slug";

         $path = $public_path.'/'.$folderPath;
          
         $aPath = public_path().'/'. Document::PROPERTY.'/'.Document::ARCHIEVED.'/'. Document::PROPERTIES; 
         
         @\File::makeDirectory($aPath, $mode = 0777, true, true);

         @\File::copyDirectory($path, $aPath.'/'.$proprty_slug);

         @\File::deleteDirectory($path);

         $property->delete();

        return redirect()->back()->with('message', 'Property Delete Successfully!');
    }
}
