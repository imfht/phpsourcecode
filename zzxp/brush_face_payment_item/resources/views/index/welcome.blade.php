<!DOCTYPE html>
<!-- saved from url=(0060)http://pay.hlwguanggao.com/web/agent/index.html#/index/index -->
<html class=" ">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>管理后台</title>
    <link href="{{asset('css/app_new.css')}}" rel="stylesheet">
    <link href="http://www.jq22.com/demo/datepicker201809181248/css/datepicker.css" rel="stylesheet">
</head>
<body>
    <div class="main-container">
        <div data-v-21e342d2="" class="tags-view-container"></div>
        <section class="app-main"><div data-v-57594b62="" class="app-container index-page">
            <div data-v-57594b62="" class="header_bar mb20">
                <div data-v-57594b62="" class="box-card" style="width: 55%;">
                    <div data-v-57594b62="" class="public_news">
                        <div data-v-57594b62="" class="title_bar">
                            <span data-v-57594b62="" class="title_bar_text">数据概览</span>
                        </div> 
                        <div data-v-57594b62="" class="box-content">
                            <div data-v-57594b62="" class="flex-center" style="padding-top: 16px;">
                                <!----> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">总交易额</div> 
                                    <div data-v-57594b62="" class="total_num">￥<span id="total_sale_money">0</span></div>
                                </div> 
                                <!----> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">佣金总额</div> 
                                    <div data-v-57594b62="" class="total_num">￥<span id="total_introduce_money">0</span></div>
                                </div> 
                                <!----> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">普通合伙人</div> 
                                    <div data-v-57594b62="" class="total_num"><span id="agent_normal">0</span></div>
                                </div> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">高级合伙人</div> 
                                    <div data-v-57594b62="" class="total_num"><span id="agent_gold">0</span></div>
                                </div> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">总商户数</div> 
                                    <div data-v-57594b62="" class="total_num"><span id="business">0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                <!----> 
                <div data-v-57594b62="" class="box-card ml20" style="width: 43.8%;">
                    <div data-v-57594b62="" class="public_news">
                        <div data-v-57594b62="" class="title_bar">
                            <span data-v-57594b62="" class="title_bar_text">今日数据</span>
                        </div> 
                        <div data-v-57594b62="" class="box-content">
                            <div data-v-57594b62="" class="flex-center" style="padding-top: 16px;">
                                <!----> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">商户数</div> 
                                    <div data-v-57594b62="" class="total_num"><span id="today_business">0</span></div>
                                </div> 
                                <!----> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">当日分润</div> 
                                    <div data-v-57594b62="" class="total_num">￥<span id="today_sale_money">0</span></div>
                                </div> 
                                <!----> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">直推奖励数</div> 
                                    <div data-v-57594b62="" class="total_num">￥<span id="today_introduce_money">0</span></div>
                                </div> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">新普合伙人数</div> 
                                    <div data-v-57594b62="" class="total_num"><span id="today_agent">0</span></div>
                                </div> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">当日交易笔数/总额</div> 
                                    <div data-v-57594b62="" class="total_num"><span id="today_money">0笔/0元</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div data-v-57594b62="" class="header_bar mb20">
                <div data-v-57594b62="" class="box-card flex-1">
                    <div data-v-57594b62="" class="public_news">
                        <div data-v-57594b62="" class="title_bar">
                            <span data-v-57594b62="" class="title_bar_text">交易数据</span> 
                            <div data-v-57594b62="" class="flex">
                                <div data-v-57594b62="" class="mr20">
                                    <div class="c-datepicker-date-editor  J-datepicker-range-day">
                                        <i class="c-datepicker-range__icon kxiconfont icon-clock"></i>
                                        <input placeholder="开始日期" onchange="getPay()" name="" class="c-datepicker-data-input only-date" id="start_date" value="">
                                        <span class="c-datepicker-range-separator">-</span>
                                        <input placeholder="结束日期"  onchange="getPay()" name="" class="c-datepicker-data-input only-date" id="end_date" value="">
                                    </div>
                                </div> 
                                <div data-v-57594b62="" class="block">
                                    <div data-v-57594b62="" role="radiogroup" class="el-radio-group">
                                        <label data-v-57594b62="" role="radio" tabindex="-1" class="el-radio-button el-radio-button--small">
                                            <input type="radio" onclick="checkDate(-1)" name="sale_date" tabindex="-1" class="el-radio-button__orig-radio" value="-1">
                                            <span class="el-radio-button__inner">昨天<!----></span>
                                        </label> 
                                        <label data-v-57594b62="" role="radio" tabindex="-1" class="el-radio-button el-radio-button--small">
                                            <input type="radio" onclick="checkDate(-3)" name="sale_date" tabindex="-1" class="el-radio-button__orig-radio" value="-3" checked="checked">
                                            <span class="el-radio-button__inner">3天<!----></span>
                                        </label> 
                                        <label data-v-57594b62="" role="radio" tabindex="-1" class="el-radio-button el-radio-button--small">
                                            <input type="radio" onclick="checkDate(-7)" name="sale_date" tabindex="-1" class="el-radio-button__orig-radio" value="-7">
                                            <span class="el-radio-button__inner">7天<!----></span>
                                        </label> 
                                        <label data-v-57594b62="" role="radio" tabindex="-1" class="el-radio-button el-radio-button--small">
                                            <input type="radio" onclick="checkDate(-15)" name="sale_date" tabindex="-1" class="el-radio-button__orig-radio" value="-15">
                                            <span class="el-radio-button__inner">15天<!----></span>
                                        </label> 
                                        <label data-v-57594b62="" role="radio" tabindex="-1" class="el-radio-button el-radio-button--small">
                                            <input type="radio" onclick="checkDate(-30)" name="sale_date" tabindex="-1" class="el-radio-button__orig-radio" value="-30">
                                            <span class="el-radio-button__inner">30天<!----></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div data-v-57594b62="" class="box-content">
                            <div data-v-57594b62="" class="flex-center" style="padding-top: 16px;">
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">交易金额</div> 
                                    <div data-v-57594b62="" class="total_num">￥<span id="sale_money">1280.5</span></div>
                                </div> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">交易笔数</div> 
                                    <div data-v-57594b62="" class="total_num"><span id="sale_count">230</span></div>
                                </div> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">退款金额</div> 
                                    <div data-v-57594b62="" class="total_num">￥<span id="return_money">0</span></div>
                                </div> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">退款笔数</div> 
                                    <div data-v-57594b62="" class="total_num"><span id="return_count">0</span></div>
                                </div> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">实际营收</div> 
                                    <div data-v-57594b62="" class="total_num">￥<span id="real_money">1280.5</span></div>
                                </div> 
                                <div data-v-57594b62="" class="border-right">
                                    <div data-v-57594b62="" class="total_title">佣金总额</div> 
                                    <div data-v-57594b62="" class="total_num">￥<span id="introduce_money">358.68</span></div>
                                </div> 
                                <!----> 
                                <!----> 
                                <!---->
                            </div>
                        </div>
                    </div>
                </div> 
            </div> 
            <div data-v-57594b62="" class="middle_chart"><div data-v-57594b62="" class="box-card chart_card mr20" style="width: 55%;">
                <div data-v-57594b62="" class="title_bar">
                    <span data-v-57594b62="" class="title_bar_text">数据统计</span> 
                    <div class="c-datepicker-date-editor  J-datepicker-range-day">
                        <i class="c-datepicker-range__icon kxiconfont icon-clock"></i>
                        <input placeholder="开始日期" id="line_start_date" name="" class="c-datepicker-data-input only-date" value="">
                        <span class="c-datepicker-range-separator">-</span>
                        <input placeholder="结束日期" id="line_end_date" name="" class="c-datepicker-data-input only-date" value="">
                    </div>
                </div> 
                <div data-v-57594b62="" class="box-content">
                    <div data-v-57594b62="" role="radiogroup" class="el-radio-group list-absolute">
                       
                        <label data-v-57594b62="" role="radio" tabindex="-1" class="el-radio-button el-radio-button--small">
                            <input type="radio" name="sale_type" tabindex="-1" class="el-radio-button__orig-radio" value="1"  onclick="showData(2)">
                            <span class="el-radio-button__inner">交易金额<!----></span>
                        </label> 
                        <label data-v-57594b62="" role="radio" tabindex="-1" class="el-radio-button el-radio-button--small">
                            <input type="radio" name="sale_type" tabindex="-1" class="el-radio-button__orig-radio" value="2"  onclick="showData(3)">
                            <span class="el-radio-button__inner">交易笔数<!----></span>
                        </label>                     </div> 
                    <div id="table" style="height:340px;">
                        
                    </div>
                </div>
            </div> 
            <div data-v-57594b62="" class="box-card chart_card" style="width: 43.8%;">
                <div data-v-57594b62="" class="title_bar">
                    <span data-v-57594b62="" class="title_bar_text">业务员发展商户占比</span> <!---->
                </div> 
                <div id="circle" style="height:340px;">
                </div>
            </div>
        </div>
            
        </div>
        </section>
    </div>

    <div class="c-datepicker-picker c-datepicker-date-range-picker c-datepicker-popper  has-sidebar is-zh-CN" x-placement="top-start" style="display: none;">
        <div class="c-datepicker-picker__body-wrapper">
            <div class="c-datepicker-picker__sidebar"><button type="button" class="c-datepicker-picker__shortcut" data-value="-7,0" data-time="">最近一周</button><button type="button" class="c-datepicker-picker__shortcut" data-value="-30,0" data-time="">最近一个月</button><button type="button" class="c-datepicker-picker__shortcut" data-value="-90, 0" data-time="">最近三个月</button></div><div class="c-datepicker-picker__body"><div class="c-datepicker-date-range-picker__time-header" style="display: none;"><div class="c-datepicker-date-range-picker__time-content"><span class="c-datepicker-date-range-picker__editor-wrap"><div class="c-datepicker-input c-datepicker-input--small"><input type="text" autocomplete="off" placeholder="选择日期" class="c-datepicker-input__inner c-datePicker__input-day"></div></span><span class="c-datepicker-date-range-picker__editor-wrap"><div class="c-datepicker-input c-datepicker-input--small"><input type="text" autocomplete="off" placeholder="选择时间" class="c-datepicker-input__inner c-datePicker__input-time"></div></span></div><span class="kxiconfont icon-right"></span><div class="c-datepicker-date-range-picker__time-content"><span class="c-datepicker-date-range-picker__editor-wrap"><div class="c-datepicker-input c-datepicker-input--small"><input type="text" autocomplete="off" placeholder="选择日期" class="c-datepicker-input__inner c-datePicker__input-day"></div></span><span class="c-datepicker-date-range-picker__editor-wrap"><div class="c-datepicker-input c-datepicker-input--small"><input type="text" autocomplete="off" placeholder="选择时间" class="c-datepicker-input__inner c-datePicker__input-time"></div></span></div></div><div class="c-datepicker-picker__body-content"><div class="c-datepicker-date-range-picker-panel__wrap is-left"><div class="c-datepicker-date-range-picker__header"><i class="kxiconfont icon-first c-datepicker-picker__icon-btn c-datepicker-date-range-picker__prev-btn year" aria-label="前一年"></i><i class="kxiconfont icon-left c-datepicker-picker__icon-btn c-datepicker-date-range-picker__prev-btn month" aria-label="下个月"></i><span role="button" class="c-datepicker-date-range-picker__header-label c-datepicker-date-range-picker__header-year"><span>2019</span> 年</span><span role="button" class="c-datepicker-date-range-picker__header-label c-datepicker-date-range-picker__header-month"><span>11</span> 月</span></div><div class="c-datepicker-picker__content"><table class="c-datepicker-date-table" style=""><tbody><tr><th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th></tr><tr><td class="prev-month"><div><a class="cell">27</a></div></td><td class="prev-month"><div><a class="cell">28</a></div></td><td class="prev-month"><div><a class="cell">29</a></div></td><td class="prev-month"><div><a class="cell">30</a></div></td><td class="prev-month"><div><a class="cell">31</a></div></td><td class="available today"><div><a class="cell">1</a></div></td><td class="available"><div><a class="cell">2</a></div></td></tr><tr><td class="available"><div><a class="cell">3</a></div></td><td class="available"><div><a class="cell">4</a></div></td><td class="available"><div><a class="cell">5</a></div></td><td class="available"><div><a class="cell">6</a></div></td><td class="available"><div><a class="cell">7</a></div></td><td class="available"><div><a class="cell">8</a></div></td><td class="available"><div><a class="cell">9</a></div></td></tr><tr><td class="available"><div><a class="cell">10</a></div></td><td class="available"><div><a class="cell">11</a></div></td><td class="available"><div><a class="cell">12</a></div></td><td class="available"><div><a class="cell">13</a></div></td><td class="available"><div><a class="cell">14</a></div></td><td class="available"><div><a class="cell">15</a></div></td><td class="available"><div><a class="cell">16</a></div></td></tr><tr><td class="available"><div><a class="cell">17</a></div></td><td class="available"><div><a class="cell">18</a></div></td><td class="available"><div><a class="cell">19</a></div></td><td class="available"><div><a class="cell">20</a></div></td><td class="available"><div><a class="cell">21</a></div></td><td class="available"><div><a class="cell">22</a></div></td><td class="available"><div><a class="cell">23</a></div></td></tr><tr><td class="available"><div><a class="cell">24</a></div></td><td class="available"><div><a class="cell">25</a></div></td><td class="available"><div><a class="cell">26</a></div></td><td class="available"><div><a class="cell">27</a></div></td><td class="available"><div><a class="cell">28</a></div></td><td class="available"><div><a class="cell">29</a></div></td><td class="available"><div><a class="cell">30</a></div></td></tr><tr><td class="next-month"><div><a class="cell">1</a></div></td><td class="next-month"><div><a class="cell">2</a></div></td><td class="next-month"><div><a class="cell">3</a></div></td><td class="next-month"><div><a class="cell">4</a></div></td><td class="next-month"><div><a class="cell">5</a></div></td><td class="next-month"><div><a class="cell">6</a></div></td><td class="next-month"><div><a class="cell">7</a></div></td></tr></tbody></table></div></div><div class="c-datepicker-date-range-picker-panel__wrap is-right"><div class="c-datepicker-date-range-picker__header"><span role="button" class="c-datepicker-date-range-picker__header-label c-datepicker-date-range-picker__header-year"><span>2019</span> 年</span><span role="button" class="c-datepicker-date-range-picker__header-label c-datepicker-date-range-picker__header-month"><span>12</span> 月</span><i class="kxiconfont icon-right c-datepicker-picker__icon-btn c-datepicker-date-range-picker__next-btn month" aria-label="下个月"></i><i class="kxiconfont icon-last c-datepicker-picker__icon-btn c-datepicker-date-range-picker__next-btn year" aria-label="后一年"></i></div><div class="c-datepicker-picker__content"><table class="c-datepicker-date-table" style=""><tbody><tr><th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th></tr><tr><td class="available"><div><a class="cell">1</a></div></td><td class="available"><div><a class="cell">2</a></div></td><td class="available"><div><a class="cell">3</a></div></td><td class="available"><div><a class="cell">4</a></div></td><td class="available"><div><a class="cell">5</a></div></td><td class="available"><div><a class="cell">6</a></div></td><td class="available"><div><a class="cell">7</a></div></td></tr><tr><td class="available"><div><a class="cell">8</a></div></td><td class="available"><div><a class="cell">9</a></div></td><td class="available"><div><a class="cell">10</a></div></td><td class="available"><div><a class="cell">11</a></div></td><td class="available"><div><a class="cell">12</a></div></td><td class="available"><div><a class="cell">13</a></div></td><td class="available"><div><a class="cell">14</a></div></td></tr><tr><td class="available"><div><a class="cell">15</a></div></td><td class="available"><div><a class="cell">16</a></div></td><td class="available"><div><a class="cell">17</a></div></td><td class="available"><div><a class="cell">18</a></div></td><td class="available"><div><a class="cell">19</a></div></td><td class="available"><div><a class="cell">20</a></div></td><td class="available"><div><a class="cell">21</a></div></td></tr><tr><td class="available"><div><a class="cell">22</a></div></td><td class="available"><div><a class="cell">23</a></div></td><td class="available"><div><a class="cell">24</a></div></td><td class="available"><div><a class="cell">25</a></div></td><td class="available"><div><a class="cell">26</a></div></td><td class="available"><div><a class="cell">27</a></div></td><td class="available"><div><a class="cell">28</a></div></td></tr><tr><td class="available"><div><a class="cell">29</a></div></td><td class="available"><div><a class="cell">30</a></div></td><td class="available"><div><a class="cell">31</a></div></td><td class="next-month"><div><a class="cell">1</a></div></td><td class="next-month"><div><a class="cell">2</a></div></td><td class="next-month"><div><a class="cell">3</a></div></td><td class="next-month"><div><a class="cell">4</a></div></td></tr><tr><td class="next-month"><div><a class="cell">5</a></div></td><td class="next-month"><div><a class="cell">6</a></div></td><td class="next-month"><div><a class="cell">7</a></div></td><td class="next-month"><div><a class="cell">8</a></div></td><td class="next-month"><div><a class="cell">9</a></div></td><td class="next-month"><div><a class="cell">10</a></div></td><td class="next-month"><div><a class="cell">11</a></div></td></tr></tbody></table></div></div></div></div></div><div class="c-datepicker-picker__footer" style=""><button type="button" class="c-datepicker-button c-datepicker-picker__link-btn c-datepicker-button--text c-datepicker-button--mini c-datepicker-picker__btn-clear"><span>清空</span></button><button type="button" class="c-datepicker-button c-datepicker-picker__link-btn confirm c-datepicker-button--default c-datepicker-button--mini is-plain"><span>确定</span></button></div><div x-arrow="" class="popper__arrow" style="left: 35px;">
            
        </div>
    </div>

    <script src="https://www.jq22.com/jquery/jquery-1.10.2.js"></script>
    <script src="https://libs.baidu.com/jquery/1.10.2/jquery.min.js"></script>
    <script src="http://www.jq22.com/demo/datepicker201809181248/js/plugins/moment.min.js"></script>
    <script src="http://www.jq22.com/demo/datepicker201809181248/js/datepicker.all.min.js"></script>
    <script src="http://www.jq22.com/demo/datepicker201809181248/js/datepicker.en.js"></script>
    <script type="text/javascript">
        $(function(){
            //年月日范围
            $('.J-datepicker-range-day').datePicker({
                hasShortcut: false,
                format: 'YYYY-MM-DD',
                isRange: true,
                // shortcutOptions: [{
                //     name: '最近一周',
                //     day: '-7,0'
                // }, {
                //     name: '最近一个月',
                //     day: '-30,0'
                // }, {
                //     name: '最近三个月',
                //     day: '-90, 0'
                // }]
            });
        })
    </script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
   <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts-gl/dist/echarts-gl.min.js"></script>
   <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts-stat/dist/ecStat.min.js"></script>
   <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/extension/dataTool.min.js"></script>
   <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/map/js/china.js"></script>
   <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/map/js/world.js"></script>
    
    <script type="text/javascript">
        var introduce_money = {};
        var wx_order_money = {};
        var ali_order_money = {};
        var wx_back_money = {};
        var ali_back_money = {};
        function checkDate(days){
            var date = new Date();
            var end_at = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + (date.getDate());
            date.setTime(date.getTime() + 3600000*24 * days);
            var start_at = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + (date.getDate());
            $('#start_date').val(start_at);
            $('#end_date').val(end_at);
            getPay();

        }
        function memberDate(days){
            var date = new Date();
            var end_at = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + (date.getDate());
            date.setTime(date.getTime() + 3600000*24 * days);
            var start_at = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + (date.getDate());
            $('#line_start_date').val(start_at);
            $('#line_end_date').val(end_at);
            getMember();

        }
        function getMember(){
            $.ajax({
                type:'GET',
                url:'/member',
                data:{start_time:$('#line_start_date').val(),end_time:$('#line_end_date').val()},
                success:function(res){
                    // console.log(res);
                    for(var i in res.wx_total_order_money){
                        var row = res.wx_total_order_money[i];
                        // console.log(row);
                        wx_order_money[row.add_date] = {count:row.totals,money:row.money};
                    }
                    for(var i in res.ali_total_order_money){
                        var row = res.ali_total_order_money[i];
                        // console.log(row);
                        ali_order_money[row.add_date] = {count:row.totals,money:row.money};
                    }
                    for(var i in res.wx_total_back_money){
                        var row = res.wx_total_back_money[i];
                        wx_back_money[row.add_date] = {count:row.totals,money:row.money};
                    
                    }

                    for(var i in res.ali_total_back_money){
                        var row = res.ali_total_back_money[i];
                        // console.log(row);
                        ali_back_money[row.add_date] = {count:row.totals,money:row.money};
                    }

                    for(var i in res.total_introduce_money){
                        var row = res.total_introduce_money[i];
                        // console.log(row);
                        introduce_money[row.add_date] = {money:row.money};
                    }
                    // console.log(introduce_money);
                    showData(2);
                }
            });

        }
        function getDayStatistics(){
            var start_at = "{{date('Y-m-d 00:00:00')}}";
            var end_at = "{{date('Y-m-d 23:59:59')}}";
            $.ajax({
                type:'GET',
                url:'/statistics',
                data:{start_time:start_at,end_time:end_at},
                success:function(res){
                    console.log(res);
                    $('#today_sale_money').html(parseFloat(res.total_sale_money[0].total_money));
                    $('#today_introduce_money').html(parseFloat(res.total_introduce_money[0].total_money));
                    $('#today_agent').html(res.agent_normal);
                    $('#today_gold').html(res.agent_gold);
                    $('#today_business').html(res.business);

                }
            })

        }

        function getDayPay(){
            var start_at = "{{date('Y-m-d 00:00:00')}}";
            var end_at = "{{date('Y-m-d 23:59:59')}}";
            
            $.ajax({
                type:'GET',
                url:'/pay',
                data:{sale_type:2,start_time:start_at,end_time:end_at},
                success:function(res){
                    // console.log(res);
                    $('#today_money').html(res.count_order_money+'笔/'+res.total_order_money[0].total_money+'元');


                }
            })

        }
        function getStatistics(){
            $.ajax({
                type:'GET',
                url:'/statistics',
                data:{},
                success:function(res){
                    // console.log(res);
                    $('#total_sale_money').html(res.total_sale_money[0].total_money);
                    $('#total_introduce_money').html(res.total_introduce_money[0].total_money);
                    $('#agent_normal').html(res.agent_normal);
                    $('#agent_gold').html(res.agent_gold);
                    $('#business').html(res.business);

                    circle(res);
                }
            })

        }
        function getPay(){
            $.ajax({
                type:'GET',
                url:'/pay',
                data:{sale_type:2,start_time:$('#start_date').val(),end_time:$('#end_date').val()},
                success:function(res){
                    console.log(res);
                    $('#sale_money').html(res.total_order_money[0].total_money);
                    $('#sale_count').html(res.count_order_money);

                    $('#return_money').html(res.total_back_money[0].total_money);
                    $('#return_count').html(res.count_back_money);


                    $('#real_money').html(res.introduce_money[0].real_money);
                    $('#introduce_money').html(res.introduce_money[0].money);

                }
            })

        }



        var colors = ['#5793f3', '#d14a61', '#675bba'];


       
        function showData(type){

            var date = new Date();
            var end_time = date.getTime();

            var start_at = $('#line_start_date').val().split('-');
            date.setFullYear(start_at[0]);
            date.setMonth(parseInt(start_at[1]) - 1);
            date.setDate(start_at[2]);
            date.setHours(0);
            date.setMinutes(0);
            date.setSeconds(0);
            var date_list = [];
            for(var start = date.getTime(); start < end_time; start += 24*3600000){
                date_list.push(date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + (date.getDate()));

                // console.log(start);
                date.setTime(start);
                // console.log(start);
                // console.log(date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + (date.getDate()));
            }
            if(type == 1){
                var series = [];
                var money_list = [];
                for(var i in date_list){
                    if(typeof introduce_money[date_list[i]] != 'undefined'){
                        money_list.push(introduce_money[date_list[i]].money);
                    }else{
                        money_list.push(0)
                    }
                }
                series.push({
                                name:'我的佣金',
                                type:'line',
                                xAxisIndex: 1,
                                smooth: true,
                                data:money_list
                            });
            }else if(type == 2){

                var series = [];
                var wx_money_list = [];
                var ali_money_list = [];
                for(var i in date_list){
                    if(typeof wx_order_money[date_list[i]] != 'undefined'){
                        wx_money_list.push(wx_order_money[date_list[i]].money);
                    }else{
                        wx_money_list.push(0)
                    }
                    if(typeof ali_order_money[date_list[i]] != 'undefined'){
                        ali_money_list.push(ali_order_money[date_list[i]].money);
                    }else{
                        ali_money_list.push(0)
                    }
                }
                series.push({
                                name:'微信',
                                type:'line',
                                xAxisIndex: 1,
                                smooth: true,
                                data:wx_money_list
                            });
                series.push({
                                name:'支付宝',
                                type:'line',
                                xAxisIndex: 1,
                                smooth: true,
                                data:ali_money_list
                            });

            }else if(type == 3){

                var series = [];
                var wx_money_list = [];
                var ali_money_list = [];
                for(var i in date_list){
                    if(typeof wx_order_money[date_list[i]] != 'undefined'){
                        wx_money_list.push(wx_order_money[date_list[i]].count);
                    }else{
                        wx_money_list.push(0)
                    }
                    if(typeof ali_order_money[date_list[i]] != 'undefined'){
                        ali_money_list.push(ali_order_money[date_list[i]].count);
                    }else{
                        ali_money_list.push(0)
                    }
                }
                series.push({
                                name:'微信',
                                type:'line',
                                xAxisIndex: 1,
                                smooth: true,
                                data:wx_money_list
                            });
                series.push({
                                name:'支付宝',
                                type:'line',
                                xAxisIndex: 1,
                                smooth: true,
                                data:ali_money_list
                            });

            }else if(type == 4){

                var series = [];
                var wx_money_list = [];
                var ali_money_list = [];
                for(var i in date_list){
                    if(typeof wx_back_money[date_list[i]] != 'undefined'){
                        wx_money_list.push(wx_back_money[date_list[i]].money);
                    }else{
                        wx_money_list.push(0)
                    }
                    if(typeof ali_back_money[date_list[i]] != 'undefined'){
                        ali_money_list.push(ali_back_money[date_list[i]].money);
                    }else{
                        ali_money_list.push(0)
                    }
                }
                series.push({
                                name:'微信',
                                type:'line',
                                xAxisIndex: 1,
                                smooth: true,
                                data:wx_money_list
                            });
                series.push({
                                name:'支付宝',
                                type:'line',
                                xAxisIndex: 1,
                                smooth: true,
                                data:ali_money_list
                            });

            }else if(type == 5){

                var series = [];
                var wx_money_list = [];
                var ali_money_list = [];
                for(var i in date_list){
                    if(typeof wx_back_money[date_list[i]] != 'undefined'){
                        wx_money_list.push(wx_back_money[date_list[i]].count);
                    }else{
                        wx_money_list.push(0)
                    }
                    if(typeof ali_back_money[date_list[i]] != 'undefined'){
                        ali_money_list.push(ali_back_money[date_list[i]].count);
                    }else{
                        ali_money_list.push(0)
                    }
                }
                series.push({
                                name:'微信',
                                type:'line',
                                xAxisIndex: 1,
                                smooth: true,
                                data:wx_money_list
                            });
                series.push({
                                name:'支付宝',
                                type:'line',
                                xAxisIndex: 1,
                                smooth: true,
                                data:ali_money_list
                            });

            }


            var option = {
                color: colors,

                tooltip: {
                    trigger: 'none',
                    axisPointer: {
                        type: 'cross'
                    }
                },
                legend: {
                    data:[]
                },
                grid: {
                    top: 70,
                    bottom: 50
                },
                xAxis: [
                    {
                        type: 'category',
                        axisTick: {
                            alignWithLabel: true
                        },
                        axisLine: {
                            onZero: false,
                            lineStyle: {
                                color: colors[1]
                            }
                        },
                        axisPointer: {
                            label: {
                                formatter: function (params) {
                                    return '交易金额  ' + params.value
                                        + (params.seriesData.length ? '：' + params.seriesData[0].data : '');
                                }
                            }
                        },
                        data: date_list
                    },
                    {
                        type: 'category',
                        axisTick: {
                            alignWithLabel: true
                        },
                        axisLine: {
                            onZero: false,
                            lineStyle: {
                                color: colors[0]
                            }
                        },
                        axisPointer: {
                            label: {
                                formatter: function (params) {
                                    return '交易金额  ' + params.value
                                        + (params.seriesData.length ? '：' + params.seriesData[0].data : '');
                                }
                            }
                        },
                        data: date_list
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: series
            };

            var dom = document.getElementById("table");
            var myChart = echarts.init(dom);
            var app = {};
            app.title = '多 X 轴示例';
            if (option && typeof option === "object") {
                myChart.setOption(option, true);
            }
        }
        $(function(){
            checkDate(-3);
            memberDate(-7);
            getStatistics();
            getDayStatistics();
            getDayPay();
            // initData();
        })
        function circle(data){
            var app = {}
            app.title = '环形图';

            var option = {
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b}: {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    x: 'left',
                    data:['黑钻合伙人','普通合伙人','高级合伙人','钻石合伙人','商户数']
                },
                series: [
                    {
                        name:'访问来源',
                        type:'pie',
                        radius: ['50%', '70%'],
                        avoidLabelOverlap: false,
                        label: {
                            normal: {
                                show: false,
                                position: 'center'
                            },
                            emphasis: {
                                show: true,
                                textStyle: {
                                    fontSize: '30',
                                    fontWeight: 'bold'
                                }
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false
                            }
                        },
                        data:[
                            {value:data.agent_normal, name:'普通合伙人'},
                            {value:data.agent_gold, name:'高级合伙人'},
                            {value:data.agent_pt, name:'钻石合伙人'},
                            {value:data.agent_diamon, name:'黑钻合伙人'},
                            {value:data.business, name:'商户数'}
                        ]
                    }
                ]
            };
            var dom = document.getElementById("circle");
            var myChart = echarts.init(dom);
            if (option && typeof option === "object") {
                myChart.setOption(option, true);
            }

        }
        
    </script>

</body>
</html>
