@extends('layouts.admin-app')

@section('title', 'Roles')

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
                        <h4 class="mt-0 text-left">Add Role</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('roles.store') }}">
                                  @csrf

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" type="text" class="form-control" placeholder="Role Name" required="">
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Permissions 
                                                </label>
                                              </div>  
                              
                                    

                                            <div class="form-group">
                                                <div class="form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="view">View
                                                  </label>
                                                </div>
                                                <div class="form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="add">Add
                                                  </label>
                                                </div>
                                                <div class="form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="edit">Edit
                                                  </label>
                                                </div>
                                                <div class="form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="update">Update
                                                  </label>
                                                </div>

                                                 <div class="form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="delete">Delete
                                                  </label>
                                                </div>
                                                <div class="form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="add_users">Add Users
                                                  </label>
                                                </div>

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