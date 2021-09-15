<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\ProprtyType;
use App\Models\DocumentType;
use App\Models\Property;
use Auth;
use Gate;

class FileController extends Controller
{
    CONST PROPERTIES = 'properties';
    CONST DOCUMENTS = 'documents';
    CONST PROPERTY = 'property';
    CONST FILES = 'files';
    CONST ARCHIVED = 'archived';
    CONST DOC_UPLOAD = 'doc_upload';

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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, $directory = null)
    {
       $directories = [
        self::PROPERTIES
       ];

       $files = [];
       $breadcrumbs = [];
        
      if($directory == self::PROPERTIES) {
         $directories = $this->getDirectoies(self::PROPERTY);
         $breadcrumbs = [
           ['name' => self::FILES,'link' => self::FILES]
         ];
         return view('files.files',compact('directories','breadcrumbs'));
      } 
      elseif(in_array($directory,$directories)) {
         $files = \Storage::disk('public')->files($directory);
      }
      
      return view('files.index',compact('directories','files')); 

    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function propertyType(Request $request, $directory, $propertyType)
    {
         $directory = self::PROPERTY;
         $files = [];
         $breadcrumbs = [];

         $directories = $this->getDirectoies($directory, true, $directory.'/'.$propertyType);
         
         $breadcrumbs = [
           ['name' => self::FILES,'link' => self::FILES],
           ['name' => self::PROPERTIES,'link' => self::FILES.'/'.self::PROPERTIES]
         ];

         return view('files.files',compact('directories','files','breadcrumbs'));
    
    }


    public function property(Request $request, $directory, $propertyType,$property)
    {
         $directory = self::PROPERTY;
         $removable = false;
         $breadcrumbs = [];
         $files = [];
         $propertyTypeUrl =  self::FILES.'/'.$directory.'/'.$propertyType;

         $directories = $this->getDirectoies($directory, true, $directory.'/'.$propertyType.'/'.$property);
          
           
         if($property == self::DOCUMENTS){
           $files = $directories;
           $removable = true;
         }

         $breadcrumbs = [
           ['name' => self::FILES,'link' => self::FILES],
           ['name' => self::PROPERTIES,'link' => self::FILES.'/'.self::PROPERTIES],
           ['name' => $propertyType,'link' => $propertyTypeUrl],
         ];

         return view('files.files',compact('directories','files','breadcrumbs','removable'));
    
    }

    public function docType(Request $request, $directory, $propertyType,$property,$docType)
    {
         $directory = self::PROPERTY;
         $directories = [];
         $breadcrumbs = [];
         $propertyTypeUrl =  self::FILES.'/'.$directory.'/'.$propertyType;
         $propertyUrl =  self::FILES.'/'.$directory.'/'.$propertyType.'/'.$property;

         $files = $this->getDirectoies($directory, true, $directory.'/'.$propertyType.'/'.$property.'/'.$docType);

         if($propertyType == self::ARCHIVED){
               $directories =  $files;
               $files = [];
         }
         

         $breadcrumbs = [
           ['name' => self::FILES,'link' => self::FILES],
           ['name' => self::PROPERTIES,'link' => self::FILES.'/'.self::PROPERTIES],
           ['name' => $propertyType,'link' => $propertyTypeUrl],
           ['name' => $property,'link' => $propertyUrl],
         ];

         return view('files.files',compact('directories','files','breadcrumbs'));
    
    }

    public function doc(Request $request, $directory, $propertyType,$property,$docType,$doc)
    {
         $directory = self::PROPERTY;
         $directories = [];
         $breadcrumbs = [];
         $propertyTypeUrl =  self::FILES.'/'.$directory.'/'.$propertyType;
         $propertyUrl =  self::FILES.'/'.$directory.'/'.$propertyType.'/'.$property;
         $docTypeUrl =  self::FILES.'/'.$directory.'/'.$propertyType.'/'.$property.'/'.$docType;

         $files = $this->getDirectoies($directory, true, $directory.'/'.$propertyType.'/'.$property.'/'.$docType.'/'.$doc);

         $breadcrumbs = [
           ['name' => self::FILES,'link' => self::FILES],
           ['name' => self::PROPERTIES,'link' => self::FILES.'/'.self::PROPERTIES],
           ['name' => $propertyType,'link' => $propertyTypeUrl],
           ['name' => $property,'link' => $propertyUrl],
           ['name' => $docType,'link' => $docTypeUrl],
         ];
         
         $removable = true;
         
         return view('files.files',compact('directories','files','breadcrumbs','removable'));
    
    }

    public function getDirectoies($dir, $rescurive = false, $dirname = null){

        $directories = \Storage::disk(self::DOC_UPLOAD)->listContents($dir,$rescurive);
 
        if($dirname) {
            $directories = collect($directories)->where('dirname',$dirname)->all();
        }

        return $directories;
    }
    

    public function destroy()
    {
         if(Gate::denies('delete')) {
               return abort('401');
          } 

          $path = request()->path;

          @unlink($path);

         return redirect()->back()->with('message', 'File Delete Successfully!');
    }

}
