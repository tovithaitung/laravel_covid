@extends('master')
@section('title')
DashBoard StockHomeVn
@endsection
@section('content')
<div class="container">
  <div class="row u-mt-xlarge">
    <div class="col-12">
      <h3 class="u-mb-small">Upload File CVS</h3>
      <p>@if($message){{$message}} @endif</p>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="row u-mb-medium">
        <div class="col-12">
          <div class="c-card">
            <form action="/upload" method="POST" enctype="multipart/form-data">
              {{csrf_field()}}
            <div class="row u-mb-medium">
              <div class="col-lg-3 u-mb-xsmall">
                <div class="c-field">
                  <label class="c-field__label" for="input1">Tên domain</label>
                  <input class="c-input" name="domain" type="text"  placeholder="Enter your email">
                </div>
              </div>
              <div class="col-lg-3 u-mb-xsmall">
                <div class="c-field">
                  <label class="c-field__label" for="input1">File</label>
                  <input class="c-input" value=""  name="domain_csv" type="file"  placeholder="Enter your email">
                </div>
              </div>
              <div class="col-lg-3 u-mb-xsmall">
                <label class="c-field__label" for="input1">Thao tác</label>
                <button type="submit" class="c-btn c-btn--info">
                Tải lên
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
