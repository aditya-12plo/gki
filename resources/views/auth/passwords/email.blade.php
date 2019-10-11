@extends('layouts.main-login-gull')
@section('content-login') 
<div class="row">
    <div class="col-md-12">
        <div class="p-4"> 
            <h1 class="mb-3 text-30" align="center">Forgot Password</h1>
            <br>
            @if (session('status'))
              <div class="alert alert-card alert-success" role="alert">
                <strong class="text-capitalize">Success!</strong> {{ session('status') }}.
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                  </button>
              </div> 
            @endif
            <form class="form-signin" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" class="form-control" type="email" placeholder="Email" required autocomplete="email" autofocus>
                @error('email')
                    <div class="invalid-tooltip">
                        {{ $message }}
                    </div>
                @enderror
            </div> 
            <button class="btn btn-rounded btn-primary btn-block mt-2" type="submit">  {{ __('Send Password Reset Link') }}</button>
            
            </form>

            <div class="mt-3 text-center">
                <a href="/"><u>Login</u> </a> 
            </div> 

        </div>
    </div>                  
</div>
@endsection
