<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

  <meta charset="utf-8"> 
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="author" content="@adit_xxx_">
  <title>{{config('app.name')}}</title>

  <!-- start: Css -->
  <link rel="stylesheet" type="text/css" href="{{('assets-miminium/css/bootstrap.min.css')}}">

  <!-- plugins -->
  <link rel="stylesheet" type="text/css" href="{{('assets-miminium/css/plugins/font-awesome.min.css')}}"/>
  <link rel="stylesheet" type="text/css" href="{{('assets-miminium/css/plugins/simple-line-icons.css')}}"/>
  <link rel="stylesheet" type="text/css" href="{{('assets-miminium/css/plugins/animate.min.css')}}"/>
  <link rel="stylesheet" type="text/css" href="{{('assets-miminium/css/plugins/icheck/skins/flat/aero.css')}}"/>
  <link href="{{('assets-miminium/css/style.css')}}" rel="stylesheet">
  <!-- end: Css --> 
  <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    </head>

    <body id="mimin" class="dashboard form-signin-wrapper">

      <div class="container">

      @yield('content-login')

      </div>

      <!-- end: Content -->
      <!-- start: Javascript -->
      <script src="{{('assets-miminium/js/jquery.min.js')}}"></script>
      <script src="{{('assets-miminium/js/jquery.ui.min.js')}}"></script>
      <script src="{{('assets-miminium/js/bootstrap.min.js')}}"></script>

      <script src="{{('assets-miminium/js/plugins/moment.min.js')}}"></script>
      <script src="{{('assets-miminium/js/plugins/icheck.min.js')}}"></script>

      <!-- custom -->
      <script src="{{('assets-miminium/js/main.js')}}"></script>
      <script type="text/javascript">
       $(document).ready(function(){
         $('input').iCheck({
          checkboxClass: 'icheckbox_flat-aero',
          radioClass: 'iradio_flat-aero'
        });
       });
     </script>
     <!-- end: Javascript -->
   </body>
   </html>