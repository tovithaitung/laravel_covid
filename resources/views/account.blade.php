@extends('master')
@section('title')
DashBoard StockHomeVn
@endsection
@section('content')
<div class="container">
  <div class="row u-mt-xlarge">
    <div class="col-12">
      <h3 class="u-mb-small">Thêm tài khoản</h3>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="row u-mb-medium">
        <div class="col-12">
          <div class="c-card">
            <form action="/addaccount" method="POST">
              {{csrf_field()}}
            <div class="row u-mb-medium">
              <div class="col-lg-4 u-mb-xsmall">
                <div class="c-field">
                  <label class="c-field__label" for="input1">UserName</label>
                  <input class="c-input" name="username" type="text" id="url" placeholder="Enter your email">
                </div>
              </div>
              <div class="col-lg-4 u-mb-xsmall">
                <div class="c-field">
                  <label class="c-field__label" for="input1">Mật khẩu</label>
                  <input class="c-input" name="password" type="text" id="url" placeholder="Enter your email">
                </div>
              </div>
              <div class="col-lg-3 u-mb-xsmall">
                <label class="c-field__label" for="input1">Loại</label>
                <div class="c-select">
                  <select class="c-select__input" name="role" id="country">
                    <?php foreach ($list as $item) { ?>
                    <option value="{{$item->role_id}}">{{$item->role_name}}</option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-1 u-mb-xsmall">
                <label class="c-field__label" for="input1">Thêm</label>
                <button type="submit" class="c-btn c-btn--info">
                Add
              </button>
              </div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--info u-mb-small">
          <i class="feather icon-activity"></i>
        </span>

        <h3 class="c-text--subtitle">TK nhân viên</h3>
        <h1><?php echo $totalStaff ?></h1>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--danger u-mb-small">
          <i class="feather icon-shopping-cart"></i>
        </span>

        <h3 class="c-text--subtitle">TK khách hàng</h3>
        <h1><?php echo $totalCustomer ?></h1>
      </div>
    </div>
  </div>
    <div class="row">
        <div class="col-12">
          <footer class="c-footer">
            <p>© 2018 son.local</p>
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
