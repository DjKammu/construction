<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportCompany;
use App\Models\Document;
use Gate;


class ReportCompanyController extends Controller
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

         $reportCompanies = ReportCompany::orderBy('account_number');

         $reportCompanies = $reportCompanies->paginate((new ReportCompany())->perPage); 
         
         return view('report_companies.index',compact('reportCompanies'));
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

        return view('report_companies.create');
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
              'name' => 'required|unique:report_companies',
              'account_number' => 'required|unique:report_companies',
        ]);

        $data['slug'] = \Str::slug($request->name);
            
        ReportCompany::create($data);
        return redirect('report-companies')->with('message', 'Report Company Created Successfully!');
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

         $reportCompany = ReportCompany::find($id);
         return view('report_companies.edit',compact('reportCompany'));
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
              'name' => 'required|unique:report_companies,name,'.$id,
              'account_number' => 'required|unique:report_companies,account_number,'.$id,
        ]);

        $data['slug'] = \Str::slug($request->name);
         
         $reportCompany = ReportCompany::find($id);
         $slug = $data['slug'];
         $oldSlug = $reportCompany->slug;
        
         if(!$reportCompany){
            return redirect()->back();
         }
         

         $reportCompany->update($data);

        return redirect('report-companies')->with('message', 'Report Company Updated Successfully!');
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

         $reportCompany = ReportCompany::find($id);
         $reportCompany->delete();       

        return redirect()->back()->with('message', 'Report Company Successfully!');
    }
}
