<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title')</title>
    <meta name="description" content="Neat">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <!-- Favicon -->
    <link rel="apple-touch-icon" href="apple-touch-icon.png">
    {{--<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">--}}
    <link rel="shortcut icon" href=" {{ asset('images/favicon_keyword.png') }}" type="image/x-icon">
    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('/stockhomevn/css/neat.min.css?v=1.0')}}">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <script src="https://unpkg.com/vue-select@3.0.0"></script>
    <link rel="stylesheet" href="https://unpkg.com/vue-select@3.0.0/dist/vue-select.css">


    @yield('custom-css')
  </head>
  <body>

    <div class="o-page">
      <div class="o-page__sidebar js-page-sidebar">
        <aside class="c-sidebar">
          <div class="c-sidebar__brand">
            <a href="/"><img src="{{ asset('/stockhomevn/img/logo.svg')}}" alt="Neat"></a>
          </div>
          <!-- Scrollable -->
          <div class="c-sidebar__body">
            <span class="c-sidebar__title">Dashboards</span>
            <ul class="c-sidebar__list">
              
            </ul>
          </div>
          

          <a class="c-sidebar__footer" href="<?php echo route('logout') ?>"> 
            Logout <i class="c-sidebar__footer-icon feather icon-power"></i>
          </a>
        </aside>
      </div>

      <main class="o-page__content">
        <header class="c-navbar u-mb-medium">
          <button class="c-sidebar-toggle js-sidebar-toggle">
            <i class="feather icon-align-left"></i>
          </button>

          <h2 class="c-navbar__title">Expired Domain Finder</h2>
          <div class="c-dropdown dropdown">
            <div class="c-avatar c-avatar--xsmall dropdown-toggle" id="dropdownMenuAvatar" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" role="button">
                            <!--<img class="c-avatar__img" src="http://via.placeholder.com/72" alt="Adam Sandler">-->
    
            </div>

            <div class="c-dropdown__menu has-arrow dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuAvatar">
              <a class="c-dropdown__item dropdown-item" href="<?php echo route('logout') ?>">Logout</a>
            </div>
          </div>
        </header>
        @yield('content')

      </main>
    </div>

    <!-- Main JavaScript -->
    <script src="{{ asset('/stockhomevn/js/neat.min.js?v=1.0')}}"></script>
    <script src="js/bootbox/bootbox.min.js"></script>
    @yield('script-custom')
  </body>
</html>