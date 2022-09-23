 <!-- Category Details -->
<div class="tab-pane active" id="details" role="tabpanel" 
aria-expanded="true">

<div class="row mb-2">
    <div class="col-6">
        <h4 class="mt-0 text-left"> {{ @$project->name }} - Project Detail</h4>
    </div>
</div>

 <form   method="post" 
          action="{{ route('projects.update',$project->id) }}" enctype="multipart/form-data">
          <input type="hidden" name="_method" value="PUT">
              @csrf
                
                <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>

                                                <input  name="name" value="{{ $project->name }}" type="text" class="form-control" placeholder="Name" required="">
                                            </div>
                                        </div>
                                
                                         <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Project Type
                                                </label>

                                                <select class="form-control" name="project_type_id"> 
                                                  <option> Select Project Type</option>
                                                  @foreach($projectTypes as $type)
                                                   <option value="{{ $type->id }}" {{ 
                                                    ($project->project_type_id == $type->id) ? 'selected=""' : ''}}>{{ $type->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>

                                          <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Property
                                                </label>

                                                <select class="form-control" name="property_type_id"> 
                                                  <option> Select Property</option>
                                                  @foreach($propertyTypes as $type)
                                                   <option value="{{ $type->id }}" {{ 
                                                    ($project->property_type_id == $type->id) ? 'selected=""' : ''}}>{{ $type->name}}
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
                                                {{ $project->address }}</textarea>
                                            </div>
                                        </div>
                                   
                                       <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">City 
                                                </label>
                                                <input  name="city" value="{{ $project->city }}" type="text" class="form-control" placeholder="City">
                                            </div>
                                        </div>
                                    
                                       <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">State
                                                </label>
                                                <input  name="state"  value="{{ $project->state }}" type="text" class="form-control" placeholder="State" >
                                            </div>
                                        </div>
                                    
                                       <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Country 
                                                </label>
                                                <input  name="country" value="{{ $project->country }}" type="text" class="form-control" placeholder="Country">
                                            </div>
                                        </div>
                                  
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Zip Code 
                                                </label>
                                                <input  name="zip_code"  value="{{ $project->zip_code }}" type="text" class="form-control" placeholder="Zip Code" >
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Project Start Date 
                                                </label>
                                                <input  name="start_date" value="{{ $project->start_date }}" type="text" class="form-control date" placeholder="Start Date">
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Project End Date 
                                                </label>
                                                <input  name="end_date" value="{{ $project->end_date }}" type="text" class="form-control date" placeholder="End Date">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Bid Due Date 
                                                </label>
                                                <input  name="due_date" value="{{ $project->due_date }}" type="text" class="form-control date" placeholder="Due Date">
                                            </div>
                                        </div>
                                  
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Plans URL
                                                </label>
                                                <input  name="plans_url"  value="{{ $project->plans_url }}" type="text" class="form-control" placeholder="Plans URL" >
                                                <a href="{{ $project->plans_url }}" target="_new"> Click Here </a>
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
                                                 {{ $project->notes }}</textarea>
                                            </div>
                                        </div>

                                         <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                @if(!empty($project->photo))
                                                <img style="width: 200px;" src="{{ url(\Storage::url($project->photo)) }}" />
                                                @endif

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

                        <input  name="project_number" value="{{ $project->project_number }}" type="text" class="form-control" placeholder="Project Number">
                    </div>
                </div> 

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Project Owner Name 
                        </label>

                        <input  name="owner_name" value="{{ $project->owner_name }}" type="text" class="form-control" placeholder="Project Owner Name">
                    </div>
                </div>
            
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Address Street 
                        </label>
                        <textarea  name="owner_street"  type="text" class="form-control" placeholder="Property Address" >
                        {{ $project->owner_street }}</textarea>
                    </div>
                </div>
           
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">City 
                        </label>
                        <input  name="owner_city" value="{{ $project->owner_city }}" type="text" class="form-control" placeholder="City">
                    </div>
                </div>
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">State
                        </label>
                        <input  name="owner_state"  value="{{ $project->owner_state }}" type="text" class="form-control" placeholder="State" >
                    </div>
                </div>
            
          
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Zip Code 
                        </label>
                        <input  name="owner_zip"  value="{{ $project->owner_zip }}" type="text" class="form-control" placeholder="Zip Code" >
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

                        <input  name="contract_name" value="{{ $project->contract_name }}" type="text" class="form-control" placeholder="Contractor Name">
                    </div>
                </div>
            
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Address Street 
                        </label>
                        <textarea  name="contract_street"  type="text" class="form-control" placeholder="Property Address" >
                        {{ $project->contract_street }}</textarea>
                    </div>
                </div>
           
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">City 
                        </label>
                        <input  name="contract_city" value="{{ $project->contract_city }}" type="text" class="form-control" placeholder="City">
                    </div>
                </div>
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">State
                        </label>
                        <input  name="contract_state"  value="{{ $project->contract_state }}" type="text" class="form-control" placeholder="State" >
                    </div>
                </div>
            
          
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Zip Code 
                        </label>
                        <input  name="contract_zip"  value="{{ $project->contract_zip }}" type="text" class="form-control" placeholder="Zip Code" >
                    </div>
                </div> 

                 <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Phone Number 
                        </label>
                        <input  name="contract_phone"  value="{{ $project->contract_phone }}" type="text" class="form-control" placeholder="Phone Number" >
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

                        <input  name="architect_name" value="{{ $project->architect_name }}" type="text" class="form-control" placeholder="Architect Name">
                    </div>
                </div>
            
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Address Street 
                        </label>
                        <textarea  name="architect_street"  type="text" class="form-control" placeholder="Property Address" >
                        {{ $project->architect_street }}</textarea>
                    </div>
                </div>
           
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">City 
                        </label>
                        <input  name="architect_city" value="{{ $project->architect_city }}" type="text" class="form-control" placeholder="City">
                    </div>
                </div>
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">State
                        </label>
                        <input  name="architect_state"  value="{{ $project->architect_state }}" type="text" class="form-control" placeholder="State" >
                    </div>
                </div>
            
          
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Zip Code 
                        </label>
                        <input  name="architect_zip"  value="{{ $project->architect_zip }}" type="text" class="form-control" placeholder="Zip Code" >
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

                        <input  name="notary_name" value="{{ $project->notary_name }}" type="text" class="form-control" placeholder="Notary Name">
                    </div>
                </div>
           
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">County 
                        </label>
                        <input  name="notary_country" value="{{ $project->notary_country }}" type="text" class="form-control" placeholder="County">
                    </div>
                </div>
            
               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">State
                        </label>
                        <input  name="notary_state"  value="{{ $project->notary_state }}" type="text" class="form-control" placeholder="State" >
                    </div>
                </div>

               <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Project Email
                        </label>
                        <input  name="project_email"  value="{{ $project->project_email }}" type="text" class="form-control" placeholder="Project Email" >
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Contract Date 
                        </label>
                        <input  name="contract_date" value="{{ $project->contract_date }}" type="text" class="form-control date" placeholder="Contract Date">
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Project Date 
                        </label>
                        <input  name="project_date" value="{{ $project->project_date }}" type="text" class="form-control date" placeholder="Project Date">
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Commission Expire Date 
                        </label>
                        <input  name="commission_expire_date" value="{{ $project->commission_expire_date }}" type="text" class="form-control date" placeholder="Commission Expire Date">
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">

                   <div class="form-group">
                        <label class="text-dark" for="password">Default Retainage Percentage   
                        </label>
                        <input  name="retainage_percentage" value="{{ $project->retainage_percentage }}" type="number" class="form-control" max="100" min="0" placeholder="Default Retainage Percentage " step="any">
                    </div>
                </div> 

                <div class="col-lg-6 col-md-6">

                   <div class="form-group">
                        <label class="text-dark" for="password">Original Amount   
                        </label>
                        <input  name="original_amount" value="{{ $project->original_amount }}" type="number" class="form-control" placeholder="Original Amount " step="any">
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="form-group">
                        <label class="text-dark" for="password">Status
                        </label>
                        <select class="form-control" name="status"> 
                          <option value="">Select Status</option>
                           @foreach($statuses as $status)
                               <option value="{{ $status->id }}" {{ (@$project->status == $status->id) ? 'selected' : ''}}> {{ $status->name }}</option>
                            @endforeach

                          <!-- <option value="{{\App\Models\Project::ACTIVE_STATUS }}"
                          {{\App\Models\Project::ACTIVE_TEXT == $project->status ? 'selected=""' : ''}} >{{\App\Models\Project::ACTIVE_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::PUT_ON_HOLD_STATUS }}"
                          {{\App\Models\Project::PUT_ON_HOLD_STATUS == $project->status ? 'selected=""' : ''}} >{{\App\Models\Project::PUT_ON_HOLD_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::FINISHED_STATUS }}"
                          {{\App\Models\Project::FINISHED_STATUS == $project->status ? 'selected=""' : ''}} >{{\App\Models\Project::FINISHED_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::CANCELLED_STATUS }}"
                          {{\App\Models\Project::CANCELLED_STATUS == $project->status ? 'selected=""' : ''}} >{{\App\Models\Project::CANCELLED_TEXT  }}</option>
                          <option value="{{ \App\Models\Project::ARCHIVED_STATUS }}"
                          {{\App\Models\Project::ARCHIVED_STATUS == $project->status ? 'selected=""' : ''}} >{{\App\Models\Project::ARCHIVED_TEXT  }}</option> -->

                        </select>
                    </div>
                </div>
          
               
            </div>

                <!-- Submit Button -->
                <div class="col-12 text-center">
                    <button id="change-password-button" type="submit" class="btn btn-danger">Update Project
                    </button>
                </div>

            </form>
          </div>