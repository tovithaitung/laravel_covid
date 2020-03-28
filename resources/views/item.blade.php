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

        <h3 class="c-text--subtitle">Tổng số File đã có</h3>
        <h1><?php echo $totalFile ?></h1>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--danger u-mb-small">
          <i class="feather icon-shopping-cart"></i>
        </span>

        <h3 class="c-text--subtitle">Tổng link đã quét</h3>
        <h1><?php echo $totalLink ?></h1>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--success u-mb-small">
          <i class="feather icon-users"></i>
        </span>

        <h3 class="c-text--subtitle">Tổng số page</h3>
        <h1><?php echo $totalPage ?></h1>
      </div>
    </div>

    <div class="col-md-6 col-xl-3">
      <div class="c-card">
        <span class="c-icon c-icon--warning u-mb-small">
          <i class="feather icon-zap"></i>
        </span>

        <h3 class="c-text--subtitle">Page hiện tại</h3>
        <h1><?php echo $currentPage ?></h1>
      </div>
    </div>
  </div>

<!--  <div class="row">
    <div class="col-md-6">
      <div class="c-card u-ph-zero u-pb-zero">

        <div class="u-ph-medium">
          <h4>Sales</h4>
          <p>Activity from 1 Jan 2018 to 30 July 2018</p>

          <span class="u-h1">$45,000</span>
        </div>

        <div class="u-p-medium">
          <div class="c-chart">
            <div class="sales-chart"></div>
          </div>
        </div>
        
      </div>
    </div>

    <div class="col-md-6">
      <div class="c-card u-ph-zero u-pb-zero">

        <div class="u-ph-medium">
          <h4>Payouts</h4>
          <p>Activity from 1 Jan 2018 to 30 July 2018</p>

          <span class="u-h1">$23,420</span>
        </div>

        <div class="u-p-medium">
          <div class="c-chart">
            <div class="payouts-chart"></div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
-->
  <div class="row">
    <div class="col-12">
      <div class="c-table-responsive@wide">
        <table class="c-table">
          <thead class="c-table__head">
            <tr class="c-table__row">
              <th class="c-table__cell c-table__cell--head">Tên</th>
              <th class="c-table__cell c-table__cell--head">Link</th>
              <th class="c-table__cell c-table__cell--head">Dung lượng</th>
              <th class="c-table__cell c-table__cell--head">Ngày đăng</th>
              <th class="c-table__cell c-table__cell--head">Ngày cập nhật</th>
              <th class="c-table__cell c-table__cell--head">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr class="c-table__row">
              <?php foreach ($items as $item) { ?> 
              <td class="c-table__cell"><?php echo $item->title ?></td>
              <td class="c-table__cell"><a href="https://elements.envato.com/<?php echo $item->slug.'-'.$item->product_id ?>" target="_blank">Link</a></td>
              <td class="c-table__cell"><?php echo round($item->fileSizeBytes/1000000,2);?> MB</td>
              <td class="c-table__cell"><?php echo date('d-m-Y',$item->publishedAt); ?></td>
              <td class="c-table__cell"><?php echo date('d-m-Y',$item->updatedAt); ?></td>
              <td class="c-table__cell">
                <div class="c-dropdown dropdown">
                  <a href="#" class="c-btn c-btn--info has-icon dropdown-toggle" id="dropdownMenuTable1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    More <i class="feather icon-chevron-down"></i>
                  </a>

                  <div class="c-dropdown__menu dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuTable1">
                    <a class="c-dropdown__item dropdown-item" href="#">Link One</a>
                    <a class="c-dropdown__item dropdown-item" href="#">Link Two</a>
                    <a class="c-dropdown__item dropdown-item" href="#">Link Three</a>
                  </div>
                </div>
              </td>
             
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
            <?php if(count($items) == 10){ ?>
            <?php if(isset($_GET['category'])){ ?>
              <ul class="c-pagination u-mb-medium">
                  <?php if($page != 1){ ?>
                  <li><a class="c-pagination__link" href="?category=<?php echo urlencode($_GET['category']) ?>&page=<?php echo ($page-1); ?>" ><</a></li>
                  <?php } ?>
                  <?php if($page - 1 == 0){$min = 1;} else {$min = $page-1;} if($page +2 < $page_total){$max = $page + 2;} else {$max = $page_total;}  for ($i=$min ; $i <= $max; $i++) {  ?>
                    <li><a class="c-pagination__link <?php if($page == $i) { ?> is-active <?php } ?>"  href="?category=<?php echo urlencode($_GET['category']) ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                  <?php } ?>
                  <?php if($page != $page_total){ ?>
                  <li title="Next page"><a class="c-pagination__link" href="?category=<?php echo urlencode($_GET['category']) ?>&page=<?php echo $page+1; ?>"><i class="feather icon-chevron-right"></i></a></li>
                  <?php } ?>
              </ul>
            <?php } else { ?>
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
            <?php } } ?>
      <!--<ul class="c-pagination u-mb-medium">
        <li><a class="c-pagination__link" href="#"><i class="feather icon-chevron-left"></i> </a></li>
        <li><a class="c-pagination__link" href="#">1</a></li>
        <li><a class="c-pagination__link is-active" href="#">5</a></li>
        <li><a class="c-pagination__link" href="#">2</a></li>
        <li><a class="c-pagination__link" href="#">3</a></li>
        <li><a class="c-pagination__link" href="#">4</a></li>
        <li><a class="c-pagination__link" href="#">5</a></li>
        <li><a class="c-pagination__link" href="#"> <i class="feather icon-chevron-right"></i> </a></li>
      </ul>-->
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
@endsection
