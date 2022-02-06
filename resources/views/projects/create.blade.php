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
                                    
                                    <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left"> Project Owner Detail</h4>
                    </div>
                </div>

                 <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Project Number
                        </label>

                        <input  name="project_number" value=" {{ old('project_number')}}" type="text" class="form-control" placeholder="Project Number">
                    </div>
                </div> 

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Project Owner Name 
                        </label>

                        <input  name="owner_name" value=" {{ old('owner_name')}}" type="text" class="form-control" placeholder="Project Owner Name">
                    </div>
                </div>
            
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Address Street 
                        </label>
                        <textarea  name="owner_street"  type="text" class="form-control" placeholder="Property Address" >
                        {{ old('owner_street')}}</textarea>
                    </div>
                </div>
           
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">City 
                        </label>
                        <input  name="owner_city" value=" {{ old('owner_city')}}" type="text" class="form-control" placeholder="City">
                    </div>
                </div>
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">State
                        </label>
                        <input  name="owner_state"  value=" {{ old('owner_state')}}" type="text" class="form-control" placeholder="State" >
                    </div>
                </div>
            
          
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Zip Code 
                        </label>
                        <input  name="owner_zip"  value=" {{ old('owner_zip')}}" type="text" class="form-control" placeholder="Zip Code" >
                    </div>
                </div>
               
            </div> 


            <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left"> Contractor Detail</h4>
                    </div>
                </div>

                 <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Contractor Name 
                        </label>

                        <input  name="contract_name" value=" {{ old('contract_name')}}" type="text" class="form-control" placeholder="Contractor Name">
                    </div>
                </div>
            
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Address Street 
                        </label>
                        <textarea  name="contract_street"  type="text" class="form-control" placeholder="Property Address" >
                        {{ old('contract_street')}}</textarea>
                    </div>
                </div>
           
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">City 
                        </label>
                        <input  name="contract_city" value=" {{ old('contract_city')}}" type="text" class="form-control" placeholder="City">
                    </div>
                </div>
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">State
                        </label>
                        <input  name="contract_state"  value=" {{ old('contract_state')}}" type="text" class="form-control" placeholder="State" >
                    </div>
                </div>
            
          
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Zip Code 
                        </label>
                        <input  name="contract_zip"  value=" {{ old('contract_zip')}}" type="text" class="form-control" placeholder="Zip Code" >
                    </div>
                </div> 

                 <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Phone Number 
                        </label>
                        <input  name="contract_phone"  value=" {{ old('contract_phone')}}" type="text" class="form-control" placeholder="Phone Number" >
                    </div>
                </div>
               
            </div>

            <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left"> Architect Detail</h4>
                    </div>
                </div>

                 <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Architect Name 
                        </label>

                        <input  name="architect_name" value=" {{ old('architect_name')}}" type="text" class="form-control" placeholder="Architect Name">
                    </div>
                </div>
            
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Address Street 
                        </label>
                        <textarea  name="architect_street"  type="text" class="form-control" placeholder="Property Address" >
                         {{ old('architect_street')}}</textarea>
                    </div>
                </div>
           
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">City 
                        </label>
                        <input  name="architect_city" value=" {{ old('architect_city')}}" type="text" class="form-control" placeholder="City">
                    </div>
                </div>
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">State
                        </label>
                        <input  name="architect_state"  value=" {{ old('architect_state')}}" type="text" class="form-control" placeholder="State" >
                    </div>
                </div>
            
          
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Zip Code 
                        </label>
                        <input  name="architect_zip"  value=" {{ old('architect_zip')}}" type="text" class="form-control" placeholder="Zip Code" >
                    </div>
                </div>
               
            </div>

            <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left"> Notary & Other  Detail</h4>
                    </div>
                </div>

                 <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Notary Name 
                        </label>

                        <input  name="notary_name" value=" {{ old('notary_name')}}" type="text" class="form-control" placeholder="Notary Name">
                    </div>
                </div>
           
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">County 
                        </label>
                        <input  name="notary_country" value=" {{ old('notary_country')}}" type="text" class="form-control" placeholder="County">
                    </div>
                </div>
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">State
                        </label>
                        <input  name="notary_state"  value=" {{ old('notary_state')}}" type="text" class="form-control" placeholder="State" >
                    </div>
                </div>

               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Project Email
                        </label>
                        <input  name="project_email"  value=" {{ old('project_email')}}" type="text" class="form-control" placeholder="Project Email" >
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Contract Date 
                        </label>
                        <input  name="contract_date" value=" {{ old('contract_date')}}" type="text" class="form-control date" placeholder="Contract Date">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Project Date 
                        </label>
                        <input  name="project_date" value=" {{ old('project_date')}}" type="text" class="form-control date" placeholder="Project Date">
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Commission Expire Date 
                        </label>
                        <input  name="commission_expire_date" value=" {{ old('commission_expire_date')}}" type="text" class="form-control date" placeholder="Commission Expire Date">
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">

                   <div class="form-group">
                        <label class="text-dark" for="password">Default Retainage Percentage   
                        </label>
                        <input  name="retainage_percentage" value="10" type="number" class="form-control" max="100" min="0" placeholder="Default Retainage Percentage " step="any">
                    </div>
                </div> 

                <div class="col-lg-6 col-md-6">

                   <div class="form-group">
                        <label class="text-dark" for="password">Original Amount   
                        </label>
                        <input  name="original_amount" value=" {{ old('original_amount')}}" type="number" class="form-control" placeholder="Original Amount " step="any">
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Status
                        </label>
                        <select class="form-control" name="status"> 
                          <option value="">Select Status</option>
                          <option value="{{\App\Models\Project::ACTIVE_STATUS }}"
                          >{{\App\Models\Project::ACTIVE_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::PUT_ON_HOLD_STATUS }}"
                          >{{\App\Models\Project::PUT_ON_HOLD_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::FINISHED_STATUS }}"
                          >{{\App\Models\Project::FINISHED_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::CANCELLED_STATUS }}"
                          >{{\App\Models\Project::CANCELLED_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::ARCHIVED_STATUS }}"
                          >{{\App\Models\Project::ARCHIVED_TEXT  }}</option>
                        </select>
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
