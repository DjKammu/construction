@extends('layouts.admin-app')

@section('title', 'Users')

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
                        <h4 class="mt-0 text-left">Add User</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('users.store') }}">
                                  @csrf

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" type="text" class="form-control" value="{{ old('name') }}" placeholder="Name" required="">
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">{{ __('E-Mail Address') }} 
                                                </label>
                                                 <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autocomplete="email">
                                            </div>
                                        </div>
                                    </div> 
                                    
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">{{ __('Password') }} 
                                                </label>
                                                <input  name="password" type="password" class="form-control" value="{{ old('name') }}" required>
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">{{ __('Confirm Password') }}
                                                </label>
                                                 <input type="password" class="form-control" name="password_confirmation" value="{{ old('email') }}" required>
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">{{ __('User Role') }}
                                                </label>

                                                <select id="role" class="form-control" name="role" required>
                                                  <option>Select Role</option>
                                                @foreach($roles as $id => $role)
                                                    <option value="{{$id}}">{{$role}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div> 



                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Create Role
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
  function deletePropertyType(id){

    if(confirm('Are you sure to delete?')){
      window.location.href = '{{ route("property-types.destroy",1)}}';
    }
  }
</script>

@endsection