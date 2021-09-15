@extends('layouts.app')

@include('layouts.nav')

@section('content')
 <div class="menz-rechnung" id="rechnung">
      <div class="container">
        <div class="row">
          <div class="col-md-12 menz-col pb-0">
            <div class="row">
              <h1 class="forgot-title">Are you having trouble signing in?</h1>
                <p class="section-text mt-4">Please enter your Kundennummer to get started.</p>
                @if (session('status'))
                      <div class="alert alert-success" role="alert">
                          {{ session('status') }}
                      </div>
                  @endif

                <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                  <div class="form-element mb-2">
                    <input class="form-textbox form-textbox-text @error('kundennummer') is-invalid @enderror" id="forgotPassInput" type="text" name="kundennummer" value="{{ old('kundennummer') }}" required autocomplete="kundennummer" autofocus />
                      <span aria-hidden="true" class="form-label" id="forgotPassLabel">Kundennummer</span>   

                      @error('kundennummer')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror

                  </div>


                  <button type="submit" class="button" id="continueBtn" disabled>
                        Reset Password
                  </button>
                </form>
                <p class="section-text mt-2">A Password reset link will be sent to your registered email.</p>
          </div>
          <div class="row mt-5">
            <h1 class="forgot-title mb-4">Enter your registered Email to find your Kundennumer</h1>
            @if (session('status-kundennummer'))
                <div class="alert alert-success" role="alert">
                    {{ session('status-kundennummer') }}
                </div>
            @endif
              <form method="POST" action="{{ route('password.email-kundennummer') }}">
                        @csrf
                <div class="form-element  mb-2">
                    <input class="form-textbox form-textbox-text @error('email') is-invalid @enderror" id="forgotidInput" type="text" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus />
                      <span aria-hidden="true" class="form-label" id="forgotPassLabel">Email Address</span>   

                      @error('email')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror

                </div>
                <button type="submit" class="button" id="idcontinueBtn" disabled>
                      Get Kundennumer
                </button>
              </form>
              <p class="section-text mt-2">Your Kundennumer will be sent to your registered email.</p>
        </div>
        </div>
      </div>
    </div>
    

@endsection
