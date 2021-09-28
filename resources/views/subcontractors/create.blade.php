@extends('layouts.admin-app')

@section('title', 'Subcontractor')

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
                        <h4 class="mt-0 text-left">Add Subcontractor</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('subcontractors.store') }}" enctype="multipart/form-data">
                                  @csrf

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" value="{{ old('property_name')}}" type="text" class="form-control" placeholder="Subcontractor Name" required="">
                                            </div>
                                        </div>
                                 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">City 
                                                </label>
                                                <input  name="city" value="{{ old('property_name')}}" type="text" class="form-control" placeholder="City">
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
                                                <label class="text-dark" for="password">Zip Code 
                                                </label>
                                                <input  name="zip_code"  value="{{ old('zip_code')}}" type="text" class="form-control" placeholder="Zip Code" >
                                            </div>
                                        </div> 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Email 1
                                                </label>
                                                <input  name="email_1"  value="{{ old('email_1')}}" type="email" class="form-control" placeholder="Email 1" >
                                            </div>
                                        </div> 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Email 2
                                                </label>
                                                <input  name="email_2"  value="{{ old('email_2')}}" type="email" class="form-control" placeholder="Email 2" >
                                            </div>
                                        </div> 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Email 3  
                                                </label>
                                                <input  name="email_3"  value="{{ old('email_3')}}" type="email" class="form-control" placeholder="Email 3" >
                                            </div>
                                        </div>
                                       
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Contact Name 
                                                </label>
                                                <input  name="contact_name"  value="{{ old('contact_name')}}" type="text" class="form-control" placeholder="Conatct Name" >
                                            </div>
                                        </div>
                                       
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Office Phone  
                                                </label>
                                                <input  name="office_phone"  value="{{ old('office_phone')}}" type="text" class="form-control" placeholder="Office Phone" >
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Mobile 
                                                </label>
                                                <input  name="mobile"  value="{{ old('mobile')}}" type="text" class="form-control" placeholder="Mobile" >
                                            </div>
                                        </div>
                                   
                                        
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Trades 
                                                </label>
                                                <select class="form-control" id="trades" name="trades[]" multiple=""> 
                                                  @foreach($trades as $trade)
                                                   <option value="{{ $trade->id }}" >{{ $trade->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Image 
                                                </label>
                                                <input  name="image"  type="file">
                                            </div>
                                        </div>

                                         <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Notes 
                                                </label>
                                                <textarea  style="min-height: 95px;" name="notes"  type="text" class="form-control" placeholder="Notes" >
                                                 {{ old('notes')}}</textarea>
                                            </div>
                                        </div>

                                        

                                       
                                   
                                    </div>


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Create Subcontractor
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
  <script>

  $("#trades").select2({
      placeholder: "Select Trades",
      allowClear: true,
      maximumSelectionLength: 2
  });
</script>

@endsection
