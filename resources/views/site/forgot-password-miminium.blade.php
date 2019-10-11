@extends('layouts.main-login')
@section('content-login')
 
<form class="form-signin">
          <div class="panel periodic-login"> 
              <div class="panel-body text-center">
                  <h1 class="atomic-symbol" style="font-size:4em !important;">REKON</h1> 
                  <p class="element-name">SYSTEM</p>

                  <i class="icons icon-arrow-down"></i>
                  <div class="form-group form-animate-text" style="margin-top:40px !important;">
                    <input type="text" class="form-text @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <span class="bar"></span>
                    <label>Email</label>
                  </div>
                  <input type="submit" class="btn col-md-12" value="SignIn"/>
              </div>
                <div class="text-center" style="padding:5px;">
                    <a href="/">Login </a> 
                </div>
          </div>
        </form>

@endsection