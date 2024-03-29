@extends('layouts.admin-app')

@section('title', 'Property Group')

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
                        <h4 class="mt-0 text-left">Update Property Group</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" action="{{ route('property-groups.update',$propertyGroup->id) }}"  >
                                  @csrf
                                     @method('PUT')
                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" value="{{ @$propertyGroup->name }}" type="text" class="form-control" placeholder="Property Group Name" required="">
                                            </div>
                                        </div>
                                  </div>
                                    
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Account Number 
                                                </label>
                                                <input  name="account_number" value="{{ @$propertyGroup->name }}" type="text" class="form-control" placeholder="Account Number">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Properties 
                                                </label>
                                                <select class="form-control" id="properties" name="properties[]" multiple=""> 
                                                  @foreach($properties as $property)
                                                   <option value="{{ $property->id }}"  {{ (in_array($property->id , @$propertyGroup->properties->pluck('id')->toArray())) ? 'selected' : ''}} >{{ $property->name}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>

                                   
                                    </div>


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update Property Group
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

  $("#properties").select2({
      placeholder: "Select Properties",
      allowClear: true
  });
</script>

@endsection
