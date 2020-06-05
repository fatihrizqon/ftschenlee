<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{{asset('img/logo-fav.png')}}">
    <title>@yield('title')</title>
    <link rel="stylesheet" type="text/css" href="{{asset('lib/perfect-scrollbar/css/perfect-scrollbar.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('lib/material-design-icons/css/material-design-iconic-font.min.css')}}"/><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="{{asset('lib/jquery.vectormap/jquery-jvectormap-1.2.2.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('lib/jqvmap/jqvmap.min.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('lib/datetimepicker/css/bootstrap-datetimepicker.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('css/style.css')}}" type="text/css"/>
    @yield('css')
  </head>
  <body>
    <div class="be-wrapper be-fixed-sidebar">
      <nav class="navbar navbar-default navbar-fixed-top be-top-header">
        <div class="container-fluid">
          <div class="navbar-header"><a href="#" class="navbar-brand"></a></div>
        </div>
      </nav>
      <div class="be-left-sidebar">
        <div class="left-sidebar-wrapper"><a href="#" class="left-sidebar-toggle">Dashboard</a>
          <div class="left-sidebar-spacer">
            <div class="left-sidebar-scroll">
              <div class="left-sidebar-content">
                <ul class="sidebar-elements">
                  <li class="divider">Menu</li>
                  <li class="@yield('Dataset')"><a href="{{route('dataset')}}"><i class="icon mdi mdi-assignment"></i><span>Perhitungan</span></a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="be-content">
          @yield('content')
      </div>
    </div>
    <script src="{{asset('lib/jquery/jquery.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('lib/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/main.js')}}" type="text/javascript"></script>
    <script src="{{asset('lib/bootstrap/dist/js/bootstrap.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('lib/jquery.gritter/js/jquery.gritter.js')}}" type="text/javascript"></script>
    @yield('js')
    <script type="text/javascript">
      $(document).ready(function(){
      	//initialize the javascript
      	App.init();

      });
    </script>
  </body>
</html>
