<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	
	<meta charset="utf-8"> 
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="author" content="@adit_xxx_">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{config('app.name')}}</title>
 
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- start: Css -->
    <link rel="stylesheet" type="text/css" href="/assets/css/bootstrap.min.css">  

      <!-- plugins -->
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/plugins/font-awesome.min.css')}}"/>
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/plugins/simple-line-icons.css')}}"/>
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/plugins/animate.min.css')}}"/>
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/plugins/fullcalendar.min.css')}}"/>
	<link href="/assets/css/style.css" rel="stylesheet"> 
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<!-- end: Css --> 
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
  </head>

 <body id="mimin" class="dashboard">
      <!-- start: Header -->
        <nav class="navbar navbar-default header navbar-fixed-top">
          <div class="col-md-12 nav-wrapper">
            <div class="navbar-header" style="width:100%;">
              <div class="opener-left-menu is-open">
                <span class="top"></span>
                <span class="middle"></span>
                <span class="bottom"></span>
              </div>
                <a href="/home#/" class="navbar-brand"> 
                 <b>{{config('app.name')}}</b>
                </a>
 

              <ul class="nav navbar-nav navbar-right user-nav">
                <li class="user-name"><span>{{ Auth::user()->name }} ({{ Auth::user()->role->role_name }})</span></li>
                  <li class="dropdown avatar-dropdown">
                   <img src="/assets/img/avatar.jpg" class="img-circle avatar" alt="{{ Auth::user()->name }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"/>
                   <ul class="dropdown-menu user-dropdown">
                     <li><a href="/users-view#/profile"><span class="fa fa-user"></span> My Profile</a></li> 
                     <li role="separator" class="divider"></li>
                     <li class="more">
                      <ul>
                        <li><a href="/users-view#/change-password"><span class="fa fa-cogs"></span></a></li> 
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><span class="fa fa-power-off "></span></a></li>
                        
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                      </ul>
                    </li>
                  </ul>
                </li> 
              </ul>
            </div>
          </div>
        </nav>
      <!-- end: Header -->

      <div class="container-fluid mimin-wrapper">
  
          <!-- start:Left Menu -->
            <div id="left-menu">
              <div class="sub-left-menu scroll">
                <ul class="nav nav-list">
                    <li><div class="left-bg"></div></li>
                    <li class="time">
                      <h1 class="animated fadeInLeft">21:00</h1>
                      <p class="animated fadeInRight">Sat,October 1st 2029</p>
                    </li> 
                    <li class="ripple"><a href="/home#/"><span class="fa-home fa"></span>Dashboard</a></li> 
                       @if(Auth::user()->role_id == 1) 
                    <li class="ripple">
                      <a class="tree-toggle nav-header">
                        <span class="fa-exchange fa"></span> Operasional
                        <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                        <li><a href="/operasional-view#/ops-perorangan">Profil Perorangan</a></li>
                        <li><a href="/operasional-view#/ops-non-perorangan">Profil Non Perorangan</a></li> 
                      </ul>
                    </li>
                    <li class="ripple">
                      <a class="tree-toggle nav-header">
                        <span class="fa-key fa"></span> UKK
                        <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                        <li><a href="/ukk-view#/ukk-peps">Dokumen PEPs</a></li>
                        <li><a href="/ukk-view#/ukk-dttot">Dokumen DTTOT</a></li> 
                      </ul>
                    </li>
                    <li class="ripple">
                      <a href="/operasional-view#/hrd-list-karyawan">
                         <span class="fa fa-users"></span>Data Karyawan
                      </a>
                    </li> 
                    <li class="ripple"><a href="/users-view#/list-users"><span class="fa-user fa"></span>Users</a></li> 
                    <li class="ripple"><a href="/roles-view#/list-roles"><span class="fa fa-code-fork"></span>Roles</a></li>
                       @endif
                  </ul>
                </div>
            </div>
          <!-- end: Left Menu -->

  		
          <!-- start: content -->
            <div id="content"> 
            @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
            @endif
            
                <div id="app">
                @yield('content-home')
                </div>

      		  </div>
          <!-- end: content -->

     
          
      </div>

      <!-- start: Mobile -->
      <div id="mimin-mobile" class="reverse">
        <div class="mimin-mobile-menu-list">
            <div class="col-md-12 sub-mimin-mobile-menu-list animated fadeInLeft">
                <ul class="nav nav-list">
                    
                    <li class="ripple">
                      <a href="/home#/">
                         <span class="fa-home fa"></span>Dashboard
                      </a>
                    </li> 

                  @if(Auth::user()->role_id == 1) 
                    <li class="ripple">
                      <a class="tree-toggle nav-header">
                        <span class="fa-exchange fa"></span> Operasional
                        <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                        <li><a href="/operasional-view#/ops-perorangan">Profil Perorangan</a></li>
                        <li><a href="/operasional-view#/ops-non-perorangan">Profil Non Perorangan</a></li> 
                      </ul>
                    </li>   
                    <li class="ripple">
                      <a class="tree-toggle nav-header">
                        <span class="fa-key fa"></span> UKK
                        <span class="fa-angle-right fa right-arrow text-right"></span>
                      </a>
                      <ul class="nav nav-list tree">
                        <li><a href="/ukk-view#/ukk-peps">Dokumen PEPs</a></li>
                        <li><a href="/ukk-view#/ukk-dttot">Dokumen DTTOT</a></li> 
                      </ul>
                    </li> 
                    <li class="ripple">
                      <a href="/operasional-view#/hrd-list-karyawan">
                         <span class="fa fa-users"></span>Data Karyawan
                      </a>
                    </li> 
                    <li class="ripple">
                      <a href="/users-view#/list-users">
                         <span class="fa-user fa"></span>Users
                      </a>
                    </li>  
                    <li class="ripple">
                      <a href="/roles-view#/list-roles">
                         <span class="fa fa-code-fork"></span>Roles
                      </a>
                    </li>
                  @endif

                  </ul>
            </div>
        </div>       
      </div>
      <button id="mimin-mobile-menu-opener" class="animated rubberBand btn btn-circle btn-danger">
        <span class="fa fa-bars"></span>
      </button>
       <!-- end: Mobile -->

    <!-- start: Javascript -->
    <script src="{{('assets/js/jquery.min.js')}}"></script>
    <script src="{{('assets/js/jquery.ui.min.js')}}"></script>
    <script src="{{('assets/js/bootstrap.min.js')}}"></script>
   
    
    <!-- plugins -->
    <script src="{{('assets/js/plugins/moment.min.js')}}"></script> 
    <script src="{{('assets/js/plugins/jquery.nicescroll.js')}}"></script> 


    <!-- custom -->
     <script src="{{('assets/js/main.js')}}"></script> 
  </body>
</html>