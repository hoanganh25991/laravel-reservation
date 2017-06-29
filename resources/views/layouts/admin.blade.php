<!DOCTYPE html>
<html>
<head>
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ url('css/font-awesome.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ url_mix('css/admin.css') }}" rel="stylesheet">
    <style>
        .table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
            padding: 2px;
        }

    </style>
    @stack('css')
</head>
<body>

<div style="width: 100%; height: 100%">
    @include('admin.navigator')
    @yield('content')
</div>
<script src="{{ url('js/jquery.min.js') }}"></script>
<script src="{{ url('js/flat-ui.min.js') }}"></script>
<script src="{{ url('js/vue.min.js') }}"></script>
<script src="{{ url('js/moment.min.js') }}"></script>
<script src="{{ url('js/redux.min.js') }}"></script>
@stack('before-script')
@stack('script')
@stack('before-body')
</body>
</html>