<!DOCTYPE html>
<html class="no-js" lang="vi">

<head>
    <meta charset="UTF-8">
    <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">




    <meta name="csrf-token" content="{{ csrf_token() }}">



    <meta name="description" content="">
    <meta name="author" content="">



    @if (!empty($meta_description))
        <meta name="description" content="{{ $meta_description }}">
    @endif

    @if (!empty($meta_keywords))
        <meta name="keywords" content="{{ $meta_keywords }}">
    @endif

    <title>


        @if (!empty($meta_title))
            {{ $meta_title }}
        @else
            Ứng dụng Thương mại Điện tử Đa nhà bán hàng
        @endif

    </title>
    <link href="favicon.ico" rel="shortcut icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,800" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('front/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('front/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ url('front/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ url('front/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ url('front/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ url('front/css/jquery-ui-range-slider.min.css') }}">
    <link rel="stylesheet" href="{{ url('front/css/utility.css') }}">
    <link rel="stylesheet" href="{{ url('front/css/bundle.css') }}">





    <link rel="stylesheet" href="{{ url('front/css/easyzoom.css') }}">




    <link rel="stylesheet" href="{{ url('front/css/custom.css') }}">



</head>

<body>



    <div class="loader">
        <img src="{{ asset('front/images/loaders/loader.gif') }}" alt="Đang tải..." />
    </div>



    <!-- app -->
    <div id="app">

        {{-- Header partial --}}
        @include('front.layout.header')


        {{-- Middle Content (varies from a page to another) --}}
        @yield('content')


        {{-- Footer partial --}}
        @include('front.layout.footer')


        {{-- Modal Popup partial --}}
        @include('front.layout.modals')

    </div>
    <!-- NoScript -->
    <noscript>
        <div class="app-issue">
            <div class="vertical-center">
                <div class="text-center">
                    <h1>JavaScript bị tắt trong trình duyệt của bạn.</h1>
                    <span>Vui lòng bật JavaScript trong trình duyệt hoặc nâng cấp lên trình duyệt hỗ trợ
                        JavaScript.</span>
                </div>
            </div>
        </div>
        <style>
            #app {
                display: none;
            }
        </style>
    </noscript>
    <script>
        window.ga = function () {
            ga.q.push(arguments)
        };
        ga.q = [];
        ga.l = +new Date;
        ga('create', 'UA-XXXXX-Y', 'auto');
        ga('send', 'pageview')
    </script>
    <script src="https://www.google-analytics.com/analytics.js" async defer></script>
    <script type="text/javascript" src="{{ url('front/js/vendor/modernizr-custom.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/nprogress.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/jquery.scrollUp.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/jquery.elevatezoom.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/jquery-ui.range-slider.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/jquery.slimscroll.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/jquery.resize-select.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/jquery.custom-megamenu.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/jquery.custom-countdown.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/owl.carousel.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('front/js/app.js') }}"></script>



    <script type="text/javascript" src="{{ url('front/js/custom.js') }}"></script>





    <script type="text/javascript" src="{{ url('front/js/easyzoom.js') }}"></script>
    <script>
        // Instantiate EasyZoom instances
        var $easyzoom = $('.easyzoom').easyZoom();

        // Setup thumbnails example
        var api1 = $easyzoom.filter('.easyzoom--with-thumbnails').data('easyZoom');

        $('.thumbnails').on('click', 'a', function (e) {
            var $this = $(this);

            e.preventDefault();

            // Use EasyZoom's `swap` method
            api1.swap($this.data('standard'), $this.attr('href'));
        });

        // Setup toggles example
        var api2 = $easyzoom.filter('.easyzoom--with-toggle').data('easyZoom');

        $('.toggle').on('click', function () {
            var $this = $(this);

            if ($this.data("active") === true) {
                $this.text("Switch on").data("active", false);
                api2.teardown();
            } else {
                $this.text("Switch off").data("active", true);
                api2._init();
            }
        });
    </script>




    @include('front.layout.scripts') {{-- scripts.blade.php --}}



</body>

</html>