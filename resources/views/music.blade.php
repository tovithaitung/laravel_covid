@extends('master')
@section('title')
DashBoard StockHomeVn
@endsection
@section('content')
<div class="container">
  <div class="row">
    <div class="col-12">
      <div class="c-table-responsive@wide">
        <table class="c-table">
          <thead class="c-table__head">
            <tr class="c-table__row">
              <th class="c-table__cell c-table__cell--head">STT</th>
              <th class="c-table__cell c-table__cell--head">Tên bài</th>
              <th class="c-table__cell c-table__cell--head">List Video</th>
              <th class="c-table__cell c-table__cell--head">Nhạc</th>
              <th class="c-table__cell c-table__cell--head">Link</th>
            </tr>
          </thead>

          <tbody>
            <tr class="c-table__row">
              <?php foreach ($list as $item) { ?> 
              <td class="c-table__cell"><?php echo $item->music_id ?></td>
              <td class="c-table__cell"><?php echo $item->title ?></td>
              <td class="c-table__cell"><a href="/music/{{$item->mid}}" target="_blank">List Video</a></td>
              <td><audio  width="320" height="240" controls>
                <source src="<?php echo $item->url ?>" type="audio/mpeg">
              </audio></td>
              <td class="c-table__cell"><?php echo $item->url ?></td>
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
