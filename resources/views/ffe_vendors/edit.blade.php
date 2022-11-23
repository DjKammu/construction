@extends('layouts.admin-app')

@section('title', 'FFE Vendor')

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
                        <h4 class="mt-0 text-left">Edit FFE Vendor</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('ffe.vendors.update',$vendor->id) }}" enctype="multipart/form-data">
                                  @csrf
                                  @method('PUT')

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" value="{{ $vendor->name}}" type="text" class="form-control" placeholder="FFE Vendor Name" required="">
                                            </div>
                                        </div>
                                 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">City 
                                                </label>
                                                <input  name="city" value="{{ $vendor->city }}" type="text" class="form-control" placeholder="City">
                                            </div>
                                        </div>
                                    
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">State
                                                </label>
                                                <input  name="state"  value="{{ $vendor->state }}" type="text" class="form-control" placeholder="State" >
                                            </div>
                                        </div>
                                  
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Zip Code 
                                                </label>
                                                <input  name="zip"  value="{{ $vendor->zip }}" type="text" class="form-control" placeholder="Zip Code" >
                                            </div>
                                        </div> 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Email
                                                </label>
                                                <input  name="email"  value="{{ $vendor->email}}" type="email" class="form-control" placeholder="Email" >
                                            </div>
                                        </div> 
                                       
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Contact Name 
                                                </label>
                                                <input  name="contact_name"  value="{{ $vendor->contact_name }}" type="text" class="form-control" placeholder="Contact Name" >
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Trades 
                                                </label>
                                                <select class="form-control" id="trades" name="trades[]" multiple=""> 
                                                  @foreach($trades as $trade)
                                                   <option value="{{ $trade->id }}" 

                                                    {{ (in_array($trade->id , @$vendor->trades->pluck('id')->toArray())) ? 'selected' : ''}}>{{ $trade->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
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
                                                <textarea  style="min-height: 95px;" name="notes"  type="text" class="form-control" placeholder="Notes" >
                                                 {{ $vendor->notes }}</textarea>
                                            </div>
                                        </div>
                                        
                                         <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                @if(!empty($vendor->photo))
                                                <img style="width: 200px;" src="{{ url(\Storage::url($vendor->photo)) }}" />
                                                @endif

                                            </div>
                                        </div>

                                        

                                    </div>


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update FFE Vendor
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
  <script >


   $("#trades").select2({
      placeholder: "Select Trades",
      allowClear: true
  });

  
</script>

@endsection
