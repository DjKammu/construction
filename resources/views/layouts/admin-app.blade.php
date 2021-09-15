<!-- <!doctype html> -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DOCUMENT MANAGEMENT') }} -  @yield('title')</title>

        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <!-- CSS Files -->
     <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/paper-dashboard.css?v=2.1.1') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" />

    <!--   Core JS Files   -->
    <script src="{{ asset('js/core/jquery.min.js') }}"></script>
    <!-- <script src="{{ asset('js/core/popper.min.js') }}"></script> -->
    <script src="{{ asset('js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('js/plugins/moment.min.js') }}"></script>
    <!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
    <script src="{{ asset('js/plugins/bootstrap-switch.js') }}"></script>
    <!--  Plugin for Sweet Alert -->
    <script src="{{ asset('js/plugins/sweetalert2.min.js') }}"></script>
    <!-- Forms Validations Plugin -->
    <script src="{{ asset('js/plugins/jquery.validate.min.js') }}"></script>
    <!--  Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
    <script src="{{ asset('js/plugins/jquery.bootstrap-wizard.js') }}"></script>
    <!--    Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
    <script src="{{ asset('js/plugins/bootstrap-selectpicker.js') }}"></script>
    <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
    <script src="{{ asset('js/plugins/bootstrap-datetimepicker.js') }}"></script>
    <!--  DataTables.net Plugin, full documentation here: https://datatables.net/    -->
    <!-- <script src="{{ asset('js/plugins/jquery.dataTables.min.js') }}"></script> -->
    <!--    Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
    <script src="{{ asset('js/plugins/bootstrap-tagsinput.js') }}"></script>
    <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
    <script src="{{ asset('js/plugins/jasny-bootstrap.min.js') }}"></script>
    <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
    <script src="{{ asset('js/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
    <script src="{{ asset('js/plugins/fullcalendar/daygrid.min.js') }}"></script>
    <script src="{{ asset('js/plugins/fullcalendar/timegrid.min.js') }}"></script>
    <script src="{{ asset('js/plugins/fullcalendar/interaction.min.js') }}"></script>
    <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
    <script src="{{ asset('js/plugins/jquery-jvectormap.js') }}"></script>
    <!--  Plugin for the Bootstrap Table -->
    <!-- <script src="{{ asset('js/plugins/nouislider.min.js') }}"></script> -->
    <!-- Chart JS -->
    <!-- <script src="{{ asset('js/plugins/chartjs.min.js') }}"></script> -->
    <!--  Notifications Plugin    -->
    <script src="{{ asset('js/plugins/bootstrap-notify.js') }}"></script>
    <!-- Control Center for Now Ui Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('js/paper-dashboard.min.js?v=2.1.1') }}" type="text/javascript"></script>
    <script src="{{ asset('js/demo/demo.js') }}"></script>




  </head>

  <body >
    @include('layouts.sidebar')

     <div class="main-panel">

     @include('layouts.nav')

    <div class="content">
       
        @yield('content')

        </div>
    </div>
       
  </body>

@yield('pagescript')
                    
  </html>

