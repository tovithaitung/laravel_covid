@extends('master')
@section('title')
DashBoard StockHomeVn
@endsection
@section('content')
<div class="container">
  <div class="row u-mt-xlarge">
    <div class="col-12">
      <h3 class="u-mb-small">Đổi mật khẩu</h3>
      <p><?php if($status == true){ echo 'Đổi mật khẩu thành công'; } else { echo 'Mật khẩu không đúng. Vui lòng thử lại';} ?></p>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="row u-mb-medium">
        <div class="col-12">
          <div class="c-card">
            <form action="/password" method="POST">
              {{csrf_field()}}
            <div class="row u-mb-medium">
              <div class="col-lg-3 u-mb-xsmall">
                <div class="c-field">
                  <label class="c-field__label" for="input1">Mật khẩu cũ</label>
                  <input class="c-input" name="pass" type="password" id="url" placeholder="Enter your email">
                </div>
              </div>
              <div class="col-lg-3 u-mb-xsmall">
                <div class="c-field">
                  <label class="c-field__label" for="input1">Mật khẩu mới</label>
                  <input class="c-input" name="new-pass" type="password" id="url" placeholder="Enter your email">
                </div>
              </div>
              <div class="col-lg-3 u-mb-xsmall">
                <div class="c-field">
                  <label class="c-field__label" for="input1">Nhập lại</label>
                  <input class="c-input" name="re-new-pass" type="password" id="url" placeholder="Enter your email">
                </div>
              </div>
              <div class="col-lg-3 u-mb-xsmall">
                <label class="c-field__label" for="input1">Thao tác</label>
                <button type="submit" class="c-btn c-btn--info">
                Cập nhật
              </button>
              </div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
