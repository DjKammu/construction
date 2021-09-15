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
                        <h4 class="mt-0 text-left">Edit Role</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('roles.update',$role->id) }}">
                              <input type="hidden" name="_method" value="PUT">
                                  @csrf

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Name 
                                                </label>
                                                <input  name="name" value="{{ $role->name }}" type="text" class="form-control" placeholder="Role Name" required="">
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
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="view" {{ (in_array('view',explode(',',$role->permissions)) ? 'checked' : '') }}>View
                                                  </label>
                                                </div>
                                                <div class="form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="add" {{ (in_array('add',explode(',',$role->permissions)) ? 'checked' : '') }}>Add
                                                  </label>
                                                </div>
                                                <div class="form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="edit" {{ (in_array('edit',explode(',',$role->permissions)) ? 'checked' : '') }}>Edit
                                                  </label>
                                                </div>
                                                <div class="form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="update" {{ (in_array('update',explode(',',$role->permissions)) ? 'checked' : '') }}>Update
                                                  </label>
                                                </div>

                                                 <div class="form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="delete" {{ (in_array('delete',explode(',',$role->permissions)) ? 'checked' : '') }}>Delete
                                                  </label>
                                                </div>
                                                <div class="form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="add_users" {{ (in_array('add_users',explode(',',$role->permissions)) ? 'checked' : '') }}>Add Users
                                                  </label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update Role
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