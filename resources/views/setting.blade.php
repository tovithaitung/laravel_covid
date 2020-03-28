@extends('master')
@section('title')
Cài đặt
@endsection
@section('content')
<div class="container">
  <div class="row u-mt-xlarge">
    <div class="col-12">
      <h3 class="u-mb-small">Cài đặt</h3>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="row u-mb-medium">
        <div class="col-12">
          <div class="c-card">
            <form action="/setting" method="POST">
              {{csrf_field()}}
            <div class="row u-mb-medium">
              <div class="col-lg-4 u-mb-xsmall">
                <div class="c-field">
                  <label class="c-field__label" for="input1">Trên 50k</label>
                  <input class="c-input" name="type" type="text" id="url" value="{{$type->discount}}">
                </div>
              </div>
              <div class="col-lg-4 u-mb-xsmall">
                <div class="c-field">
                  <label class="c-field__label" for="input1">Dưới 50k</label>
                  <input class="c-input" name="type-2" type="text" id="url" value="{{$type2->discount}}">
                </div>
              </div>
              <div class="col-lg-1 u-mb-xsmall">
                <label class="c-field__label" for="input1">Cập nhật</label>
                <button type="submit" class="c-btn c-btn--info">
                Sửa
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
