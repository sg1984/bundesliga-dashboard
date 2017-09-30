<!doctype html>
<html lang="{{ app()->getLocale() }}">
    @include('templates.header')
    <body class="fixed-nav sticky-footer" id="page-top">
        @yield('content')
        @include('templates.scripts')
    </body>
</html>
