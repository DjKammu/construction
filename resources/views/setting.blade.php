@extends('layouts.admin-app')

@section('title', 'Setting')

@section('content')

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


            <div class="card-body">
              <div class="row mb-2">
                    <div class="col-6">
                        <h4 class="mt-0 text-left">Setting</h4>
                    </div>
                </div>

               <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <form   method="post" 
                              action="{{ route('setting.store') }}" enctype="multipart/form-data">
                                  @csrf

                                    <!-- Current Password -->

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Server Type 
                                                </label>
                                                <select class="form-control" name="server_type" required=""> 
                                                  <option value=""> Select Sever Type</option>
                                                  @foreach(\App\Models\Setting::$serverTypes as $key => $serverType)
                                                   <option value="{{ $serverType }}" {{ (@$setting->server_type == $serverType ) ? 'selected="selected"' : ''}} >{{ @ucfirst(Str::title(str_replace(['-','_'], ' ', $serverType)))}}
                                                   </option>
                                                  @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Server Name 
                                                </label>
                                                <input  name="server_name" value="{{ @$setting->server_name }}" type="text" class="form-control" placeholder="Server Name" required="">
                                            </div>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Port 
                                                </label>
                                                <input  name="port" value="{{ @$setting->server_name }}" type="text" class="form-control" placeholder="Port" required="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">Mail Encryption 
                                                </label>
                                                <select class="form-control" name="mail_encryption" required=""> 
                                                
                                                   <option value="{{\App\Models\Setting::SSL}}" {{ (@$setting->mail_encryption == \App\Models\Setting::SSL ) ? 'selected="selected"' : ''}} >{{ @Str::upper(Str::title(str_replace(['-','_'], ' ', \App\Models\Setting::SSL)))}}
                                                   </option> 
                                                   <option value="{{\App\Models\Setting::TLS}}" {{ (@$setting->mail_encryption == \App\Models\Setting::TLS) ? 'selected="selected"' : ''}} >{{ @Str::upper(Str::title(str_replace(['-','_'], ' ', \App\Models\Setting::TLS)))}}
                                                   </option>
                                               
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">User Name 
                                                </label>
                                                <input  name="user_name" value="{{ @$setting->user_name }}" type="text" class="form-control" placeholder="User Name" required="">
                                            </div>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">password 
                                                </label>
                                                <input  name="password" value="{{ @$setting->password }}" type="text" class="form-control" placeholder="Password" required="">
                                            </div>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-lg-5 col-md-6 mx-auto">
                                            <div class="form-group">
                                                <label class="text-dark" for="password">from_email 
                                                </label>
                                                <input  name="from_email" value="{{ @$setting->from_email }}" type="text" class="form-control" placeholder="From Email" required="">
                                            </div>
                                        </div>
                                    </div>
                                    


                                    <!-- Submit Button -->
                                    <div class="col-12 text-center">
                                        <button id="change-password-button" type="submit" class="btn btn-danger">Update Setting
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

$('.date').datetimepicker({
    format: 'Y-M-D'
});

</script>
@endsection
