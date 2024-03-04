<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport"            content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description"         content="{{ !empty($metaDescription) ? $metaDescription : '' }}">

    <meta property="og:url"          content="{{ !empty($pageUrl) ? $pageUrl : '' }}" />
    <meta property="og:type"         content="website" />
    <meta property="og:title"        content="{{ !empty($metaTitle) ? $metaTitle : '' }}" />
    <meta property="og:description"  content="{{ !empty($metaDescription) ? $metaDescription : '' }}" />
    <meta property="og:image"        content="{{ !empty($metaImage) ? $metaImage : '' }}" />
    <meta property="og:image:width"  content="600" />
    <meta property="og:image:height" content="600" />

    <meta itemprop="name"            content="{{ !empty($metaTitle) ? $metaTitle : '' }}">
    <meta itemprop="description"     content="{{ !empty($metaDescription) ? $metaDescription : '' }}">
    <meta itemprop="image"           content="{{ !empty($metaImage) ? $metaImage : '' }}"> 

    <meta property="title"            content="{{!empty($metaTitle) ? $metaTitle : ''}}">
    <meta property="description"     content="{{ !empty($metaDescription) ? $metaDescription : '' }}">

    <title>{{ $pageTitle }}</title>

    <style>
        :root {
            --main-color: {{ $frontTheme->primary_color }};
        }

        {!! $frontTheme->front_custom_css !!}
    </style>

    <!-- Styles -->
    <link href="{{ asset('froiden-helper/helper.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/node_modules/toast-master/css/jquery.toast.css') }}" rel="stylesheet">

    <link href="{{ asset('front/assets/css/core.min.css') }}" rel="stylesheet">
    <link href="{{ asset('front/assets/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('front/assets/css/thesaas.min.css') }}" rel="stylesheet">
    <link href="{{ asset('front/assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('front/assets/css/custom.css') }}" rel="stylesheet">
    @stack('header-css')
    <link rel='stylesheet prefetch'
    href='//cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css'>
    <link href="{{ asset('assets/node_modules/sweetalert/sweetalert.css') }}" rel="stylesheet">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">
    @stack('style')
    <style>
        .dropdown-toggle::after {
            right: 22px !important;
        }
    </style>
</head>

<body>


<!-- Topbar -->
<nav class="topbar topbar-inverse topbar-expand-md">
    <div class="container">

        <div class="topbar-left">
            <button class="topbar-toggler">&#9776;</button>
            <a class="topbar-brand" href="{{ url('/') }}">
                <img src="{{ $global->logo_url }}" class="logo-inverse" alt="home" />
            </a>
        </div>
        
            

        @if($global->front_language == 1)
        <div class="topbar-right">
            <div class="d-inline-flex ml-200 language-drop">

                <div class="dropdown btn btn-default" style="margin-left: 390px;">
                    <a href="#" class="dropdown-toggle text-capitalize" data-toggle="dropdown">
                        <i class="flag-icon @if($language->language_code == 'en') flag-icon-us @else  flag-icon-{{ $language->language_code }} @endif"></i> {{$language->language_name}}
                    </a>
                    <div class="dropdown-menu" style="margin-left: 10px">
                        
                        @forelse ($languageSettings as $language)
                            <a class="dropdown-item" data-lang-code="{{ $language->language_code }}" href="javascript:;"> <span class="flag-icon @if($language->language_code == 'en') flag-icon-us @elseif($language->language_code == 'ar') flag-icon-ae @elseif($language->language_code == 'es-ar') flag-icon-ar @else   flag-icon-{{ $language->language_code }} @endif"></span> {{ $language->language_name }}</a>
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif
 @if($global->job_alert_status == 1)
    @if( isset($alertId) && !is_null($alertId))
        <div class="topbar-left">
               
               <button class="btn btn-danger disable-job-alert" type="button">Disable job alert</button>
               </div>
    @else
        <div class="topbar-left">
               
               <button class="btn btn-primary" type="button" id="job-alert"> Create job alert </button>
               </div>
    
    @endif
    @endif
    {{-- <div class="topbar-right">
           
        </div> --}}

    </div>
</nav>
<!-- END Topbar -->

<!-- Header -->
<header class="bg-img-shape">
        
    <div class="header inner-header" style="background-image: url({{ $frontTheme->background_image_url }})" data-overlay="8">
        <div class="container text-center">

            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">

                    @yield('header-text')

                </div>
            </div>

        </div>
    </div>
</header>
<!-- END Header -->

<!-- Main container -->
<main class="main-content">

    @yield('content')

</main>
{{-- Ajax Modal Start for --}}
    <div class="modal fade bs-modal-md in" id="addJobAlert" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="icon-plus"></i> @lang('app.department')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn blue">Save changes</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    {{-- Ajax Modal Ends --}}
<!-- END Main container -->

<!-- Footer -->
<footer class="site-footer">
    <div class="container">
        <div class="row text-center">
            <div class="col-12 col-lg-12 mb-10">
               @forelse($customPages as $customPage)
                    <a class="px-5 fw-400" href="{{route('jobs.custom-page',$customPage->slug)}}"><span>{{ ($customPage->name) }}</span></a>
               @empty
               @endforelse

            </div>
            <div class="col-12 col-lg-12 fw-400">
                <p>&copy; {{ \Carbon\Carbon::today()->year }} @lang('app.by') {{ $companyName }}</p>

            </div>
        </div>
    </div>
</footer>
<!-- END Footer -->



<!-- Scripts -->
<script src="{{ asset('front/assets/js/core.min.js') }}"></script>
{{-- <script src="{{ asset('front/assets/js/thesaas.min.js') }}"></script> --}}
<script src="{{ asset('front/assets/js/script_new.js') }}"></script>
<script src="{{ asset('front/assets/js/select2.min.js') }}"></script>
<script src="{{ asset('froiden-helper/helper.js') }}"></script>
<script src="{{ asset('assets/node_modules/toast-master/js/jquery.toast.js') }}"></script>
<script src="{{ asset('assets/node_modules/sweetalert/sweetalert.min.js') }}"></script>
<script>
    setActiveClassToLanguage();
    $('.language-drop .dropdown-item').click(function () {
        let code = $(this).data('lang-code');

        let url = '{{ route('jobs.changeLanguage', ':code') }}';
        url = url.replace(':code', code);

        if (!$(this).hasClass('active')) {
            $.easyAjax({
                url: url,
                type: 'POST',
                container: 'body',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                        setActiveClassToLanguage();
                    }
                }
            })
        }
    })
    @if(isset($alertId) && !is_null($alertId))
    $('body').on('click', '.disable-job-alert', function(event){
       var id = {{$alertId}};
           
       swal({
           title: "@lang('errors.areYouSure')",
           text: "@lang('errors.warnigJobAlert')",
           type: "warning",
           showCancelButton: true,
           confirmButtonColor: "#DD6B55",
           confirmButtonText: "@lang('app.disable')",
           cancelButtonText: "@lang('app.cancel')",
           closeOnConfirm: true,
           closeOnCancel: true
       }, function(isConfirm){
           if (isConfirm) {

               var url = "{{ route('jobs.disableJobAlert',':id') }}";
               url = url.replace(':id', id);

               var token = "{{ csrf_token() }}";

               $.easyAjax({
                   type: 'POST',
                   url: url,
                   data: {'_token': token},
                   success: function (response) {
                       if (response.status == "success") {
                           $.unblockUI();
                           table._fnDraw();
                       }
                   }
               });
           }
       });
   });
@endif
    function setActiveClassToLanguage() {
        // language switcher
        if ('{{ \Cookie::has('language_code') }}') {
            $('.language-drop .dropdown-item').filter(function () {
                return $(this).data('lang-code') === '{{ \Cookie::get('language_code') }}'
            }).addClass('active');
        }
        else {
            $('.language-drop .dropdown-item').filter(function () {
                return $(this).data('lang-code') === '{{ $global->locale }}'
            }).addClass('active');
        }
    }
    $(document).ready(function() {


    $('#job-alert').click(function() {
    var url = "{{ route('jobs.jobAlert') }}";
    console.log(url);
    $('.modal-title').html("<i class='icon-plus'></i> @lang('modules.front.jobAlert')");
    $.ajaxModal('#addJobAlert', url);
    });

    

});

    
</script>
@stack('footer-script')

</body>
</html>