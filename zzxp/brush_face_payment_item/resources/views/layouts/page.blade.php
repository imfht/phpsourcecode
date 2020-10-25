@if(isset($total))
<?php 
    $size = isset($size) ? $size : 20;
    // $page = \Input::get('page',0);
    $page = isset($search['page']) ? $search['page'] : 1;
    $page < 1 && $page = 1; 
    $total_page = ceil($total / $size);
?>
@if($total_page > 1)
<?php 
    if(isset($search['page'])) unset($search['page']);

    if(empty($search)){
        $queryStr = '';
    }else{
        $query = array();
        foreach($search as $key => $val){
			if(is_string($val)){
				$query[] = $key . '=' . $val;
			}
        }
        $queryStr = implode('&',$query);    
    }
    !empty($queryStr) && $queryStr = '&' . $queryStr;
    $page_lun = 5;  
    $page_start = $page - $page_lun;
    $page_end   = $page + $page_lun;
    $page_end   > $total_page && $page_end = $total_page;
    if($page_end - $page < $page_lun){
        $page_start = $page_end - ($page_lun * 2);
    }
    $page_start < 1 && $page_start = 1;
    $pre_page = $page - 1;
    $next_page = $page + 1;
    $pre_page <= 0 && $pre_page = 1;
    $next_page > $total_page && $next_page = $total_page;

    $page_end = $page_start + ($page_lun * 2);
    $page_end   > $total_page && $page_end = $total_page;

?>
<ul class="page">
    @if($page > $page_lun && $total_page > $page_lun * 2)
    <li><a href="?page=1{{ $queryStr }}">首页</a></li>
    @endif

    <li><a href="?page={{ $pre_page.$queryStr }}">上一页</a></li>
    @for($p = $page_start; $p <= $page_end; $p++)
        {{--将'class="selected"'改为：'class=selected'--}}
    <li><a  {{ $p == $page ? 'class=selected' : 'href=?page=' . $p . $queryStr .''}}>{{$p}}</a></li>
    @endfor
    <li><a href="?page={{ $next_page.$queryStr }}">下一页</a></li>

    @if($page < $total_page - $page_lun && $total_page > $page_lun * 2)
    <li><a href="?page={{ $total_page.$queryStr }}">尾页</a></li>
    @endif
    <li><a>总计：{{$total}}</a></li>
    @if(isset($total_price))
    <li><a>总金额：{{$total_price}}</a></li>
    @endif
    @if(isset($price))
    <li><a>折后金额：{{$price}}</a></li>
    @endif
    <div style="clear:both;"></div>
</ul>
@elseif(isset($total_price))
<ul class="page">
    @if(isset($total_price))
    <li><a>总金额：{{$total_price}}</a></li>
    @endif
    @if(isset($price))
    <li><a>折后金额：{{$price}}</a></li>
    @endif
    <div style="clear:both;"></div>
</ul>
@endif
@elseif(isset($total_price))
<ul class="page">
    @if(isset($total_price))
    <li><a>总金额：{{$total_price}}</a></li>
    @endif
    @if(isset($price))
    <li><a>折后金额：{{$price}}</a></li>
    @endif
    <div style="clear:both;"></div>
</ul>
@endif