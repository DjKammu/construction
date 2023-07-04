@extends('layouts.admin-app')

@section('title', 'Soft Cost Proposal')

@section('content')

@include('includes.back',
['url' => route('ffe.index',['project' => request()->project ]) , 'to' => 'To Soft Cost'])


      <!-- Start Main View -->
  <div class="card p-2">
    <div class="row">
        <div class="col-md-12">
              <!-- Start Main View -->
            @if(session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show">
                  <strong>Success!</strong> {{ session()->get('message') }}
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
              </div>
            @endif

             @if ($errors->any())
               <div class="alert alert-warning alert-dismissible fade show">
                 <button type="button" class="close" data-dismiss="alert">&times;</button>
                  <strong>Error!</strong>  
                   {{implode(',',$errors->all() )}}
                </div>
             @endif

            <div class="card-body">
              <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left"> {{ @$project->name }} -  Create Soft Cost Proposal</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.ffe.proposals',['project' => request()->project,'trade' => request()->trade ]) }}" enctype="multipart/form-data">
                                  @csrf

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Soft Cost Vendors
                                                </label>
                                                <select class="form-control" name="f_f_e_vendor_id"> 
                                                  <option value=""> Select Soft Cost Vendors</option>
                                                  @foreach($sc_vendors as $sc_vendor)
                                                   <option value="{{ $sc_vendor->id }}" >{{ $sc_vendor->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div> 
                                    
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password">Labor Cost 
                                                </label>
                                                <input  name="labour_cost" value="{{ old('labour_cost')}}" type="number" class="form-control" placeholder="Labor Cost" step="any">
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password">Material  
                                                </label>
                                                <input  name="material" value="{{ old('material')}}" type="number" class="form-control" placeholder="Material" step="any">
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password">Vendor Price 
                                                </label>
                                                <input  name="subcontractor_price" value="{{ old('subcontractor_price')}}" type="number" class="form-control" placeholder="Vendor Price Cost" step="any">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password">Trade Budget</label>
                                                <input  name="trade_budget" value="{{ old('trade_budget')}}" type="number" class="form-control" placeholder="Trade Budget" step="any">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Notes
                                                </label>
                                                <textarea  name="notes"  type="text" class="form-control" placeholder="Notes" >
                                                 {{ old('notes')}}</textarea>
                                            </div>
                                        </div>
                                    </div> 

                                   <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Files
                                                </label>
                                                <input  name="files[]"  type="file" multiple="">
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Create Soft Cost Proposal
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

@endsection