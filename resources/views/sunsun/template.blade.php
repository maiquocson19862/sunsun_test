<!doctype html>
<html lang='{{ str_replace("_", "-", app()->getLocale()) }}'>
    <head>
        <meta charset='utf-8'>
        <meta name='csrf-token' content='{{ csrf_token() }}'>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        @yield('head')

    </head>
    <body>
		@yield('beforebody')

		@yield('header')

		@yield('main')

		@yield('footer')

        <script>
            var _date_holiday = @php print json_encode(Session::get("date_holiday")); @endphp;
        </script>

        @yield('script')
    </body>
</html>
