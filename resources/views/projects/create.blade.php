@extends('layouts.admin-app')

@section('title', 'Project')

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
                        <h4 class="mt-0 text-left">Add Project</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('projects.store') }}" enctype="multipart/form-data">
                                  @csrf

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" value="{{ old('name')}}" type="text" class="form-control" placeholder="Name" required="">
                                            </div>
                                        </div>
                                    
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Project Type
                                                </label>
                                                <select class="form-control" name="project_type_id"> 
                                                  <option> Select Project Type</option>
                                                  @foreach($projectTypes as $type)
                                                   <option value="{{ $type->id }}" >{{ $type->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    
                                       <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Address
                                                </label>
                                                <textarea  name="address"  type="text" class="form-control" placeholder="Property Address" >
                                                 {{ old('address')}}</textarea>
                                            </div>
                                        </div>
                                   
                                       <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">City 
                                                </label>
                                                <input  name="city" value="{{ old('city')}}" type="text" class="form-control" placeholder="City">
                                            </div>
                                        </div>
                                    
                                       <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">State
                                                </label>
                                                <input  name="state"  value="{{ old('state')}}" type="text" class="form-control" placeholder="State" >
                                            </div>
                                        </div>
                                    
                                       <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Country 
                                                </label>
                                                <input  name="country" value="{{ old('country')}}" type="text" class="form-control" placeholder="Country">
                                            </div>
                                        </div>
                                  
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Zip Code 
                                                </label>
                                                <input  name="zip_code"  value="{{ old('zip_code')}}" type="text" class="form-control" placeholder="Zip Code" >
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Project Start Date 
                                                </label>
                                                <input  name="start_date" value="{{ old('start_date')}}" type="text" class="form-control date" placeholder="Start Date">
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Project End Date 
                                                </label>
                                                <input  name="end_date" value="{{ old('end_date')}}" type="text" class="form-control date" placeholder="End Date">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Project Due Date 
                                                </label>
                                                <input  name="due_date" value="{{ old('due_date')}}" type="text" class="form-control date" placeholder="Due Date">
                                            </div>
                                        </div>
                                  
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Plans URL
                                                </label>
                                                <input  name="plans_url"  value="{{ old('plans_url')}}" type="text" class="form-control" placeholder="Plans URL" >
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Photo 
                                                </label>
                                                <input  name="photo"  type="file">
                                            </div>
                                        </div>

                                       <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Notes 
                                                </label>
                                                <textarea  name="notes"  type="text" class="form-control" placeholder="Notes" >
                                                 {{ old('notes')}}</textarea>
                                            </div>
                                        </div>
                                    
                                       
                                    </div>


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Create Project
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

@section('pagescript')

<script type="text/javascript">

$('.date').datetimepicker({
    format: 'Y-M-D'
});

</script>
@endsection
