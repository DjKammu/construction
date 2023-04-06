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
                        <h4 class="mt-0 text-left">Edit Subcontractor</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('subcontractors.update',$subcontractor->id) }}" enctype="multipart/form-data">
                                  @csrf
                                  @method('PUT')

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" value="{{ $subcontractor->name}}" type="text" class="form-control" placeholder="Subcontractor Name" required="">
                                            </div>
                                        </div>
                                 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">City 
                                                </label>
                                                <input  name="city" value="{{ $subcontractor->city }}" type="text" class="form-control" placeholder="City">
                                            </div>
                                        </div>
                                    
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">State
                                                </label>
                                                <input  name="state"  value="{{ $subcontractor->state }}" type="text" class="form-control" placeholder="State" >
                                            </div>
                                        </div>
                                  
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Zip Code 
                                                </label>
                                                <input  name="zip_code"  value="{{ $subcontractor->zip_code }}" type="text" class="form-control" placeholder="Zip Code" >
                                            </div>
                                        </div> 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Email 1
                                                </label>
                                                <input  name="email_1"  value="{{ $subcontractor->email_1 }}" type="email" class="form-control" placeholder="Email 1" >
                                            </div>
                                        </div> 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Email 2
                                                </label>
                                                <input  name="email_2"  value="{{ $subcontractor->email_2 }}" type="email" class="form-control" placeholder="Email 2" >
                                            </div>
                                        </div> 
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Email 3  
                                                </label>
                                                <input  name="email_3"  value="{{ $subcontractor->email_3 }}" type="email" class="form-control" placeholder="Email 3" >
                                            </div>
                                        </div>
                                       
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Contact Name 
                                                </label>
                                                <input  name="contact_name"  value="{{ $subcontractor->contact_name }}" type="text" class="form-control" placeholder="Contact Name" >
                                            </div>
                                        </div>
                                       
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Office Phone  
                                                </label>
                                                <input  name="office_phone"  value="{{ $subcontractor->office_phone }}" type="text" class="form-control" placeholder="Office Phone" >
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Mobile 
                                                </label>
                                                <input  name="mobile"  value="{{ $subcontractor->mobile }}" type="text" class="form-control" placeholder="Mobile" >
                                            </div>
                                        </div>
                                   
                                        
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Trades 
                                                </label>
                                                <select class="form-control" id="trades" name="trades[]" multiple=""> 
                                                  @foreach($trades as $trade)
                                                   <option value="{{ $trade->id }}" 

                                                    {{ (in_array($trade->id , @$subcontractor->trades->pluck('id')->toArray())) ? 'selected' : ''}}>{{ $trade->name}}
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
                                                <label class="text-dark" for="password">Replace 
                                                </label>
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6">
                                                     What

                                                     <select class="form-control" id="what" name="what"> 
                                                      <option value="">Select What</option>
                                                      @foreach(@$subcontractor->trades as $trade)
                                                       <option value="{{ $trade->id }}" 
                                                        >{{ $trade->name}}
                                                       </option>
                                                      @endforeach
                                                    </select>

                                                    </div>   
                                                    <div class="col-lg-6 col-md-6">
                                                      With

                                                      <select class="form-control" id="with" 
                                                      name="with"> 
                                                      <option value="">Select With</option>
                                                      @foreach($trades as $trade)
                                                       @php
                                                        if(in_array($trade->id , @$subcontractor->trades->pluck('id')->toArray())){
                                                          continue;
                                                        }
                                                       @endphp
                                                       <option value="{{ $trade->id }}" 
                                                        >{{ $trade->name}}
                                                       </option>
                                                      @endforeach
                                                    </select>

                                                    </div>   
                                                </div>
                                            </div>
                                        </div>

                                         <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Notes 
                                                </label>
                                                <textarea  style="min-height: 95px;" name="notes"  type="text" class="form-control" placeholder="Notes" >
                                                 {{ $subcontractor->notes }}</textarea>
                                            </div>
                                        </div>
                                        
                                         <div class="col-lg-6 col-md-6">
                                            <div class="form-group">
                                                @if(!empty($subcontractor->image))
                                                <img style="width: 200px;" src="{{ url(\Storage::url($subcontractor->image)) }}" />
                                                @endif

                                            </div>
                                        </div>

                                        

                                    </div>


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update Subcontractor
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
   $("#what").select2({
      placeholder: "Select Trades",
      allowClear: true
  });
  $("#with").select2({
      placeholder: "Select Trades",
      allowClear: true
  });

  
</script>

@endsection
