@extends('layouts.main-login-miminium')

@section('content-login') 
<form class="form-signin" method="POST" action="{{ route('password.email') }}">
@csrf
          <div class="panel periodic-login"> 
              <div class="panel-body text-center">
                  <h1 class="atomic-symbol" style="font-size:4em !important;">REKON</h1> 
                  <p class="element-name">SYSTEM</p>

                  @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                  <i class="icons icon-arrow-down"></i>
                  <div class="form-group form-animate-text" style="margin-top:40px !important;">
                    <input id="email" type="email" class="form-text @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <span class="bar"></span>
                    <label>Email</label>
                  </div>
                  
                  @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong style="color:red;">{{ $message }}</strong>
                                    </span>
                  @enderror
                  <button type="submit" class="btn col-md-12">
                                    {{ __('Send Password Reset Link') }}
                                </button> 
              </div>
                <div class="text-center" style="padding:5px;">
                    <a href="/">Login </a> 
                </div>
          </div>
        </form>


   
@endsection
