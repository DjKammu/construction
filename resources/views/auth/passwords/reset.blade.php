@extends('layouts.app')

@section('content')
<div class="menz-logo">
       <img class="img-fluid" src="{{ asset('images/logo-blue.svg') }}" alt="Menz Logo">
     </div>
<section class="menz-bnr">
        <div class="container">
          <div class="row justify-content-center text-center">
              <div class="col-md-8">
                <h1 class="formHeading reset-title">Reset Password</h1>
                <p class="pass-hint mb-2">Password hint: Use 8 or more characters with a mix of letters, numbers, and symbols.</p>
                @if (session('status'))
                      <div class="alert alert-success" role="alert">
                          {{ session('status') }}
                      </div>
                  @endif
                <div class="card-body">
                   <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                         <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-element mb-2 mx-auto">
                          <input type="email" name="email" class="form-textbox form-textbox-text @error('email') is-invalid @enderror" id="resetemail" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                            <span aria-hidden="true" class="form-label" id="resetemailLabel">E-Mail Address</span>    

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        <div class="form-element mb-2 mx-auto">
                          <input type="password" class=" form-textbox form-textbox-text @error('password') is-invalid @enderror" id="resetpass" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*?[#?!@$%^&*-\]\[]).{8,}" title="Must contain at least one number and one uppercase and one special character and at least 8 or more characters" required autocomplete="new-password" >
                            <span aria-hidden="true" class="form-label" id="resetpassLabel">Passwort</span>  

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror  
                        </div>
                        <div class="form-element mb-2 mx-auto">
                          <input type="password" class=" form-textbox form-textbox-text " id="resetconfirmpass" name="password_confirmation" required autocomplete="new-password">

                            <span aria-hidden="true" class="form-label" id="resetconfirmpassLabel">Confirm Passwort</span>    
                        </div>
                        <button type="submit" class="button" id="resetBtn" disabled>
                          Reset Passwort
                        </button>         
                    </form>
                </div>
              </div>
          </div>  
        </div>
    </section>  

@endsection
