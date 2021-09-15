@extends('layouts.app')

@section('content')

  <div class="col-lg-4 col-md-6 ml-auto mr-auto">
      <form class="form" id="login_form" method="POST" action="{{ route('login') }}">
            @csrf
        <div class="card card-login">
            <div class="card-header ">
                <div class="card-header ">
                    <h3 class="header text-center">Login</h3>
                </div>
            </div>
            <div class="card-body ">
                <div id="alert-container"></div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa fa-envelope-o"></i>
                        </span>
                    </div>
                    <input id="email" type="email" class="form-control menz-input @error('email') is-invalid @enderror" name="email" id="idInput" value="{{ old('email') }}" placeholder="Email" required>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fa fa-key"></i>
                        </span>
                    </div>
                    <input id="password" type="password"  class="form-control menz-input psInput display-none @error('password') is-invalid @enderror" placeholder="Password" name="password" required>
                    @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                </div>
                <div class="form-group">
                    <a href="{{ route('password.request') }}" class="text-muted pull-right">Forgot Password?</a>
                </div>
            </div>
            <div class="card-footer ">
                <button type="submit" class="btn btn-warning btn-round btn-block mb-3">Login</button>
            </div>
        </div>
    </form>

 </div>   

@endsection
