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

        <h3 class="c-text--subtitle">Tổng Link</h3>
        <h1><?php echo $totalPage ?></h1>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--danger u-mb-small">
          <i class="feather icon-shopping-cart"></i>
        </span>

        <h3 class="c-text--subtitle">Tổng hàng</h3>
        <h1><?php echo $total_author ?></h1>
      </div>
    </div>
	  <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--danger u-mb-small">
          <i class="feather icon-zap"></i>
        </span>

        <h3 class="c-text--subtitle">Tổng Tag</h3>
        <h1><?php echo $total_tag ?></h1>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--success u-mb-small">
          <i class="feather icon-users"></i>
        </span>

        <h3 class="c-text--subtitle">Tổng video</h3>
        <h1><?php echo $total_video ?></h1>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--danger u-mb-small">
          <i class="feather icon-zap"></i>
        </span>

        <h3 class="c-text--subtitle">Video check</h3>
        <h1><?php echo $video_check ?></h1>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--success u-mb-small">
          <i class="feather icon-users"></i>
        </span>

        <h3 class="c-text--subtitle">Author check</h3>
        <h1><?php echo $author_check ?></h1>
      </div>
    </div>
    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--success u-mb-small">
          <i class="feather icon-users"></i>
        </span>

        <h3 class="c-text--subtitle">Video 7 ngày</h3>
        <h1><?php echo $video_time ?></h1>
      </div>
    </div>

    <!--<div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--warning u-mb-small">
          <i class="feather icon-zap"></i>
        </span>

        <h3 class="c-text--subtitle">Revenue</h3>
        <h1>$8794</h1>
      </div>
    </div>-->
  </div>
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
              <div class="col-lg-8 u-mb-xsmall">
                <div class="c-field">
                  <label class="c-field__label" for="input1">URL</label>
                  <input class="c-input" name="url" type="text" id="url" placeholder="Enter your email">
                </div>
              </div>
              <div class="col-lg-3 u-mb-xsmall">
                <label class="c-field__label" for="input1">Quốc gia</label>
                <div class="c-select">
                  <select class="c-select__input" name="country" id="country">
                    <option value="1">Việt Nam</option>
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
  <div class="row">
    <div class="col-12">
      <div class="c-table-responsive@wide">
        <table class="c-table">
          <thead class="c-table__head">
            <tr class="c-table__row">
              <th class="c-table__cell c-table__cell--head">STT</th>
              <th class="c-table__cell c-table__cell--head">Like</th>
              <th class="c-table__cell c-table__cell--head">Ngày tạo</th>
              <th class="c-table__cell c-table__cell--head">Signature</th>
              <th class="c-table__cell c-table__cell--head">Video</th>
            </tr>
          </thead>

          <tbody>
            <tr class="c-table__row">
              <?php foreach ($list as $item) { ?> 
              <td class="c-table__cell"><?php echo $item->id ?></td>
              <td class="c-table__cell"><?php echo $item->like ?></td>
              <td class="c-table__cell"><?php echo date('d-m-y',$item->create_time) ?></td>
              <td class="c-table__cell"><?php echo $item->signature ?></td>
              <td><video width="320" height="240" controls>
                <source src="<?php echo $item->video_url ?>" type="video/mp4">
              </video></td>
            </tr>
             <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="row u-mt-xlarge">
    <div class="col-md-4"></div>
    <div class="col-md-4">
      <?php $page_total = $totalPage; if(isset($_GET['page'])){ $page = $_GET['page'];
            } else {$page = 1;}?>
            <?php if(count($list) == 10){ ?>
              <ul class="c-pagination u-mb-medium">
                  <?php if($page != 1){ ?>
                  <li><a class="c-pagination__link" href="?page=<?php echo ($page-1); ?>" ><</a></li>
                  <?php } ?>
                  <?php if($page - 1 == 0){$min = 1;} else {$min = $page-1;} if($page +2 < $page_total){$max = $page + 2;} else {$max = $page_total;}  for ($i=$min ; $i <= $max; $i++) {  ?>
                    <li><a class="c-pagination__link <?php if($page == $i) { ?> is-active <?php } ?>"  href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                  <?php } ?>
                  <?php if($page != $page_total){ ?>
                  <li title="Next page"><a class="c-pagination__link" href="?page=<?php echo $page+1; ?>"><i class="feather icon-chevron-right"></i></a></li>
                  <?php } ?>
              </ul>
            <?php } ?>
    </div>
    <div class="col-md-4"></div>
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
<script type="text/javascript">
    
</script>
@endsection
