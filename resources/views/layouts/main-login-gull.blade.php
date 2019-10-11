<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8"> 
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="author" content="@adit_xxx_">
<title>{{config('app.name')}}</title>

<link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">

<!-- start: Css -->
<link rel="stylesheet" type="text/css" href="{{('/assets-gull/styles/css/themes/lite-orange.min.css')}}">

<script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};
</script>

</head>

<body>
    
<!-- Pre Loader Strat  -->
<div class='loadscreen' id="preloader">
    <div class="loader spinner-bubble spinner-bubble-primary"></div>
</div>
<!-- Pre Loader end  -->

    <div class="auth-layout-wrap" style="background-image: url({{('/images/bg-login.jpg')}})">
        <div class="auth-content">
            <div class="card o-hidden">

            @yield('content-login')

                
            </div>
        </div>
    </div>
    


<!-- start: Javascript -->
<script src="{{('/assets-gull/js/common-bundle-script.js')}}"></script>
<script src="{{('/assets-gull/js/script.js')}}"></script>
<script src="{{('/assets-gull/js/form.validation.script.js')}}"></script> 
</body>
</html>
