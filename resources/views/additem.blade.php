@extends('master')
@section('title')
Thêm tài nguyên
@endsection
@section('content')
<div class="container">
	<div class="row u-mt-xlarge">
	    <div class="col-12">
	      <h3 class="u-mb-small">Thêm Link</h3>
	    </div>
  	</div>

	<div class="row">
		<div class="col-12">
		  <div class="row u-mb-medium">
		    <div class="col-12">
		      <div class="c-card">
		        <form action="/addUrl" method="POST">
		          {{csrf_field()}}
		        <div class="row u-mb-medium">
		          <div class="col-lg-6 u-mb-xsmall">
		            <div class="c-field">
		              <label class="c-field__label" for="input1">Tiêu đề</label>
		              <input class="c-input" name="url" type="text" id="url" placeholder="Enter your email">
		            </div>
		          </div>
		          <div class="col-lg-6 u-mb-xsmall">
		            <div class="c-field">
		              <label class="c-field__label" for="input1">File</label>
		              <input class="c-input" name="url" type="file" id="url" placeholder="Enter your email">
		            </div>
		          </div>
		          <div class="col-lg-6 u-mb-xsmall">
		            <div class="c-field">
		              <label class="c-field__label" for="input1">Ảnh đại diện</label>
		              <input class="c-input" name="url" type="file" id="url" placeholder="Enter your email">
		            </div>
		          </div>
		          <div class="col-lg-6 u-mb-xsmall">
		            <div class="c-field u-block u-mb-xsmall">
	              		<div class="c-switch u-mr-small">
	              			<label class="c-field__label" for="input1">Premium</label>
	              			<label>
			              	<input class="c-switch__input" id="switch1" type="checkbox" checked>
                			<span class="c-switch__label">Có</span>
                			</label>
		            	</div>
		            </div>
		          </div>
		          <div class="col-lg-3 u-mb-xsmall">
		            <label class="c-field__label" for="input1">Thể loại</label>
		            <div class="c-select">
		              <select class="c-select__input" name="country" id="country">
		                <option value="1">Background</option>
		                <option value="2">TQ</option>
		                <option value="3">Hàn Xẻng</option>
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
</div>
@endsection