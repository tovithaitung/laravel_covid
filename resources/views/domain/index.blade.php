@extends('master')
@section('title')
    DashBoard ToviCorp
@endsection
@section('custom-css')

<link rel="stylesheet" href="{{asset('nouislider.min.css')}}">

@endsection
@section('content')
<?php
$url = '';
foreach(request()->all() as $key => $params){
  if($key != 'page' && $key != 'sort' && $key != 'type')
      if ($key != 'filter') {
          $url .= '&'.$key.'='.$params;
  } else {
          foreach ($params as $param) {
              $url .= '&'. $key . urlencode('[]'). '='. $param;
          }
      }
} 
?>

<div class="" id="app">
  <div class="row">
    <div class="col-12">
      <div class="row u-mb-medium">
        <div class="col-12">
          <div class="c-card">
              <form action="{{ route('home') }}">
                  <div class="row u-mb-medium">
                      <div class="col-lg-12 u-mb-xsmall">
                          <div class="c-field">
                              {{--<input type="hidden" name="page" value="">--}}
                              <label class="c-field__label" for="input1">Search</label>
                              <input class="c-input" name="search" value="{{ request()->search }}" type="text" id="" oninput="">
                              <button class="c-btn c-btn--info search" > <i class="fa fa-search"></i></button>
                          </div>
                      </div>
                  </div>
              </form>
            <form action="/" method="GET">
                <div class="row u-mb-medium">
                      <div class="col-lg-12 u-mb-xsmall">
                            <div class="c-field">
                                <input type="hidden" name="page" value="{{(request()->input('page')) ? request()->input('page') : 1}}">
                                <?php $filters = request()->input('filter');
                                        $url_filter = '';
                                        foreach ($filters as $key => $filter) {
                                            $url_filter = $filter;
                                        }
                                ?>
                                @if (request()->get('filter'))
                                    <input type="hidden" name="filter[]" value="{{(request()->input('filter')) ? $url_filter : 0}}">
                                @endif
                                <label class="c-field__label" for="input1">DR</label>
                                <input class="c-input" name="dr"  max="100" min="-1" value="{{request()->input('dr')}}" type="range" id="abcd_range" oninput="abcd.value=abcd_range.value"><output  id="abcd">{{request()->input('dr')}}</output>
                            </div>
                      </div>
                      <div class="col-lg-12 u-mb-xsmall">
                            <div class="c-field">
                                <label class="c-field__label" for="input1">Referring Domains</label>
                                <input class="c-input" name="RDomain"  max="{{$maxReferr}}" min="-1" type="range" id="rf_range" value="{{(request()->input('RDomain')) ? request()->input('RDomain') : -1}}" oninput="rf_domain.value = rf_range.value" ><output  id="rf_domain">{{request()->input('RDomain')}}</output>
                            </div>
                      </div>
                      <div class="col-lg-12 u-mb-xsmall">
                            <div class="c-field">
                                <label class="c-field__label" for="input1">Anchors</label>
                                <input class="c-input" name="TotalAnchor" type="range" value="{{(request()->input('TotalAnchor')) ? request()->input('TotalAnchor') : -1}}"  max="{{$maxAnchor}}" min="-1"  oninput="anchorss.value = anchor_range.value"  id="anchor_range"  ><output  id="anchorss">{{request()->input('TotalAnchor')}}</output>
                            </div>
                      </div>
                      <div class="col-lg-12 u-mb-xsmall">
                            <div class="c-field">
                                <label class="c-field__label" for="input1">Price</label>
                                <input class="c-input" name="price" type="range" value="{{(request()->input('price')) ? request()->input('price') : -1}}"  max="100" min="-1" oninput="abc.value = ffff.value" id="ffff" ><output  id="abc">{{request()->input('price')}}</output>
                            </div>
                      </div>
                      <div class="col-lg-2 u-mb-xsmall">
                            <label class="c-field__label" for="input1">Thao tác</label>
                            <button type="submit" class="c-btn c-btn--info">Tìm</button>
                      </div>
                      <div class="col-lg-2 u-mb-xsmall">
                            <label class="c-field__label" for="input1">Thao tác</label>
                            <a href="/@if(request()->input('page')){{'?page='.request()->input('page')}} @endif{{$url}}" class="c-btn c-btn--info">Bỏ lọc</a>
                      </div>
                </div>
            </form>

              {{--Filter--}}

            <form action=" {{ route('home') }}">
                <?php $filters = request()->input('filter');
                    $url_filter = '';
                    foreach ($filters as $key => $filter) {
                        $url_filter = $filter;
                    }
                ?>
                <div class="">
                    <label class="c-field__label" style="padding-right: 50px">Filter by:</label>
                    <div class="container" style="margin-left: 70px">
                        @if(request()->get('price'))
                            <input type="hidden" name="dr" value="{{(request()->input('dr')) ? request()->input('dr') : 0}}">
                            <input type="hidden" name="RDomain" value="{{(request()->input('RDomain')) ? request()->input('RDomain') : 0}}">
                            <input type="hidden" name="TotalAnchor" value="{{(request()->input('TotalAnchor')) ? request()->input('TotalAnchor') : -1}}">
                            <input type="hidden" name="price" value="{{(request()->input('price')) ? request()->input('price') : 0}}">
                        @endif
                        <label id="filter1" class="customcheck" style="display: none;" v-for="item in items">@{{ item.val }}
                            <input type="checkbox" name="filter[]" :value="item.id" :id="item.val" :checked=filter.includes(item.id) v-model="filter">
                            <span class="checkmark"></span>
                        </label>
                        <button class="c-btn c-btn--info" style="vertical-align: super">Filter</button>
                    </div>
                </div>
            </form>

              <div style="margin-top: 20px; margin: 4em auto">
                  <label class="c-field__label">Domain Request:</label>
                  <v-select :options="options" @input="filterDomain()" onchange='if(this.value != 0) { this.form.submit(); }' v-model="domainLink" ></v-select>
              </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row hide-default">
    <div class="col-12">
      <div class="container">
         <p style="font-size: 30px;" >Tổng số kết quả: <?php echo number_format($total) ?>.</p><br>
      <p class="currentpage">Page hiện tại: <?php echo $page ?></p>
      <a href="/{{'?page=1'}}{{$url}}" class="c-btn c-btn--info" style="margin-bottom: 20px; display: inline-block">Quay lại page 1</a>
      </div>
     
    </div>
  </div>

    {{--Domain - Link out --}}
    <div class="row" v-show="active">
        <div class="col-12">
            <div class="container">
                <p style="font-size: 30px;">Tổng số kết quả refer từ domain <strong>@{{ domainLink }}</strong> : @{{ elements.length }} </p><br>
                <div class="hide-default">
                    <p class="currentpage">Page hiện tại: <?php echo $page ?></p>
                    <a href="/{{'?page=1'}}{{$url}}" class="c-btn c-btn--info" style="margin-bottom: 20px; display: inline-block">Quay lại page 1</a>
                </div>
            </div>

        </div>
    </div>

    <div class="alert alert-success" id="success-alert" style="display: none">
        <button type="button" class="close" data-dismiss="alert">x</button>
        <strong>Success! </strong> Status have added to your wishlist.
    </div>

  <div class="row">
    <div class="col-12" id="app">
      <?php if(count($list) >0 ){ ?>
      <div class="c-table-responsive@wide">
        <table class="c-table hide-default" id="tableCore" cellpadding="0" cellspacing="0">
          <thead class="c-table__head">
            <tr class="c-table__row">
              <th class="c-table__cell c-table__cell--head">Domain Name</th>
              <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=total_index{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">Index</a></th>
              <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=dr{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">DR</a></th>

              <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=RDomain{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">Referring<br>domains</a></th>
              <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=backlinks{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">Backlinks</a></th>
              <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=TotalAnchor{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">Anchors</a></th>
              <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=url_rating{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">UR</a></th>
              <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=ahrefs_rank{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">Ahrefs Rank</a></th>
              <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=price{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">Price (Godaddy.com)</a></th>
              <th class="c-table__cell c-table__cell--head">WayBack</th>
              <td class="c-table__cell"><span style="vertical-align: text-bottom">Status</span></td>

            </tr>
          </thead>

          <tbody>
          <?php foreach ($list as $item) { ?>
            <tr class="c-table__row" id="row-{{ $item->domain_out_id}}">

              <td class="c-table__cell" >{{ $item->domain_name }}</td>
              <td class="c-table__cell" >{{ $item->total_index }}</td>
              <td class="c-table__cell"><?php echo number_format($item->dr) ?></td>
              <td class="c-table__cell popup" onmouseover="showDomain({{$item->domain_out_id}})" onmouseout="closeDomain({{$item->domain_out_id}})" >
                  <span style="display: block; margin-top: 15px;"><?php echo number_format($item->RDomain) ?></span>

                <span class="popuptext" id="domain-{{$item->domain_out_id}}">
                  <p style="color:white"><?php echo $item->domain_name ?> - Referrings Domain - Total: <?php echo $item->RDomain ?></p>
                  <table>
                    <thead>
                      <tr>
                        <th>STT</th>
                        <th>domain</th>
                        <th>DR</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $domains = json_decode($item->RDomain_detail,true);
                      foreach($domains as $key => $domain){
                        if($key < 20){
                      ?>
                      <tr>
                        <td>{{$key+1}}</td>
                        <td><span style="white-space: nowrap;overflow: hidden;display: inline-block;text-overflow: ellipsis;width: 200px"><?php echo $domain['refdomain'] ?></span></td>
                        <td><?php echo $domain['domain_rating'] ?></td>

                      </tr>
                      <?php } } ?>

                    </tbody>
                  </table>
                  <?php if(count($domains)>20){echo "<div align='left'>&nbsp;&nbsp;&nbsp;&nbsp;.</div>";
                echo "<div align='left'>&nbsp;&nbsp;&nbsp;&nbsp;.</div>";
                echo "<div align='left'>&nbsp;&nbsp;&nbsp;&nbsp;.</div>";}
                ?>
                  <div align='left'>&nbsp;&nbsp;&nbsp;<?php echo $item->RDomain ?> - Total Referrings Domain: <?php echo $item->RDomain ?></div>
                </span>
              </td>
              <td class="c-table__cell"><?php echo number_format($item->backlinks) ?></td>
              <td class="c-table__cell popup" onmouseover="showAnchors({{$item->domain_out_id}})" onmouseout="closeAnchors({{$item->domain_out_id}})"><?php echo number_format($item->TotalAnchor) ?>
                <span class="popuptext" id="myPopup-{{$item->domain_out_id}}">
                  <p style="color:white">Anchors: <?php echo $item->domain_name ?> - Total: <?php echo $item->TotalAnchor ?></p> 
                  <table>
                    <thead>
                      <tr>
                        <th>Anchor</th>
                        <th>PercentageRefDomains</th>
                        <th>Refdomains</th>
                      </tr>     
                    </thead>
                    <tbody>
                      <?php $anchors = json_decode($item->anchor,true); 
                      foreach($anchors as $key => $anchor){ 
                        if($key < 20){
                      ?>
                      <tr>
                        <td><span style="white-space: nowrap;overflow: hidden;display: inline-block;text-overflow: ellipsis;width: 200px"><?php if($anchor['anchor'] == ''){ echo 'no text';} else {echo $anchor['anchor'];} ?></span></td>
                        <td><?php echo $anchor['PercentageRefDomains'] ?></td>
                        <td><?php echo $anchor['refdomains'] ?></td>
                      </tr>
                      <?php } } ?>
                    </tbody>
                  </table>
                </span>
              </td>
              <td class="c-table__cell"><?php echo $item->url_rating ?></td>
              <td class="c-table__cell"><?php echo number_format($item->ahrefs_rank) ?></td>
              <td class="c-table__cell">$ <?php echo number_format($item->price) ?></td>
              <td class="c-table__cell"><a class="c-btn c-btn--info" target="_blank" href="https://web.archive.org/web/*/{{ $item->domain_name }}">Waybacklink</a></td>

                  {{--Select--}}
              <td class="c-table__cell">
                      <div class="form-group box">
                          <select class="form-control" id="select1-{{$item->domain_out_id}}" @change="changeStatus({{$item->domain_out_id}})">
                              <option value="{{ $item-> domain_out_id}}-0" <?php if ($item->check_status == '0') { echo 'selected'; } ?> >All</option>
                              <option value="{{ $item-> domain_out_id}}-1" <?php if ($item->check_status == '1') { echo 'selected'; } ?> >Seen</option>
                              @if(Auth::user()->email == 'admin@domain.com')
                              <option value="{{ $item-> domain_out_id}}-2" <?php if ($item->check_status == '2') { echo 'selected'; } ?> >Bought</option>
                              <option value="{{ $item-> domain_out_id}}-3" <?php if ($item->check_status == '3') { echo 'selected'; } ?> >Interested</option>
                              @endif
                              <option value="{{ $item-> domain_out_id}}-4" <?php if ($item->check_status == '4') { echo 'selected'; } ?> >Buy</option>
                              <option value="{{ $item-> domain_out_id}}-5" <?php if ($item->check_status == '5') { echo 'selected'; } ?> >SEO</option>
                              <option value="{{ $item-> domain_out_id}}-6" <?php if ($item->check_status == '6') { echo 'selected'; } ?> >PBN</option>
                              <option value="{{ $item-> domain_out_id}}-7" <?php if ($item->check_status == '7') { echo 'selected'; } ?> >Auction</option>

                          </select>
                      </div>
              </td>
            </tr>
             <?php } ?>
          </tbody>
        </table>

      </div>


      <table class="c-table" id="tableCustom" style="display: none" v-show="active">
          <thead class="c-table__head">
              <tr class="c-table__row">
                  <th class="c-table__cell c-table__cell--head">Domain Name</th>
                  <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=dr{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">DR</a></th>
                  <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=RDomain{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">Referring<br>domains</a></th>
                  <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=backlinks{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">Backlinks</a></th>
                  <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=TotalAnchor{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">Anchors</a></th>
                  <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=url_rating{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">UR</a></th>
                  <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=ahrefs_rank{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">Ahrefs Rank</a></th>
                  <th class="c-table__cell c-table__cell--head"><a href="/?page={{(request()->input('page')) ? request()->input('page') : 1}}{{$url}}&sort=price{{(request()->input('type') == 'desc' && request()->input('type')) ? '&type=asc' : '&type=desc'}}" title="">Price ( Godday.com)</a></th>
                  <th class="c-table__cell c-table__cell--head">WayBack</th>
                  <td class="c-table__cell"><span style="vertical-align: text-bottom">Status</span></td>
              </tr>
          </thead>

          <tbody>
          <tr class="c-table__row" :id="'row-' + element.domain_out_id" v-for="element in elements">
              <td class="c-table__cell" v-html="element.domain_name" ></td>
              <td class="c-table__cell" v-html="element.dr"></td>
              <td class="c-table__cell popup" @mouseover="showDomain(element.domain_out_id)" @onmouseout="closeDomain(element.domain_out_id)" >@{{ element.RDomain }}
                  <span class="popuptext" :id="'domain-' + element.domain_out_id">
                      <p style="color:white"></p>
                      <table>
                          <thead>
                              <tr>
                                  <th>STT</th>
                                  <th>domain</th>
                                  <th>DR</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr v-if="element.RDomain_detail == null">
                                  <td >1</td>
                                  <td><span style="white-space: nowrap;overflow: hidden;display: inline-block;text-overflow: ellipsis;width: 200px" >0</span></td>
                                  <td>0</td>
                              </tr>
                        </tbody>
                      </table>
                      <div align='left'>&nbsp;&nbsp; - Total Referrings Domain: </div>
                  </span>
              </td>
              <td class="c-table__cell" v-if="element.backlinks != null" v-html="element.backlinks"></td><td v-if="element.backlinks == null" class="c-table__cell" v-html="0"></td>
              <td class="c-table__cell popup" @mouseover="showAnchors(element.domain_out_id)" @mouseout="closeAnchors(element.domain_out_id)" >@{{ element.TotalAnchor }}
                  <span class="popuptext" :id="'myPopup-' + element.domain_out_id">
                      <p style="color:white">Anchors: </p>
                      <table>
                          <thead>
                              <tr>
                                  <th>Anchor</th>
                                  <th>PercentageRefDomains</th>
                                  <th>Refdomains</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td><span style="white-space: nowrap;overflow: hidden;display: inline-block;text-overflow: ellipsis;width: 200px">1</span></td>
                                  <td>2</td>
                                  <td>3</td>
                              </tr>
                          </tbody>
                      </table>
                   </span>
              </td>
              <td class="c-table__cell" v-if="element.url_rating == !null" v-html="element.url_rating"></td><td class="c-table__cell" v-if="element.url_rating == null" v-html="0"></td>
              <td class="c-table__cell" v-if="element.ahrefs_rank != null" v-html="element.ahrefs_rank"></td><td class="c-table__cell" v-if="element.ahrefs_rank == null" v-html="0"></td>
              <td class="c-table__cell" v-html="element.price"></td>
              <td class="c-table__cell"><a class="c-btn c-btn--info" target="_blank" href="https://web.archive.org/web/*/">Waybacklink</a></td>

              <td class="c-table__cell">
                  <div class="form-group box">
                      <select class="form-control" :id="'select1-' + element.domain_out_id" @change="changeStatus(element.domain_out_id)">
                          <option :value="element.domain_out_id + '-0'"  >All</option>
                          <option :value="element.domain_out_id + '-1'"  >Seen</option>
                          @if(Auth::user()->email == 'admin@domain.com')
                          <option :value="element.domain_out_id + '-2'"  >Bought</option>
                          <option :value="element.domain_out_id + '-3'"  >Interested</option>
                          @endif
                          <option :value="element.domain_out_id + '-4'"  >Buy</option>
                          <option :value="element.domain_out_id + '-5'"  >SEO</option>
                          <option :value="element.domain_out_id + '-6'"  >PBN</option>
                          <option :value="element.domain_out_id + '-7'"  >PBN</option>
                      </select>
                  </div>
              </td>
          </tr>
          </tbody>
      </table>
          {{--End table domain - link out --}}

      </div>
      <?php } else { echo '<p>Không có kết quả phù hợp</p>';

       } ?>
    </div>

  </div>
  <div class="row u-mt-xlarge hide-default">
    <div class="col-md-4"></div>
    <div class="col-md-4">
      <?php $page_total = $totalPage; if(isset($_GET['page'])){ $page = $_GET['page'];
            } else {$page = 1;}
            $char = '?';
            $params = request()->all();
            $url = '';
            foreach($params as $key => $param){

              if($key != 'page'){
                //$char = '&';
                  if ($key == 'filter') {
                      $fil = request()->get('filter');
                      foreach ($fil as $k => $value) {
                          $url .= '&'.'filter'.urlencode('[]'). '='.$value;
                      }

                  } else {
                      $url .= '&'.$key.'='.$param;
                  }
              }
            }
            ?>
            <?php if(count($list) == 20){ ?>
              <ul class="c-pagination u-mb-medium">
                  <?php if($page != 1){ ?>
                  <li><a class="c-pagination__link" href="{{$char}}page=1{{$url}}" ><<</a></li>
                  <li><a class="c-pagination__link" href="{{$char}}page=<?php echo ($page-1); ?>{{$url}}" ><</a></li>
                  <?php } ?>
                  <?php if($page - 1 == 0){$min = 1;} else {$min = $page-1;} if($page +2 < $page_total){$max = $page + 2;} else {$max = $page_total;}  for ($i=$min ; $i <= $max; $i++) {  ?>
                    <li><a class="c-pagination__link <?php if($page == $i) { ?> is-active <?php } ?>"  href="{{$char}}page=<?php echo $i; ?>{{$url}}"><?php echo $i; ?></a></li>
                  <?php } ?>
                  <?php if($page != $page_total){ ?>
                  <li title="Next page"><a class="c-pagination__link" href="{{$char}}page=<?php echo $page+1; ?>{{$url}}"><i class="feather icon-chevron-right"></i></a></li>
                  <li><a class="c-pagination__link" href="{{$char}}page={{$page_total}}{{$url}}" >>></a></li>
                  <?php } ?>
              </ul>
            <?php } ?>
    </div>
    <div class="col-md-4"></div>
  </div>
  <div class="row">
      <div class="col-12">
        <footer class="c-footer">
          <p>© 2019 tovicorp.com</p>
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
<!-- Modal -->
<div class="c-modal modal fade" id="modalWayBack" tabindex="-1" role="dialog" aria-labelledby="modalWayBack">
    <div class="c-modal__dialog modal-dialog" role="document">
        <div class="modal-content">
            <div class="c-card u-p-medium u-mh-auto" style="max-width:500px;">
                <h3>WayBack History</h3>
                <p class="u-text-mute u-mb-small" id="content"></p>
                <p class="u-text-mute u-mb-small" id="urlContent"></p>
                <button class="c-btn c-btn--info" data-dismiss="modal">
                    Ok, just close this modal
                </button>
            </div>
        </div>
    </div>
</div>


@endsection
@section('script-custom')
<script src="{{asset('nouislider.min.js')}}"></script>
<script src='https://cdn.jsdelivr.net/npm/vue'></script>
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>--}}
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script>
  function getMessage(domain) {
      $.ajax({
         type:'GET',
         url:'/getDomain',
         data:'_token = <?php echo csrf_token() ?>&domain='+domain,
         success:function(data) {
            data = JSON.parse(data);
            if(data.status == 1 || data.content == ''){
              $('#modalWayBack #content').html('<iframe width="100%" src="'+data.content+'"></iframe>');
              $('#modalWayBack #urlContent').html('<a href="'+data.content+'" target="_blank">Link</a>');
            } else {
              $('#modalWayBack #content').html('No Wayback history');
            }
            $('#modalWayBack').modal('show');
         }
      });
   }
  function showAnchors(id){
    var popup = document.getElementById("myPopup-"+id);
    popup.classList.toggle("show");
  }
  function closeAnchors(id){
    var popup = document.getElementById("myPopup-"+id);
    popup.classList.remove("show");
  }
  function showDomain(id){
    var popup = document.getElementById("domain-"+id);
    popup.classList.toggle("show");
  }
  function closeDomain(id){
    var popup = document.getElementById("domain-"+id);
    popup.classList.remove("show");
  }
</script>
{{-- Custom Css --}}
<script type="text/javascript">
   var options = <?php echo json_encode($domainLinks) ?>;
    var filter = <?php echo  ($filters = request()->filter) ? json_encode($filters = request()->filter) : '[]' ?>;
   /*  Vue.component('v-select', VueSelect.VueSelect);
    var app = new Vue({
        el: '#app',
        data: {
            filter: filter,
            input: '',
            items: [
                {id: 0, val: 'All'},
                {id: 1, val: 'Seen'},
                {id: 2, val: 'Bought'},
                {id: 3, val: 'Interested'},
                {id: 4, val: 'Buy'},
                {id: 5, val: 'SEO'},
            ],
            lists: [],
            options: ['developer.android.com'],
            domainLink: '',
            elements: [],
        },
        methods: {
            changeStatus: function () {
                var value = $('#select1').val();
                var self = this;
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/selected",
                    type: 'GET',
                    data: {
                        selected : value
                    },
                    complete : function() {

                    },
                    success: function (result) {
                        if (result) {
                            var tmp = value.split('-');
                            $('#row-'+tmp[0]).hide('slow');
                            $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                                $("#success-alert").slideUp(400);
                            });
                        }
                    }
                });
            },
            filterDomain: function () {
                var self = this;
                console.log(self.domainLink);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/filter",
                    type: 'GET',
                    data: {
                        domainLink : self.domainLink,
                    },
                    success: function (result) {
                        if (result) {
                            self.elements = JSON.parse(result);
                            $('#myModal').modal('show');
                        }
                    }
                });
            }
        }
    });*/
    $(document).ready(function () {
    $('.customcheck').show();
});


Vue.component('v-select', VueSelect.VueSelect);
var app = new Vue({
    el: '#app',
    data: {
        filter: filter,
        content: '<input type="checkbox" name="filter[]" :value="item.id" :id="item.val" :checked=filter.includes(item.id) v-model="filter">\n' +
                '<span class="checkmark"></span>',
        input: '',
        items: [
            {id: 0, val: 'All'},
            {id: 1, val: 'Seen'},
            @if(Auth::user()->email == 'admin@domain.com')
            {id: 2, val: 'Bought'},
            {id: 3, val: 'Interested'},
            @endif
            {id: 4, val: 'Buy'},
            {id: 5, val: 'SEO'},
            {id: 6, val: 'PPN'},
            {id: 7, val: 'Auction'},
            {id: 8, val: 'News'},
            {id: 9, val: 'Domcop'}

        ],
        lists: [],
        options: ['developer.android.com'],
        domainLink: '',
        elements: [],
        active: false
    },
    methods: {
        changeStatus: function (id) {
            var value = $('#select1-' + id).val();
            var self = this;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/selected",
                type: 'GET',
                data: {
                    selected : value
                },
                complete : function() {

                },
                success: function (result) {
                    if (result) {
                        var tmp = value.split('-');
                        console.log(tmp[0]);
                        $('#row-'+tmp[0]).hide('slow');
                        $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
                            $("#success-alert").slideUp(400);
                        });
                    }
                }
            });
        },
        filterDomain: function () {
            var self = this;
            self.active = !self.active;
            console.log(self.domainLink);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/filter",
                type: 'GET',
                data: {
                    domainLink : self.domainLink
                },
                success: function (result) {
                    if (result) {
                        self.elements = JSON.parse(result);
                        $('.hide-default').remove();
                        // $('#tableCore tbody').html(html);
                        // $('#tableCore tbody').append(html);

                    }
                }
            });
        },
        showDomain : function (id) {
            var popup = document.getElementById("domain-"+id);
            popup.classList.toggle("show");
        },
        closeDomain : function (id) {
            var popup = document.getElementById("domain-"+id);
            popup.classList.remove("show");
        },
        showAnchors : function (id) {
            var popup = document.getElementById("myPopup-"+id);
            popup.classList.toggle("show");
        },
        closeAnchors : function (id) {
            var popup = document.getElementById("myPopup-"+id);
            popup.classList.remove("show");
        }
    }
});
</script>


@endsection
