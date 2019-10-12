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
  <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">
      <!-- plugins --> 
  <link rel="stylesheet" type="text/css" href="{{ asset('assets-miminium/css/plugins/font-awesome.min.css')}}"/>
  <link id="gull-theme" rel="stylesheet" href="{{('/assets-gull/styles/css/themes/lite-orange.min.css')}}">
  <link rel="stylesheet" href="{{('/assets-gull/styles/vendor/perfect-scrollbar.css')}}"> 
  <link rel="stylesheet" href="{{('/assets-gull/styles/vendor/datatables.min.css')}}">  
  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <!-- end: Css --> 
  
<script>
   window.Laravel = {!! json_encode([
    'csrfToken' => csrf_token(),
  ]) !!};
</script>
</head>

<body class="text-left"> 
<div class="app-admin-wrap layout-horizontal-bar clearfix">

<!-- header top menu end -->
  <div class="main-header"> 

    <div class="menu-toggle">
      <div></div>
      <div></div>
      <div></div>
    </div> 

    <div class="d-flex align-items-center">
      <div class="search-bar">
        <div class="logo">
          <img src="{{('/images/logo_big.png')}}" alt="">
        </div>
      </div>
    </div>

    <div style="margin: auto"></div>
      <div class="header-part-right">
        <!-- Full screen toggle -->
        <i class="i-Full-Screen header-icon d-none d-sm-inline-block" data-fullscreen></i> 

        <!-- User avatar dropdown -->
        <div class="dropdown">
          <div  class="user col align-self-end">
            <img src="{{('/img/user.png')}}" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
              <div class="dropdown-header">
                <i class="i-Lock-User mr-1"></i> {{ Auth::user()->name }}
              </div>
              <a href="/users-view#/profile" class="dropdown-item">Profil</a>
              <a href="/users-view#/change-password" class="dropdown-item">Rubah Password</a>
              <a class="dropdown-item"  href="{{ route('logout') }}" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">Sign out</a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </div>
          </div>
        </div> 
      </div>  
  </div>
<!-- header top menu end -->

  <div class="horizontal-bar-wrap">
    <div class="header-topnav">
      <div class="container-fluid">
        <div class=" topnav rtl-ps-none" id="" data-perfect-scrollbar data-suppress-scroll-x="true">
          <ul class="menu float-left">


            <li class="">
              <div>
                <div>
                  <label class="toggle" for="drop-2">Dashboard</label>
                  <a href="/home#/"><i class="nav-icon mr-2 i-Home-Window"></i>Dashboard</a> 
                </div>
              </div>
            </li>

              @if(Auth::user()->role_id == 1) 
              <li class="">
                <div>
                  <div>
                    <label class="toggle" for="drop-2">Administrator</label>
                    <a href="#"><i class="nav-icon mr-2 i-Medal-2"></i> Administrator</a> 
                    <input type="checkbox" id="drop-2">
                    <ul>
                      <li class="nav-item ">
                        <a href="/users-view#/list-users">
                        <i class="nav-icon mr-2 fa fa-user"></i>
                        <span class="item-name">Users</span>
                        </a>
                      </li> 
                      <li class="nav-item ">
                        <a href="/roles-view#/list-roles">
                        <i class="nav-icon mr-2 fa fa-gear"></i>
                        <span class="item-name">Roles</span>
                        </a>
                      </li> 
                    </ul>
                  </div>
                </div>
              </li> 
              @endif

            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6 || Auth::user()->role_id == 2) 
            <li class="">
              <div>
                <div>
                  <label class="toggle" for="drop-2">Operasional</label>
                  <a href="#"><i class="nav-icon mr-2 i-Money-2"></i>Operasional</a> 
                  <input type="checkbox" id="drop-2">
                  <ul>
                    <li class="nav-item ">
                      <a href="/operasional-view#/ops-perorangan">
                        <i class="nav-icon mr-2 i-Checked-User"></i>
                        <span class="item-name">Profil Perorangan</span>
                      </a>
                    </li>
                    <li class="nav-item ">
                      <a href="/operasional-view#/ops-non-perorangan">
                        <i class="nav-icon mr-2 i-Business-ManWoman"></i>
                        <span class="item-name">Profil Non Perorangan</span>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </li>
            @endif

            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 6 || Auth::user()->role_id == 3) 
            <li class="">
              <div>
                <div>
                  <label class="toggle" for="drop-2">HRD</label>
                  <a href="#"><i class="nav-icon mr-2 fa fa-users"></i> HRD</a> 
                  <input type="checkbox" id="drop-2">
                  <ul>
                    <li class="nav-item ">
                      <a href="/operasional-view#/hrd-chart-karyawan">
                      <i class="nav-icon mr-2 i-Bar-Chart"></i>
                      <span class="item-name">Chart Karyawan</span>
                      </a>
                    </li> 
                    <li class="nav-item ">
                      <a href="/operasional-view#/hrd-list-jabatan-karyawan">
                      <i class="nav-icon mr-2 i-Checked-User"></i>
                      <span class="item-name">Jabatan Karyawan</span>
                      </a>
                    </li> 
                    <li class="nav-item ">
                      <a href="/operasional-view#/hrd-list-divisi-karyawan">
                      <i class="nav-icon mr-2 i-Library"></i>
                      <span class="item-name">Divisi Karyawan</span>
                      </a>
                    </li> 
                    <li class="nav-item ">
                      <a href="/operasional-view#/hrd-list-karyawan">
                      <i class="nav-icon mr-2 fa fa-users"></i>
                      <span class="item-name">Data Karyawan</span>
                      </a>
                    </li> 
                  </ul>
                </div>
              </div>
            </li> 
            @endif

            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 7 || Auth::user()->role_id == 4) 
            <li class="">
              <div>
                <div>
                  <label class="toggle" for="drop-2">UKK</label>
                  <a href="#"><i class="nav-icon i-Library mr-2"></i> UKK</a> 
                  <input type="checkbox" id="drop-2">
                  <ul>
                    <li class="nav-item ">
                      <a href="/ukk-view#/ukk-peps">
                      <i class="nav-icon mr-2 i-File-Horizontal-Text"></i>
                      <span class="item-name">Dokumen PEPS</span>
                      </a>
                    </li> 
                    <li class="nav-item ">
                      <a href="/ukk-view#/ukk-dttot">
                      <i class="nav-icon mr-2 i-File-Horizontal-Text"></i>
                      <span class="item-name">Dokumen DTTOT</span>
                      </a>
                    </li> 
                  </ul>
                </div>
              </div>
            </li> 
            @endif


          </ul>
        </div>
      </div>
    </div>  
  </div>


  
<!-- ============ Body content start ============= -->
  <div class="main-content-wrap  d-flex flex-column">
    <div class="main-content">
      <div id="app">
        @yield('content-home')
      </div>
    </div>
 
  </div>


</div>
     
<script src="{{('/assets-gull/js/common-bundle-script.js')}}"></script>
<script src="{{('/assets-gull/js/es5/dashboard.v1.script.js')}}"></script>
<script src="{{('/assets-gull/js/script.js')}}"></script>
<script src="{{('/assets-gull/js/sidebar-horizontal.script.js')}}"></script>
<script src="{{('/assets-gull/js/customizer.script.js')}}"></script>  
 
  </body>
</html>