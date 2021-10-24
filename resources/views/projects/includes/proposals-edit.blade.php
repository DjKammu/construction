@extends('layouts.admin-app')

@section('title', 'Proposal')

@section('content')

@include('includes.back')

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
                        <h4 class="mt-0 text-left">  Update Proposal</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.proposals.update',['id' => request()->id]) }}" >
                                  @csrf

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Subcontractors
                                                </label>
                                                <select class="form-control"> 
                                                  <option>
                                                      {{ @$subcontractor->name }}
                                                  </option>
                                                 
                                                </select>
                                            </div>
                                        </div>
                                    </div> 
                                    
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password">
                                                  Labour Cost 
                                                </label>
                                                <input  name="labour_cost" value="{{ $proposal->labour_cost }}" type="number" class="form-control" placeholder="Labour Cost" required="">
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password">Material  
                                                </label>
                                                <input  name="material" value="{{ $proposal->material }}" type="number" class="form-control" placeholder="Material" required="">
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">

                                           <div class="form-group">
                                                <label class="text-dark" for="password">Subcontractor Price 
                                                </label>
                                                <input  name="subcontractor_price" value="{{ $proposal->subcontractor_price }}" type="number" class="form-control" placeholder="Subcontractor Price Cost" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                         <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Notes
                                                </label>
                                                <textarea  name="notes"  type="text" class="form-control" placeholder="Notes" >
                                                 {{ $proposal->notes }}</textarea>
                                            </div>
                                        </div>
                                    </div> 




                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update Proposal
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