 <!-- Category Details -->
<div class="tab-pane active" id="details" role="tabpanel" 
aria-expanded="true">
 <form   method="post" 
          action="{{ route('properties.update',$property->id) }}" enctype="multipart/form-data">
          <input type="hidden" name="_method" value="PUT">
              @csrf

                <!-- Current Password -->
                <div class="row">
                    <div class="col-lg-5 col-md-6 mx-auto">
                        <div class="form-group">
                            <label class="text-dark" for="password">Name 
                            </label>
                            <input  name="property_name" value="{{ $property->property_name }}" type="text" class="form-control" placeholder="Property Name" required="">
                        </div>
                    </div>
                </div>
                
                 <!-- Current Password -->
                <div class="row">
                    <div class="col-lg-5 col-md-6 mx-auto">
                        <div class="form-group">
                            <label class="text-dark" for="password">Property Type
                            </label>
                            <select class="form-control" name="proprty_type_id"> 
                              <option> Select Property Type</option>
                              @foreach($propertyTypes as $type)
                               <option value="{{ $type->id }}" {{ 
                                ($property->proprty_type_id == $type->id) ? 'selected=""' : ''}}>{{ $type->name}}
                               </option>
                              @endforeach
                            </select>
                        </div>
                    </div>
                </div> 

                <!-- Current Password -->
                <div class="row">
                    <div class="col-lg-5 col-md-6 mx-auto">
                        <div class="form-group">
                            <label class="text-dark" for="password">Address
                            </label>
                            <textarea  name="property_address"  type="text" class="form-control" placeholder="Property Address" >
                             {{ $property->property_address }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Current Password -->
                <div class="row">
                    <div class="col-lg-5 col-md-6 mx-auto">
                        <div class="form-group">
                            <label class="text-dark" for="password">City 
                            </label>
                            <input  name="city" value="{{ $property->city }}" type="text" class="form-control" placeholder="City">
                        </div>
                    </div>
                </div>

                <!-- Current Password -->
                <div class="row">
                    <div class="col-lg-5 col-md-6 mx-auto">
                        <div class="form-group">
                            <label class="text-dark" for="password">State
                            </label>
                            <input  name="state"  value="{{ $property->state }}" type="text" class="form-control" placeholder="State" >
                        </div>
                    </div>
                </div>

                <!-- Current Password -->
                <div class="row">
                    <div class="col-lg-5 col-md-6 mx-auto">
                        <div class="form-group">
                            <label class="text-dark" for="password">Country 
                            </label>
                            <input  name="country" value="{{ $property->country }}" type="text" class="form-control" placeholder="Country">
                        </div>
                    </div>
                </div>

                <!-- Current Password -->
                <div class="row">
                    <div class="col-lg-5 col-md-6 mx-auto">
                        <div class="form-group">
                            <label class="text-dark" for="password">Zip Code 
                            </label>
                            <input  name="zip_code"  value="{{ $property->zip_code }}" type="text" class="form-control" placeholder="Zip Code" >
                        </div>
                    </div>
                </div>

                <!-- Current Password -->
                <div class="row">
                    <div class="col-lg-5 col-md-6 mx-auto">
                        <div class="form-group">
                            <label class="text-dark" for="password">Notes 
                            </label>
                            <textarea  name="notes"  type="text" class="form-control" placeholder="Notes" >
                             {{ $property->notes }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Current Password -->
                <div class="row">
                    <div class="col-lg-5 col-md-6 mx-auto">
                        <div class="form-group">
                            <label class="text-dark" for="password">Photo 
                            </label>
                            <input  name="photo"  type="file">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 text-center">
                    <button id="change-password-button" type="submit" class="btn btn-danger">Update Property
                    </button>
                </div>

            </form>
          </div>