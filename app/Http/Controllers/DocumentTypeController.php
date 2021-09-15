<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProprtyType;
use App\Models\DocumentType;
use Gate;


class DocumentTypeController extends Controller
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

         $documentTypes = DocumentType::orderBy('account_number');

         $documentTypes = $documentTypes->paginate((new DocumentType())->perPage); 
         
         return view('document_types.index',compact('documentTypes'));
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

        return view('document_types.create');
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
              'name' => 'required|unique:document_types',
              'account_number' => 'required|unique:document_types',
        ]);

        $data['slug'] = \Str::slug($request->name);
            
        DocumentType::create($data);

        return redirect('document-types')->with('message', 'Document Type Created Successfully!');
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

         $type = DocumentType::find($id);
         return view('document_types.edit',compact('type'));
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
              'name' => 'required|unique:document_types,name,'.$id,
              'account_number' => 'required|unique:document_types,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);

        $type = DocumentType::find($id);
        
         if(!$type){
            return redirect()->back();
         }
          
         $type->update($data);

        return redirect('document-types')->with('message', 'Document Type Updated Successfully!');
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

         DocumentType::find($id)->delete();

        return redirect()->back()->with('message', 'Document Type Delete Successfully!');
    }
}
