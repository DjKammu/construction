 <!-- Category Details -->
<div class="tab-pane active" id="details" role="tabpanel" 
aria-expanded="true">
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
                                                <label class="text-dark" for="password">Project Due Date 
                                                </label>
                                                <input  name="due_date" value="{{ $project->due_date }}" type="text" class="form-control date" placeholder="Due Date">
                                            </div>
                                        </div>
                                  
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Plans URL
                                                </label>
                                                <input  name="plans_url"  value="{{ $project->plans_url }}" type="text" class="form-control" placeholder="Plans URL" >
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

                <!-- Submit Button -->
                <div class="col-12 text-center">
                    <button id="change-password-button" type="submit" class="btn btn-danger">Update Project
                    </button>
                </div>

            </form>
          </div>