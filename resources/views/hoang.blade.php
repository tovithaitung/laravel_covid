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
              <th class="c-table__cell c-table__cell--head">URL</th>
            </tr>
          </thead>

          <tbody>
            <tr class="c-table__row">
              <?php foreach ($list as $item) { ?> 
              <td class="c-table__cell"><?php echo $item->id ?></td>
              <td class="c-table__cell"><?php echo $item->url ?></td>
            </tr>
             <?php } ?>
          </tbody>
        </table>
      </div>
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
