@extends('master')
@section('title')
DashBoard StockHomeVn
@endsection
@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--info u-mb-small">
          <i class="feather icon-activity"></i>
        </span>

        <h3 class="c-text--subtitle">Tổng link</h3>
        <h1><?php echo $totalLink ?></h1>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--danger u-mb-small">
          <i class="feather icon-shopping-cart"></i>
        </span>

        <h3 class="c-text--subtitle">Tổng danh mục</h3>
        <h1><?php echo $totalCategory ?></h1>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--success u-mb-small">
          <i class="feather icon-users"></i>
        </span>

        <h3 class="c-text--subtitle">Tổng số File</h3>
        <h1><?php echo $totalFile ?></h1>
      </div>
    </div>
    <div class="row">
        <div class="col-12">
          <footer class="c-footer">
            <p>© 2018 Stockhomevn.com</p>
            <span class="c-footer__divider">|</span>
            <!--<nav>
              <a class="c-footer__link" href="#">Terms</a>
              <a class="c-footer__link" href="#">Privacy</a>
              <a class="c-footer__link" href="#">FAQ</a>
              <a class="c-footer__link" href="#">Help</a>
            </nav> -->
          </footer>
        </div>
    </div>
</div>
@endsection
