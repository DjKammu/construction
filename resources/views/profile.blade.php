@extends('layouts.admin-app')

@section('title','Profile')

@section('content')

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


                <div class="card p-2">
    <div class="row">
        <div class="col-md-12">
            <div class="card-body">
                <div class="nav-tabs-navigation">
                    <div class="nav-tabs-wrapper">
                        <ul id="tabs" class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link text-dark active" data-toggle="tab" href="#profile" role="tab" aria-expanded="true">Profile</a>
                            </li>

                           <!--  <li class="nav-item">
                                <a class="nav-link text-dark" data-toggle="tab" href="#details" role="tab" aria-expanded="false">Details</a>
                            </li> -->

                            <li class="nav-item">
                                <a class="nav-link text-dark" data-toggle="tab" href="#password" role="tab" aria-expanded="false">Password</a>
                            </li>

                        </ul>
                    </div>
                </div>

                <div id="my-tab-content" class="tab-content">

                  <div class="tab-pane" id="password" role="tabpanel" aria-expanded="false">
                        <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form id="change_password_form"  method="post" 
                              action="{{ url('password') }}">
                                  @csrf

                                    <!-- Current Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Current Password</label>
                                                <input id="current_password" name="current_password" type="password" class="form-control" placeholder="Current Password" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- New Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="new_password">New Password</label>
                                                <input id="new_password" type="password" name="password" class="form-control" placeholder="New Password" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Confirm New Password -->
                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="confirm_new_password">Confirm New Password</label>
                                                <input id="confirm_new_password" name="confirm_password" type="password" class="form-control" placeholder="Confirm New Password" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update Password
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                              </div>

                    <!-- Profile -->
                    <div class="tab-pane active" id="profile" role="tabpanel" aria-expanded="true">
                        <div class="row">
                            <form id="personal_details_form"  method="post" 
                    action="{{ url('profile') }}" enctype="multipart/form-data">
                        @csrf
    <div class="col-md-12">
        <div class="card-body">

            <div class="row">

                

                <!-- Mini Profile Section -->
                <div class="col-lg-4 text-center">
                    <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <img style="width: 200px;" id="picture" class=" border-gray" src="{{ ($user->avatar) ? url(\Storage::url($user->avatar)) : asset('images/profile-pictures/default.jpg') }}"
                             alt="Profile Picture">
                        <div>
                        </br>
                            <input type="file" name="profile_picture" />
                            <!-- <button class="btn btn-round btn-dark btn-file btn-sm mt-0"
                                    onclick="document.getElementById('profile-picture-uploader').click()">Change Photo
                            </button> -->
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="col-lg-8">
                    
                        <div class="row text-left">

                            <!-- first name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-dark" for="first_name">Name</label>
                                    <input id="first_name"name="name" type="text"  value="{{ $user->name}}"class="form-control" placeholder="First Name"
                                           required>
                                </div>
                            </div>

                            <!-- last name -->
                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-dark" for="last_name">Last Name</label>
                                    <input id="last_name" type="text" class="form-control" placeholder="Last Name"
                                           required>
                                </div>
                            </div> -->

                            <!-- email address -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-dark" for="email">Email address</label>
                                    <input id="email" type="email" name="email" value="{{ $user->email}}" class="form-control" placeholder="Email" required>
                                </div>
                            </div>

                            <!-- gender -->
                           <!--  <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-dark" for="gender">Gender</label>
                                    <select id="gender" class="form-control" required>
                                        <option>Male</option>
                                        <option>Female</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                            </div> -->

                            <!-- date of birth -->
                           <!--  <div class="col-md-6">
                                <div class="form-group">
                                    <label class="text-dark" for="date-of-birth">Date Of Birth</label>
                                    <input id="date_of_birth" type="text" class="form-control datepicker" required>
                                </div>
                            </div> -->

                            <div class="col-12 text-center">
                                <button id="update-personal-details-button" type="submit" class="btn btn-danger">Update
                                    Profile
                                </button>
                            </div>
                        </div>
                  
                </div>

            </div>
             
        </div>
    </div>
     </form>
</div>


     
@endsection
