@extends('layouts.main-login-gull')
@section('content-login')
 
<div class="row">
                    <div class="col-md-12">
                        <div class="p-4"> 
                            <h1 class="mb-3 text-30" align="center">Sign In</h1>
                            <br>
                            @foreach (['danger', 'warning', 'success', 'info'] as $msg)

                            @if(Session::has('alert-' . $msg)) 
                            <div class="alert alert-card alert-{{ $msg }}" role="alert">
                                <strong class="text-capitalize">{{ $msg }}</strong> {{ Session::get('alert-' . $msg) }}.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div> 
                            @endif

                            @endforeach 
                            <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                                
                            @csrf
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="text" class="form-text @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    @error('email')
                                    <div class="invalid-tooltip">
                                       {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                 

                                
                                <button class="btn btn-rounded btn-primary btn-block mt-2" type="submit">Sign In</button>

                            </form>
                            <div class="mt-3 text-center">
                                <a href="/"><u>Login</u></a>
                            </div>
                        </div>
                    </div>
                     
                </div>

 

@endsection