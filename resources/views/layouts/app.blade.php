<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>HTHC</title>

    <link rel="icon" href="{!! secure_asset('images/favicon.ico') !!}"/>

    <link href="{{ secure_asset('css/app.css') }}" rel="stylesheet">

    <!-- Styles -->
{{--    <link href="{{ secure_asset('css/custom-app.css') }}" rel="stylesheet">--}}

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;1,400&display=swap" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-5e2ESR8Ycmos6g3gAKr1Jvwye8sW4U1u/cAKulfVJnkakCcMqhOudbtPnvJ+nbv7" crossorigin="anonymous">
    <link href="{{ secure_asset('css/layout-fix.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script src="{{ secure_asset('js/app.js') }}" defer></script>
{{--    <script src="{{ secure_asset('js/custom.js') }}" defer></script>--}}
    <script src="{{ secure_asset('js/navigation.js') }}" defer></script>
    <script>
        // Show loader on page load
        $(window).on('load', function() {
            $('#global-loader').fadeOut(200);
        });

        // Show loader before page unload (when navigating to a new page)
        $(window).on('beforeunload', function() {
            $('#global-loader').fadeIn(200);
        });

        // // Show/hide loader during AJAX, but ignore select2 and autocomplete requests
        // $(document).ajaxStart(function(event, xhr, settings) {
        //     // Check if the AJAX request is not from select2 or autocomplete
        //     if (!settings ||
        //         (settings.url &&
        //             !settings.url.includes('select2') &&
        //             !settings.url.includes('select') &&
        //             !settings.url.includes('autocomplete')
        //         )
        //     ) {
        //         $('#global-loader').fadeIn(200);
        //     }
        // });
        //
        // $(document).ajaxStop(function(event, xhr, settings) {
        //     // Check if the AJAX request is not from select2 or autocomplete
        //     if (!settings ||
        //         (settings.url &&
        //             !settings.url.includes('select2') &&
        //             !settings.url.includes('select') &&
        //             !settings.url.includes('autocomplete')
        //         )
        //     ) {
        //         $('#global-loader').fadeOut(200);
        //     }
        // });

        // Show loader immediately when document is ready
        $(document).ready(function() {
            $('#global-loader').fadeIn(200);

            // Initialize submenus
            $('.has-submenu.open > ul').show();

            // Add click handlers for menu toggles
            $('.has-submenu > a, .accordion').on('click', function(e) {
                e.preventDefault();
                var $submenu = $(this).next('ul, .submenu');
                $submenu.slideToggle();
                $(this).parent().toggleClass('open');
            });
        });
    </script>
    <style>
        /* Critical layout styles to ensure proper rendering */
        #app {
            display: flex;
            min-height: 100vh;
        }
        .navigation {
            width: 200px;
            min-width: 200px;
            position: fixed;
            height: 100vh;
            z-index: 1030;
        }
        .main-container {
            flex: 1;
            margin-left: 200px;
            width: calc(100% - 200px);
            padding: 1rem;
        }
        @media (max-width: 768px) {
            .main-container {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div id="app">
    @if (Request::is('admin/*'))
        <div class="navigation">
            @include('components.nav')
        </div>
    @endif

    @hasSection('content')
        <main class="main-container">
            @yield('content')
        </main>
    @else
        <main class="container">
            @yield('auth-content')
        </main>
    @endif
</div>

<div id="global-loader" style="display: none;">
    <div class="loader-overlay"></div>
    <div class="loader-content">
        <div class="spinner"></div>
        <p>Loading...</p>
    </div>
</div>
</body>
</html>
