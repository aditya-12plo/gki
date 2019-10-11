@extends('layouts.main-login-miminium')
@section('content-login') 
<form class="form-signin" method="POST" action="{{ route('login') }}">
@csrf

          <div class="panel periodic-login"> 
              <div class="panel-body text-center">
                  <h1 class="atomic-symbol" style="font-size:4em !important;">COMPANY</h1> 
                  <p class="element-name">SYSTEM</p>

                  <i class="icons icon-arrow-down"></i>

                  <div class="flash-message">

@foreach (['danger', 'warning', 'success', 'info'] as $msg)

  @if(Session::has('alert-' . $msg))



  <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>

  @endif

@endforeach

</div> <!-- end .flash-message -->

                  <div class="form-group form-animate-text" style="margin-top:40px !important;">
                    <input id="email" type="email" class="form-text @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <span class="bar"></span>
                    <label>{{ __('E-Mail Address') }}</label>
                  </div>
                  @error('email')
                                    <span class="invalid-feedback" role="alert">
                                    <strong style="color:red;">{{ $message }}</strong>
                                    </span>
                  @enderror

                  <div class="form-group form-animate-text" style="margin-top:40px !important;">
                    <input id="password" type="password" class="form-text @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    <span class="bar"></span>
                    <label>{{ __('Password') }}</label>
                  </div> 

                  @error('password')
                                    <span class="invalid-feedback" role="alert">
                                    <strong style="color:red;">{{ $message }}</strong>
                                    </span>
                  @enderror

                  <input type="submit" class="btn col-md-12" value="SignIn"/>
              </div>
                <div class="text-center" style="padding:5px;">
                    <a href="{{ route('password.request') }}">Forgot Password </a> 
                </div>
          </div>
        </form>

@endsection