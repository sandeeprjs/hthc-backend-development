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
    {{--    <link href="{{ secure_asset('css/app.css') }}" rel="stylesheet">--}}

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
        // Simple loader that only handles page loads/navigations
        $(window).on('load', function() {
            // Hide loader when page is fully loaded
            $('#global-loader').fadeOut(200);
        });

        // Show loader when navigating to a new page
        $(window).on('beforeunload', function() {
            $('#global-loader').fadeIn(200);
        });

        // Document ready handler
        $(document).ready(function() {
            // Hide loader once DOM is ready (but before all resources might be loaded)
            setTimeout(function() {
                $('#global-loader').fadeOut(200);
            }, 500);  // Small delay to ensure DOM is fully ready

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

        // Emergency failsafe - if the loader is shown for more than 5 seconds, hide it
        setInterval(function() {
            if ($('#global-loader').is(':visible')) {
                $('#global-loader').fadeOut(200);
            }
        }, 5000);
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

        /* Loader styles */
        #global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
        }

        .loader-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
        }

        .loader-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            margin: 0 auto 10px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
