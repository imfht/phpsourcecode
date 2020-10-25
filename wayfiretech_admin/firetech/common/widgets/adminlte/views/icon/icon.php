<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-30 17:51:53
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-25 01:36:20
 */
use richardfan\widget\JSRegister;
use yii\bootstrap\Modal;
use yii\helpers\Html;

?>
<!-- demo style -->
<style>
    /* FROM HTTP://WWW.GETBOOTSTRAP.COM
         * Glyphicons
         *
         * Special styles for displaying the icons and their classes in the docs.
         */

    .bs-glyphicons {
        padding-left: 0;
        padding-bottom: 1px;
        margin-bottom: 20px;
        list-style: none;
        overflow: hidden;
    }

    .bs-glyphicons li {
        float: left;
        width: 25%;
        height: 115px;
        padding: 10px;
        margin: 0 -1px -1px 0;
        font-size: 12px;
        line-height: 1.4;
        text-align: center;
        border: 1px solid #ddd;
    }

    .bs-glyphicons .glyphicon {
        margin-top: 5px;
        margin-bottom: 10px;
        font-size: 24px;
    }

    .bs-glyphicons .fa {
        margin-top: 5px;
        margin-bottom: 10px;
        font-size: 24px;
    }

    .bs-glyphicons .glyphicon-class {
        display: block;
        text-align: center;
        word-wrap: break-word;
        /* Help out IE10+ with class names */
    }

    .bs-glyphicons li:hover {
        background-color: rgba(86, 61, 124, .1);
        cursor: pointer;

    }
    .modal-body{
        padding: 0px;
    }

    @media (min-width: 768px) {
        .bs-glyphicons li {
            width: 25%;
        }
    }
</style>

<div class="input-group">
    <span class="input-group-addon" id="iconyl"><?= $model->$attribute ? "<i class='".$model->$attribute."'></i>" : '*'; ?></span>
    <?= Html::activeInput($type, $model, $attribute, [
        'class' => 'form-control',
        'id' => 'iconinput',
    ]); ?>
    <div class="input-group-btn">
        <button class=" btn-default btn" type="button" data-toggle="modal" href='#modal-id'>选择图标</button>
    </div>
</div>

<?php Modal::begin([
            'header' => '<ul class="nav nav-tabs">
            <li   class="active"><a href="#web-application" data-toggle="tab">web应用</a></li>
            <li><a href="#hand" data-toggle="tab">手势</a></li>
            <li><a href="#transportation" data-toggle="tab">交通</a></li>
            <li><a href="#gender" data-toggle="tab">性别</a></li>
            <li><a href="#spinner" data-toggle="tab">加载</a></li>
            <li><a href="#form-control" data-toggle="tab">常用操作</a></li>
            <li><a href="#payment" data-toggle="tab">支付</a></li>
            <li><a href="#chart" data-toggle="tab">图表统计</a></li>
            <li><a href="#currency" data-toggle="tab">货币符号</a></li>
            <li><a href="#text-editor" data-toggle="tab">文本编辑</a></li>
            <li><a href="#directional" data-toggle="tab">指向</a></li>
            <li><a href="#video-player" data-toggle="tab">多媒体</a></li>
            <li><a href="#brand" data-toggle="tab">商标</a></li>
            <li><a href="#medical" data-toggle="tab">医疗</a></li>
            <!-- <li class="active"><a href="#fa-icons" data-toggle="tab">Font Awesome</a></li> -->
            <li><a href="#glyphicons" data-toggle="tab">字体图标</a></li>
            <li><a href="#new" data-toggle="tab">其他</a></li>
        </ul>',
            'toggleButton' => false,
            'options' => [
                'id' => 'modal-id',
                'style' => 'padding:0px;',
            ],
            'size' => 'modal-lg',
            'footer' => '
            当前选中图标：<span  id="dqxztb"></span>  
            <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
            <button type="button" class="btn btn-primary avatar-save" data-dismiss="modal">保存</button>',
        ]); ?>
         <div class="nav-tabs-custom">                               
                                <div class="tab-content " style="height:400px;overflow: auto;">
                                    <!-- Font Awesome Icons -->
                                   
                                    <section id="new" class="tab-pane">
                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-500px"></span>
                                                        <span class="glyphicon-class"> fa-500px</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-amazon"></span>
                                                        <span class="glyphicon-class"> fa-amazon</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-balance-scale"></span>
                                                        <span class="glyphicon-class"> fa-balance-scale
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-0"></span>
                                                        <span class="glyphicon-class"> fa-battery-0
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-1"></span>
                                                        <span class="glyphicon-class"> fa-battery-1
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-2"></span>
                                                        <span class="glyphicon-class"> fa-battery-2
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-3"></span>
                                                        <span class="glyphicon-class"> fa-battery-3
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-4"></span>
                                                        <span class="glyphicon-class"> fa-battery-4
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-empty"></span>
                                                        <span class="glyphicon-class"> fa-battery-empty
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-full"></span>
                                                        <span class="glyphicon-class"> fa-battery-full
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-half"></span>
                                                        <span class="glyphicon-class"> fa-battery-half
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-quarter"></span>
                                                        <span class="glyphicon-class"> fa-battery-quarter
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-three-quarters"></span>
                                                        <span class="glyphicon-class"> fa-battery-three-quarters
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-black-tie"></span>
                                                        <span class="glyphicon-class"> fa-black-tie</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-calendar-check-o"></span>
                                                        <span class="glyphicon-class"> fa-calendar-check-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-calendar-minus-o"></span>
                                                        <span class="glyphicon-class"> fa-calendar-minus-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-calendar-plus-o"></span>
                                                        <span class="glyphicon-class"> fa-calendar-plus-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-calendar-times-o"></span>
                                                        <span class="glyphicon-class"> fa-calendar-times-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-diners-club"></span>
                                                        <span class="glyphicon-class"> fa-cc-diners-club
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-jcb"></span>
                                                        <span class="glyphicon-class"> fa-cc-jcb</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chrome"></span>
                                                        <span class="glyphicon-class"> fa-chrome</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-clone"></span>
                                                        <span class="glyphicon-class"> fa-clone</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-commenting"></span>
                                                        <span class="glyphicon-class"> fa-commenting
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-commenting-o"></span>
                                                        <span class="glyphicon-class"> fa-commenting-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-contao"></span>
                                                        <span class="glyphicon-class"> fa-contao</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-creative-commons"></span>
                                                        <span class="glyphicon-class"> fa-creative-commons
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-expeditedssl"></span>
                                                        <span class="glyphicon-class"> fa-expeditedssl
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-firefox"></span>
                                                        <span class="glyphicon-class"> fa-firefox</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-fonticons"></span>
                                                        <span class="glyphicon-class"> fa-fonticons</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-genderless"></span>
                                                        <span class="glyphicon-class"> fa-genderless
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-get-pocket"></span>
                                                        <span class="glyphicon-class"> fa-get-pocket
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gg"></span>
                                                        <span class="glyphicon-class"> fa-gg</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gg-circle"></span>
                                                        <span class="glyphicon-class"> fa-gg-circle</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-grab-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-grab-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-lizard-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-lizard-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-paper-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-paper-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-peace-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-peace-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-pointer-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-pointer-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-rock-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-rock-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-scissors-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-scissors-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-spock-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-spock-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-stop-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-stop-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass"></span>
                                                        <span class="glyphicon-class"> fa-hourglass</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-1"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-1
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-2"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-2
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-3"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-3
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-end"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-end
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-half"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-half
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-o"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-start"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-start
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-houzz"></span>
                                                        <span class="glyphicon-class"> fa-houzz</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-i-cursor"></span>
                                                        <span class="glyphicon-class"> fa-i-cursor</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-industry"></span>
                                                        <span class="glyphicon-class"> fa-industry</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-internet-explorer"></span>
                                                        <span class="glyphicon-class"> fa-internet-explorer
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-map"></span>
                                                        <span class="glyphicon-class"> fa-map</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-map-o"></span>
                                                        <span class="glyphicon-class"> fa-map-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-map-pin"></span>
                                                        <span class="glyphicon-class"> fa-map-pin</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-map-signs"></span>
                                                        <span class="glyphicon-class"> fa-map-signs</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mouse-pointer"></span>
                                                        <span class="glyphicon-class"> fa-mouse-pointer
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-object-group"></span>
                                                        <span class="glyphicon-class"> fa-object-group
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-object-ungroup"></span>
                                                        <span class="glyphicon-class"> fa-object-ungroup
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-odnoklassniki"></span>
                                                        <span class="glyphicon-class"> fa-odnoklassniki
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-odnoklassniki-square"></span>
                                                        <span class="glyphicon-class"> fa-odnoklassniki-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-opencart"></span>
                                                        <span class="glyphicon-class"> fa-opencart</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-opera"></span>
                                                        <span class="glyphicon-class"> fa-opera</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-optin-monster"></span>
                                                        <span class="glyphicon-class"> fa-optin-monster
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-registered"></span>
                                                        <span class="glyphicon-class"> fa-registered
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-safari"></span>
                                                        <span class="glyphicon-class"> fa-safari</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sticky-note"></span>
                                                        <span class="glyphicon-class"> fa-sticky-note
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sticky-note-o"></span>
                                                        <span class="glyphicon-class"> fa-sticky-note-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-television"></span>
                                                        <span class="glyphicon-class"> fa-television
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-trademark"></span>
                                                        <span class="glyphicon-class"> fa-trademark</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tripadvisor"></span>
                                                        <span class="glyphicon-class"> fa-tripadvisor
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tv"></span>
                                                        <span class="glyphicon-class"> fa-tv</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-vimeo"></span>
                                                        <span class="glyphicon-class"> fa-vimeo</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-wikipedia-w"></span>
                                                        <span class="glyphicon-class"> fa-wikipedia-w
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-y-combinator"></span>
                                                        <span class="glyphicon-class"> fa-y-combinator
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-yc"></span>
                                                        <span class="glyphicon-class"> fa-yc</span>
                                                    </li>
                                                </ul>
                                            </section>


                                            <section id="web-application" class="tab-pane active">
                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-adjust"></span>
                                                        <span class="glyphicon-class"> fa-adjust</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-anchor"></span>
                                                        <span class="glyphicon-class"> fa-anchor</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-archive"></span>
                                                        <span class="glyphicon-class"> fa-archive</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-area-chart"></span>
                                                        <span class="glyphicon-class"> fa-area-chart
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrows"></span>
                                                        <span class="glyphicon-class"> fa-arrows</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrows-h"></span>
                                                        <span class="glyphicon-class"> fa-arrows-h</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrows-v"></span>
                                                        <span class="glyphicon-class"> fa-arrows-v</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-asterisk"></span>
                                                        <span class="glyphicon-class"> fa-asterisk</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-at"></span>
                                                        <span class="glyphicon-class"> fa-at</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-automobile"></span>
                                                        <span class="glyphicon-class"> fa-automobile
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-balance-scale"></span>
                                                        <span class="glyphicon-class"> fa-balance-scale
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-ban"></span>
                                                        <span class="glyphicon-class"> fa-ban</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bank"></span>
                                                        <span class="glyphicon-class"> fa-bank <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bar-chart"></span>
                                                        <span class="glyphicon-class"> fa-bar-chart</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bar-chart-o"></span>
                                                        <span class="glyphicon-class"> fa-bar-chart-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-barcode"></span>
                                                        <span class="glyphicon-class"> fa-barcode</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bars"></span>
                                                        <span class="glyphicon-class"> fa-bars</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-0"></span>
                                                        <span class="glyphicon-class"> fa-battery-0
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-1"></span>
                                                        <span class="glyphicon-class"> fa-battery-1
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-2"></span>
                                                        <span class="glyphicon-class"> fa-battery-2
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-3"></span>
                                                        <span class="glyphicon-class"> fa-battery-3
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-4"></span>
                                                        <span class="glyphicon-class"> fa-battery-4
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-empty"></span>
                                                        <span class="glyphicon-class"> fa-battery-empty
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-full"></span>
                                                        <span class="glyphicon-class"> fa-battery-full
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-half"></span>
                                                        <span class="glyphicon-class"> fa-battery-half
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-quarter"></span>
                                                        <span class="glyphicon-class"> fa-battery-quarter
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-battery-three-quarters"></span>
                                                        <span class="glyphicon-class"> fa-battery-three-quarters
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bed"></span>
                                                        <span class="glyphicon-class"> fa-bed</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-beer"></span>
                                                        <span class="glyphicon-class"> fa-beer</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bell"></span>
                                                        <span class="glyphicon-class"> fa-bell</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bell-o"></span>
                                                        <span class="glyphicon-class"> fa-bell-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bell-slash"></span>
                                                        <span class="glyphicon-class"> fa-bell-slash
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bell-slash-o"></span>
                                                        <span class="glyphicon-class"> fa-bell-slash-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bicycle"></span>
                                                        <span class="glyphicon-class"> fa-bicycle</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-binoculars"></span>
                                                        <span class="glyphicon-class"> fa-binoculars
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-birthday-cake"></span>
                                                        <span class="glyphicon-class"> fa-birthday-cake
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bolt"></span>
                                                        <span class="glyphicon-class"> fa-bolt</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bomb"></span>
                                                        <span class="glyphicon-class"> fa-bomb</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-book"></span>
                                                        <span class="glyphicon-class"> fa-book</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bookmark"></span>
                                                        <span class="glyphicon-class"> fa-bookmark</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bookmark-o"></span>
                                                        <span class="glyphicon-class"> fa-bookmark-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-briefcase"></span>
                                                        <span class="glyphicon-class"> fa-briefcase</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bug"></span>
                                                        <span class="glyphicon-class"> fa-bug</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-building"></span>
                                                        <span class="glyphicon-class"> fa-building</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-building-o"></span>
                                                        <span class="glyphicon-class"> fa-building-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bullhorn"></span>
                                                        <span class="glyphicon-class"> fa-bullhorn</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bullseye"></span>
                                                        <span class="glyphicon-class"> fa-bullseye</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bus"></span>
                                                        <span class="glyphicon-class"> fa-bus</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cab"></span>
                                                        <span class="glyphicon-class"> fa-cab <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-calculator"></span>
                                                        <span class="glyphicon-class"> fa-calculator
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-calendar"></span>
                                                        <span class="glyphicon-class"> fa-calendar</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-calendar-check-o"></span>
                                                        <span class="glyphicon-class"> fa-calendar-check-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-calendar-minus-o"></span>
                                                        <span class="glyphicon-class"> fa-calendar-minus-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-calendar-o"></span>
                                                        <span class="glyphicon-class"> fa-calendar-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-calendar-plus-o"></span>
                                                        <span class="glyphicon-class"> fa-calendar-plus-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-calendar-times-o"></span>
                                                        <span class="glyphicon-class"> fa-calendar-times-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-camera"></span>
                                                        <span class="glyphicon-class"> fa-camera</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-camera-retro"></span>
                                                        <span class="glyphicon-class"> fa-camera-retro
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-car"></span>
                                                        <span class="glyphicon-class"> fa-car</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-square-o-down"></span>
                                                        <span class="glyphicon-class"> fa-caret-square-o-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-square-o-left"></span>
                                                        <span class="glyphicon-class"> fa-caret-square-o-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-square-o-right"></span>
                                                        <span class="glyphicon-class"> fa-caret-square-o-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-square-o-up"></span>
                                                        <span class="glyphicon-class"> fa-caret-square-o-up
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cart-arrow-down"></span>
                                                        <span class="glyphicon-class"> fa-cart-arrow-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cart-plus"></span>
                                                        <span class="glyphicon-class"> fa-cart-plus</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc"></span>
                                                        <span class="glyphicon-class"> fa-cc</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-certificate"></span>
                                                        <span class="glyphicon-class"> fa-certificate
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-check"></span>
                                                        <span class="glyphicon-class"> fa-check</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-check-circle"></span>
                                                        <span class="glyphicon-class"> fa-check-circle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-check-circle-o"></span>
                                                        <span class="glyphicon-class"> fa-check-circle-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-check-square"></span>
                                                        <span class="glyphicon-class"> fa-check-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-check-square-o"></span>
                                                        <span class="glyphicon-class"> fa-check-square-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-child"></span>
                                                        <span class="glyphicon-class"> fa-child</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-circle"></span>
                                                        <span class="glyphicon-class"> fa-circle</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-circle-o"></span>
                                                        <span class="glyphicon-class"> fa-circle-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-circle-o-notch"></span>
                                                        <span class="glyphicon-class"> fa-circle-o-notch
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-circle-thin"></span>
                                                        <span class="glyphicon-class"> fa-circle-thin
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-clock-o"></span>
                                                        <span class="glyphicon-class"> fa-clock-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-clone"></span>
                                                        <span class="glyphicon-class"> fa-clone</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-close"></span>
                                                        <span class="glyphicon-class"> fa-close <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cloud"></span>
                                                        <span class="glyphicon-class"> fa-cloud</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cloud-download"></span>
                                                        <span class="glyphicon-class"> fa-cloud-download
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cloud-upload"></span>
                                                        <span class="glyphicon-class"> fa-cloud-upload
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-code"></span>
                                                        <span class="glyphicon-class"> fa-code</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-code-fork"></span>
                                                        <span class="glyphicon-class"> fa-code-fork</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-coffee"></span>
                                                        <span class="glyphicon-class"> fa-coffee</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cog"></span>
                                                        <span class="glyphicon-class"> fa-cog</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cogs"></span>
                                                        <span class="glyphicon-class"> fa-cogs</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-comment"></span>
                                                        <span class="glyphicon-class"> fa-comment</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-comment-o"></span>
                                                        <span class="glyphicon-class"> fa-comment-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-commenting"></span>
                                                        <span class="glyphicon-class"> fa-commenting
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-commenting-o"></span>
                                                        <span class="glyphicon-class"> fa-commenting-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-comments"></span>
                                                        <span class="glyphicon-class"> fa-comments</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-comments-o"></span>
                                                        <span class="glyphicon-class"> fa-comments-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-compass"></span>
                                                        <span class="glyphicon-class"> fa-compass</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-copyright"></span>
                                                        <span class="glyphicon-class"> fa-copyright</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-creative-commons"></span>
                                                        <span class="glyphicon-class"> fa-creative-commons
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-credit-card"></span>
                                                        <span class="glyphicon-class"> fa-credit-card
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-crop"></span>
                                                        <span class="glyphicon-class"> fa-crop</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-crosshairs"></span>
                                                        <span class="glyphicon-class"> fa-crosshairs
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cube"></span>
                                                        <span class="glyphicon-class"> fa-cube</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cubes"></span>
                                                        <span class="glyphicon-class"> fa-cubes</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cutlery"></span>
                                                        <span class="glyphicon-class"> fa-cutlery</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-dashboard"></span>
                                                        <span class="glyphicon-class"> fa-dashboard
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-database"></span>
                                                        <span class="glyphicon-class"> fa-database</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-desktop"></span>
                                                        <span class="glyphicon-class"> fa-desktop</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-diamond"></span>
                                                        <span class="glyphicon-class"> fa-diamond</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-dot-circle-o"></span>
                                                        <span class="glyphicon-class"> fa-dot-circle-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-download"></span>
                                                        <span class="glyphicon-class"> fa-download</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-edit"></span>
                                                        <span class="glyphicon-class"> fa-edit <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-ellipsis-h"></span>
                                                        <span class="glyphicon-class"> fa-ellipsis-h
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-ellipsis-v"></span>
                                                        <span class="glyphicon-class"> fa-ellipsis-v
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-envelope"></span>
                                                        <span class="glyphicon-class"> fa-envelope</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-envelope-o"></span>
                                                        <span class="glyphicon-class"> fa-envelope-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-envelope-square"></span>
                                                        <span class="glyphicon-class"> fa-envelope-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-eraser"></span>
                                                        <span class="glyphicon-class"> fa-eraser</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-exchange"></span>
                                                        <span class="glyphicon-class"> fa-exchange</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-exclamation"></span>
                                                        <span class="glyphicon-class"> fa-exclamation
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-exclamation-circle"></span>
                                                        <span class="glyphicon-class"> fa-exclamation-circle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-exclamation-triangle"></span>
                                                        <span class="glyphicon-class"> fa-exclamation-triangle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-external-link"></span>
                                                        <span class="glyphicon-class"> fa-external-link
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-external-link-square"></span>
                                                        <span class="glyphicon-class"> fa-external-link-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-eye"></span>
                                                        <span class="glyphicon-class"> fa-eye</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-eye-slash"></span>
                                                        <span class="glyphicon-class"> fa-eye-slash</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-eyedropper"></span>
                                                        <span class="glyphicon-class"> fa-eyedropper
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-fax"></span>
                                                        <span class="glyphicon-class"> fa-fax</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-feed"></span>
                                                        <span class="glyphicon-class"> fa-feed <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-female"></span>
                                                        <span class="glyphicon-class"> fa-female</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-fighter-jet"></span>
                                                        <span class="glyphicon-class"> fa-fighter-jet
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-archive-o"></span>
                                                        <span class="glyphicon-class"> fa-file-archive-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-audio-o"></span>
                                                        <span class="glyphicon-class"> fa-file-audio-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-code-o"></span>
                                                        <span class="glyphicon-class"> fa-file-code-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-excel-o"></span>
                                                        <span class="glyphicon-class"> fa-file-excel-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-image-o"></span>
                                                        <span class="glyphicon-class"> fa-file-image-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-movie-o"></span>
                                                        <span class="glyphicon-class"> fa-file-movie-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-pdf-o"></span>
                                                        <span class="glyphicon-class"> fa-file-pdf-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-photo-o"></span>
                                                        <span class="glyphicon-class"> fa-file-photo-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-picture-o"></span>
                                                        <span class="glyphicon-class"> fa-file-picture-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-powerpoint-o"></span>
                                                        <span class="glyphicon-class"> fa-file-powerpoint-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-sound-o"></span>
                                                        <span class="glyphicon-class"> fa-file-sound-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-video-o"></span>
                                                        <span class="glyphicon-class"> fa-file-video-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-word-o"></span>
                                                        <span class="glyphicon-class"> fa-file-word-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-zip-o"></span>
                                                        <span class="glyphicon-class"> fa-file-zip-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-film"></span>
                                                        <span class="glyphicon-class"> fa-film</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-filter"></span>
                                                        <span class="glyphicon-class"> fa-filter</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-fire"></span>
                                                        <span class="glyphicon-class"> fa-fire</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-fire-extinguisher"></span>
                                                        <span class="glyphicon-class"> fa-fire-extinguisher
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-flag"></span>
                                                        <span class="glyphicon-class"> fa-flag</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-flag-checkered"></span>
                                                        <span class="glyphicon-class"> fa-flag-checkered
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-flag-o"></span>
                                                        <span class="glyphicon-class"> fa-flag-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-flash"></span>
                                                        <span class="glyphicon-class"> fa-flash <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-flask"></span>
                                                        <span class="glyphicon-class"> fa-flask</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-folder"></span>
                                                        <span class="glyphicon-class"> fa-folder</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-folder-o"></span>
                                                        <span class="glyphicon-class"> fa-folder-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-folder-open"></span>
                                                        <span class="glyphicon-class"> fa-folder-open
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-folder-open-o"></span>
                                                        <span class="glyphicon-class"> fa-folder-open-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-frown-o"></span>
                                                        <span class="glyphicon-class"> fa-frown-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-futbol-o"></span>
                                                        <span class="glyphicon-class"> fa-futbol-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gamepad"></span>
                                                        <span class="glyphicon-class"> fa-gamepad</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gavel"></span>
                                                        <span class="glyphicon-class"> fa-gavel</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gear"></span>
                                                        <span class="glyphicon-class"> fa-gear <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gears"></span>
                                                        <span class="glyphicon-class"> fa-gears <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gift"></span>
                                                        <span class="glyphicon-class"> fa-gift</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-glass"></span>
                                                        <span class="glyphicon-class"> fa-glass</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-globe"></span>
                                                        <span class="glyphicon-class"> fa-globe</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-graduation-cap"></span>
                                                        <span class="glyphicon-class"> fa-graduation-cap
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-group"></span>
                                                        <span class="glyphicon-class"> fa-group <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-grab-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-grab-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-lizard-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-lizard-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-paper-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-paper-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-peace-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-peace-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-pointer-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-pointer-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-rock-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-rock-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-scissors-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-scissors-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-spock-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-spock-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-stop-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-stop-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hdd-o"></span>
                                                        <span class="glyphicon-class"> fa-hdd-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-headphones"></span>
                                                        <span class="glyphicon-class"> fa-headphones
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-heart"></span>
                                                        <span class="glyphicon-class"> fa-heart</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-heart-o"></span>
                                                        <span class="glyphicon-class"> fa-heart-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-heartbeat"></span>
                                                        <span class="glyphicon-class"> fa-heartbeat</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-history"></span>
                                                        <span class="glyphicon-class"> fa-history</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-home"></span>
                                                        <span class="glyphicon-class"> fa-home</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hotel"></span>
                                                        <span class="glyphicon-class"> fa-hotel <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass"></span>
                                                        <span class="glyphicon-class"> fa-hourglass</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-1"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-1
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-2"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-2
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-3"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-3
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-end"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-end
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-half"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-half
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-o"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hourglass-start"></span>
                                                        <span class="glyphicon-class"> fa-hourglass-start
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-i-cursor"></span>
                                                        <span class="glyphicon-class"> fa-i-cursor</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-image"></span>
                                                        <span class="glyphicon-class"> fa-image <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-inbox"></span>
                                                        <span class="glyphicon-class"> fa-inbox</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-industry"></span>
                                                        <span class="glyphicon-class"> fa-industry</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-info"></span>
                                                        <span class="glyphicon-class"> fa-info</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-info-circle"></span>
                                                        <span class="glyphicon-class"> fa-info-circle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-institution"></span>
                                                        <span class="glyphicon-class"> fa-institution
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-key"></span>
                                                        <span class="glyphicon-class"> fa-key</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-keyboard-o"></span>
                                                        <span class="glyphicon-class"> fa-keyboard-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-language"></span>
                                                        <span class="glyphicon-class"> fa-language</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-laptop"></span>
                                                        <span class="glyphicon-class"> fa-laptop</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-leaf"></span>
                                                        <span class="glyphicon-class"> fa-leaf</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-legal"></span>
                                                        <span class="glyphicon-class"> fa-legal <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-lemon-o"></span>
                                                        <span class="glyphicon-class"> fa-lemon-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-level-down"></span>
                                                        <span class="glyphicon-class"> fa-level-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-level-up"></span>
                                                        <span class="glyphicon-class"> fa-level-up</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-life-bouy"></span>
                                                        <span class="glyphicon-class"> fa-life-bouy
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-life-buoy"></span>
                                                        <span class="glyphicon-class"> fa-life-buoy
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-life-ring"></span>
                                                        <span class="glyphicon-class"> fa-life-ring</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-life-saver"></span>
                                                        <span class="glyphicon-class"> fa-life-saver
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-lightbulb-o"></span>
                                                        <span class="glyphicon-class"> fa-lightbulb-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-line-chart"></span>
                                                        <span class="glyphicon-class"> fa-line-chart
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-location-arrow"></span>
                                                        <span class="glyphicon-class"> fa-location-arrow
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-lock"></span>
                                                        <span class="glyphicon-class"> fa-lock</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-magic"></span>
                                                        <span class="glyphicon-class"> fa-magic</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-magnet"></span>
                                                        <span class="glyphicon-class"> fa-magnet</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mail-forward"></span>
                                                        <span class="glyphicon-class"> fa-mail-forward
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mail-reply"></span>
                                                        <span class="glyphicon-class"> fa-mail-reply
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mail-reply-all"></span>
                                                        <span class="glyphicon-class"> fa-mail-reply-all
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-male"></span>
                                                        <span class="glyphicon-class"> fa-male</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-map"></span>
                                                        <span class="glyphicon-class"> fa-map</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-map-marker"></span>
                                                        <span class="glyphicon-class"> fa-map-marker
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-map-o"></span>
                                                        <span class="glyphicon-class"> fa-map-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-map-pin"></span>
                                                        <span class="glyphicon-class"> fa-map-pin</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-map-signs"></span>
                                                        <span class="glyphicon-class"> fa-map-signs</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-meh-o"></span>
                                                        <span class="glyphicon-class"> fa-meh-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-microphone"></span>
                                                        <span class="glyphicon-class"> fa-microphone
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-microphone-slash"></span>
                                                        <span class="glyphicon-class"> fa-microphone-slash
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-minus"></span>
                                                        <span class="glyphicon-class"> fa-minus</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-minus-circle"></span>
                                                        <span class="glyphicon-class"> fa-minus-circle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-minus-square"></span>
                                                        <span class="glyphicon-class"> fa-minus-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-minus-square-o"></span>
                                                        <span class="glyphicon-class"> fa-minus-square-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mobile"></span>
                                                        <span class="glyphicon-class"> fa-mobile</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mobile-phone"></span>
                                                        <span class="glyphicon-class"> fa-mobile-phone
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-money"></span>
                                                        <span class="glyphicon-class"> fa-money</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-moon-o"></span>
                                                        <span class="glyphicon-class"> fa-moon-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mortar-board"></span>
                                                        <span class="glyphicon-class"> fa-mortar-board
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-motorcycle"></span>
                                                        <span class="glyphicon-class"> fa-motorcycle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mouse-pointer"></span>
                                                        <span class="glyphicon-class"> fa-mouse-pointer
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-music"></span>
                                                        <span class="glyphicon-class"> fa-music</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-navicon"></span>
                                                        <span class="glyphicon-class"> fa-navicon
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-newspaper-o"></span>
                                                        <span class="glyphicon-class"> fa-newspaper-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-object-group"></span>
                                                        <span class="glyphicon-class"> fa-object-group
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-object-ungroup"></span>
                                                        <span class="glyphicon-class"> fa-object-ungroup
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-paint-brush"></span>
                                                        <span class="glyphicon-class"> fa-paint-brush
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-paper-plane"></span>
                                                        <span class="glyphicon-class"> fa-paper-plane
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-paper-plane-o"></span>
                                                        <span class="glyphicon-class"> fa-paper-plane-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-paw"></span>
                                                        <span class="glyphicon-class"> fa-paw</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pencil"></span>
                                                        <span class="glyphicon-class"> fa-pencil</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pencil-square"></span>
                                                        <span class="glyphicon-class"> fa-pencil-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pencil-square-o"></span>
                                                        <span class="glyphicon-class"> fa-pencil-square-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-phone"></span>
                                                        <span class="glyphicon-class"> fa-phone</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-phone-square"></span>
                                                        <span class="glyphicon-class"> fa-phone-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-photo"></span>
                                                        <span class="glyphicon-class"> fa-photo <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-picture-o"></span>
                                                        <span class="glyphicon-class"> fa-picture-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pie-chart"></span>
                                                        <span class="glyphicon-class"> fa-pie-chart</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-plane"></span>
                                                        <span class="glyphicon-class"> fa-plane</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-plug"></span>
                                                        <span class="glyphicon-class"> fa-plug</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-plus"></span>
                                                        <span class="glyphicon-class"> fa-plus</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-plus-circle"></span>
                                                        <span class="glyphicon-class"> fa-plus-circle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-plus-square"></span>
                                                        <span class="glyphicon-class"> fa-plus-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-plus-square-o"></span>
                                                        <span class="glyphicon-class"> fa-plus-square-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-power-off"></span>
                                                        <span class="glyphicon-class"> fa-power-off</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-print"></span>
                                                        <span class="glyphicon-class"> fa-print</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-puzzle-piece"></span>
                                                        <span class="glyphicon-class"> fa-puzzle-piece
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-qrcode"></span>
                                                        <span class="glyphicon-class"> fa-qrcode</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-question"></span>
                                                        <span class="glyphicon-class"> fa-question</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-question-circle"></span>
                                                        <span class="glyphicon-class"> fa-question-circle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-quote-left"></span>
                                                        <span class="glyphicon-class"> fa-quote-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-quote-right"></span>
                                                        <span class="glyphicon-class"> fa-quote-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-random"></span>
                                                        <span class="glyphicon-class"> fa-random</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-recycle"></span>
                                                        <span class="glyphicon-class"> fa-recycle</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-refresh"></span>
                                                        <span class="glyphicon-class"> fa-refresh</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-registered"></span>
                                                        <span class="glyphicon-class"> fa-registered
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-remove"></span>
                                                        <span class="glyphicon-class"> fa-remove
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-reorder"></span>
                                                        <span class="glyphicon-class"> fa-reorder
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-reply"></span>
                                                        <span class="glyphicon-class"> fa-reply</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-reply-all"></span>
                                                        <span class="glyphicon-class"> fa-reply-all</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-retweet"></span>
                                                        <span class="glyphicon-class"> fa-retweet</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-road"></span>
                                                        <span class="glyphicon-class"> fa-road</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-rocket"></span>
                                                        <span class="glyphicon-class"> fa-rocket</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-rss"></span>
                                                        <span class="glyphicon-class"> fa-rss</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-rss-square"></span>
                                                        <span class="glyphicon-class"> fa-rss-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-search"></span>
                                                        <span class="glyphicon-class"> fa-search</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-search-minus"></span>
                                                        <span class="glyphicon-class"> fa-search-minus
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-search-plus"></span>
                                                        <span class="glyphicon-class"> fa-search-plus
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-send"></span>
                                                        <span class="glyphicon-class"> fa-send <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-send-o"></span>
                                                        <span class="glyphicon-class"> fa-send-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-server"></span>
                                                        <span class="glyphicon-class"> fa-server</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-share"></span>
                                                        <span class="glyphicon-class"> fa-share</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-share-alt"></span>
                                                        <span class="glyphicon-class"> fa-share-alt</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-share-alt-square"></span>
                                                        <span class="glyphicon-class"> fa-share-alt-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-share-square"></span>
                                                        <span class="glyphicon-class"> fa-share-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-share-square-o"></span>
                                                        <span class="glyphicon-class"> fa-share-square-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-shield"></span>
                                                        <span class="glyphicon-class"> fa-shield</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-ship"></span>
                                                        <span class="glyphicon-class"> fa-ship</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-shopping-cart"></span>
                                                        <span class="glyphicon-class"> fa-shopping-cart
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sign-in"></span>
                                                        <span class="glyphicon-class"> fa-sign-in</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sign-out"></span>
                                                        <span class="glyphicon-class"> fa-sign-out</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-signal"></span>
                                                        <span class="glyphicon-class"> fa-signal</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sitemap"></span>
                                                        <span class="glyphicon-class"> fa-sitemap</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sliders"></span>
                                                        <span class="glyphicon-class"> fa-sliders</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-smile-o"></span>
                                                        <span class="glyphicon-class"> fa-smile-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-soccer-ball-o"></span>
                                                        <span class="glyphicon-class"> fa-soccer-ball-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sort"></span>
                                                        <span class="glyphicon-class"> fa-sort</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sort-alpha-asc"></span>
                                                        <span class="glyphicon-class"> fa-sort-alpha-asc
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sort-alpha-desc"></span>
                                                        <span class="glyphicon-class"> fa-sort-alpha-desc
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sort-amount-asc"></span>
                                                        <span class="glyphicon-class"> fa-sort-amount-asc
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sort-amount-desc"></span>
                                                        <span class="glyphicon-class"> fa-sort-amount-desc
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sort-asc"></span>
                                                        <span class="glyphicon-class"> fa-sort-asc</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sort-desc"></span>
                                                        <span class="glyphicon-class"> fa-sort-desc</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sort-down"></span>
                                                        <span class="glyphicon-class"> fa-sort-down
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sort-numeric-asc"></span>
                                                        <span class="glyphicon-class"> fa-sort-numeric-asc
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sort-numeric-desc"></span>
                                                        <span class="glyphicon-class"> fa-sort-numeric-desc
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sort-up"></span>
                                                        <span class="glyphicon-class"> fa-sort-up
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-space-shuttle"></span>
                                                        <span class="glyphicon-class"> fa-space-shuttle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-spinner"></span>
                                                        <span class="glyphicon-class"> fa-spinner</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-spoon"></span>
                                                        <span class="glyphicon-class"> fa-spoon</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-square"></span>
                                                        <span class="glyphicon-class"> fa-square</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-square-o"></span>
                                                        <span class="glyphicon-class"> fa-square-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-star"></span>
                                                        <span class="glyphicon-class"> fa-star</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-star-half"></span>
                                                        <span class="glyphicon-class"> fa-star-half</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-star-half-empty"></span>
                                                        <span class="glyphicon-class"> fa-star-half-empty
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-star-half-full"></span>
                                                        <span class="glyphicon-class"> fa-star-half-full
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-star-half-o"></span>
                                                        <span class="glyphicon-class"> fa-star-half-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-star-o"></span>
                                                        <span class="glyphicon-class"> fa-star-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sticky-note"></span>
                                                        <span class="glyphicon-class"> fa-sticky-note
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sticky-note-o"></span>
                                                        <span class="glyphicon-class"> fa-sticky-note-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-street-view"></span>
                                                        <span class="glyphicon-class"> fa-street-view
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-suitcase"></span>
                                                        <span class="glyphicon-class"> fa-suitcase</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sun-o"></span>
                                                        <span class="glyphicon-class"> fa-sun-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-support"></span>
                                                        <span class="glyphicon-class"> fa-support
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tablet"></span>
                                                        <span class="glyphicon-class"> fa-tablet</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tachometer"></span>
                                                        <span class="glyphicon-class"> fa-tachometer
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tag"></span>
                                                        <span class="glyphicon-class"> fa-tag</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tags"></span>
                                                        <span class="glyphicon-class"> fa-tags</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tasks"></span>
                                                        <span class="glyphicon-class"> fa-tasks</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-taxi"></span>
                                                        <span class="glyphicon-class"> fa-taxi</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-television"></span>
                                                        <span class="glyphicon-class"> fa-television
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-terminal"></span>
                                                        <span class="glyphicon-class"> fa-terminal</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-thumb-tack"></span>
                                                        <span class="glyphicon-class"> fa-thumb-tack
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-thumbs-down"></span>
                                                        <span class="glyphicon-class"> fa-thumbs-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-thumbs-o-down"></span>
                                                        <span class="glyphicon-class"> fa-thumbs-o-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-thumbs-o-up"></span>
                                                        <span class="glyphicon-class"> fa-thumbs-o-up
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-thumbs-up"></span>
                                                        <span class="glyphicon-class"> fa-thumbs-up</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-ticket"></span>
                                                        <span class="glyphicon-class"> fa-ticket</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-times"></span>
                                                        <span class="glyphicon-class"> fa-times</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-times-circle"></span>
                                                        <span class="glyphicon-class"> fa-times-circle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-times-circle-o"></span>
                                                        <span class="glyphicon-class"> fa-times-circle-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tint"></span>
                                                        <span class="glyphicon-class"> fa-tint</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-toggle-down"></span>
                                                        <span class="glyphicon-class"> fa-toggle-down
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-toggle-left"></span>
                                                        <span class="glyphicon-class"> fa-toggle-left
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-toggle-off"></span>
                                                        <span class="glyphicon-class"> fa-toggle-off
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-toggle-on"></span>
                                                        <span class="glyphicon-class"> fa-toggle-on</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-toggle-right"></span>
                                                        <span class="glyphicon-class"> fa-toggle-right
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-toggle-up"></span>
                                                        <span class="glyphicon-class"> fa-toggle-up
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-trademark"></span>
                                                        <span class="glyphicon-class"> fa-trademark</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-trash"></span>
                                                        <span class="glyphicon-class"> fa-trash</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-trash-o"></span>
                                                        <span class="glyphicon-class"> fa-trash-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tree"></span>
                                                        <span class="glyphicon-class"> fa-tree</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-trophy"></span>
                                                        <span class="glyphicon-class"> fa-trophy</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-truck"></span>
                                                        <span class="glyphicon-class"> fa-truck</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tty"></span>
                                                        <span class="glyphicon-class"> fa-tty</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tv"></span>
                                                        <span class="glyphicon-class"> fa-tv
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-umbrella"></span>
                                                        <span class="glyphicon-class"> fa-umbrella</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-university"></span>
                                                        <span class="glyphicon-class"> fa-university
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-unlock"></span>
                                                        <span class="glyphicon-class"> fa-unlock</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-unlock-alt"></span>
                                                        <span class="glyphicon-class"> fa-unlock-alt
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-unsorted"></span>
                                                        <span class="glyphicon-class"> fa-unsorted
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-upload"></span>
                                                        <span class="glyphicon-class"> fa-upload</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-user"></span>
                                                        <span class="glyphicon-class"> fa-user</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-user-plus"></span>
                                                        <span class="glyphicon-class"> fa-user-plus</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-user-secret"></span>
                                                        <span class="glyphicon-class"> fa-user-secret
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-user-times"></span>
                                                        <span class="glyphicon-class"> fa-user-times
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-users"></span>
                                                        <span class="glyphicon-class"> fa-users</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-video-camera"></span>
                                                        <span class="glyphicon-class"> fa-video-camera
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-volume-down"></span>
                                                        <span class="glyphicon-class"> fa-volume-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-volume-off"></span>
                                                        <span class="glyphicon-class"> fa-volume-off
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-volume-up"></span>
                                                        <span class="glyphicon-class"> fa-volume-up</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-warning"></span>
                                                        <span class="glyphicon-class"> fa-warning
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-wheelchair"></span>
                                                        <span class="glyphicon-class"> fa-wheelchair
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-wifi"></span>
                                                        <span class="glyphicon-class"> fa-wifi</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-wrench"></span>
                                                        <span class="glyphicon-class"> fa-wrench
                                                        </span></li>
                                                </ul>

                                            </section>

                                            <section id="hand" class="tab-pane">


                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-hand-grab-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-grab-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-lizard-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-lizard-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-o-down"></span>
                                                        <span class="glyphicon-class"> fa-hand-o-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-o-left"></span>
                                                        <span class="glyphicon-class"> fa-hand-o-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-o-right"></span>
                                                        <span class="glyphicon-class"> fa-hand-o-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-o-up"></span>
                                                        <span class="glyphicon-class"> fa-hand-o-up</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-paper-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-paper-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-peace-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-peace-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-pointer-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-pointer-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-rock-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-rock-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-scissors-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-scissors-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-spock-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-spock-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-stop-o"></span>
                                                        <span class="glyphicon-class"> fa-hand-stop-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-thumbs-down"></span>
                                                        <span class="glyphicon-class"> fa-thumbs-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-thumbs-o-down"></span>
                                                        <span class="glyphicon-class"> fa-thumbs-o-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-thumbs-o-up"></span>
                                                        <span class="glyphicon-class"> fa-thumbs-o-up
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-thumbs-up"></span>
                                                        <span class="glyphicon-class"> fa-thumbs-up</span>
                                                    </li>
                                                </ul>
                                            </section>

                                            <section id="transportation" class="tab-pane">

                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-ambulance"></span>
                                                        <span class="glyphicon-class"> fa-ambulance</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-automobile"></span>
                                                        <span class="glyphicon-class"> fa-automobile
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bicycle"></span>
                                                        <span class="glyphicon-class"> fa-bicycle</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bus"></span>
                                                        <span class="glyphicon-class"> fa-bus</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cab"></span>
                                                        <span class="glyphicon-class"> fa-cab <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-car"></span>
                                                        <span class="glyphicon-class"> fa-car</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-fighter-jet"></span>
                                                        <span class="glyphicon-class"> fa-fighter-jet
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-motorcycle"></span>
                                                        <span class="glyphicon-class"> fa-motorcycle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-plane"></span>
                                                        <span class="glyphicon-class"> fa-plane</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-rocket"></span>
                                                        <span class="glyphicon-class"> fa-rocket</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-ship"></span>
                                                        <span class="glyphicon-class"> fa-ship</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-space-shuttle"></span>
                                                        <span class="glyphicon-class"> fa-space-shuttle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-subway"></span>
                                                        <span class="glyphicon-class"> fa-subway</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-taxi"></span>
                                                        <span class="glyphicon-class"> fa-taxi</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-train"></span>
                                                        <span class="glyphicon-class"> fa-train</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-truck"></span>
                                                        <span class="glyphicon-class"> fa-truck</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-wheelchair"></span>
                                                        <span class="glyphicon-class"> fa-wheelchair</span>
                                                    </li>
                                                </ul>

                                            </section>

                                            <section id="gender" class="tab-pane">
                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-genderless"></span>
                                                        <span class="glyphicon-class"> fa-genderless
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-intersex"></span>
                                                        <span class="glyphicon-class"> fa-intersex
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mars"></span>
                                                        <span class="glyphicon-class"> fa-mars</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mars-double"></span>
                                                        <span class="glyphicon-class"> fa-mars-double
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mars-stroke"></span>
                                                        <span class="glyphicon-class"> fa-mars-stroke
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mars-stroke-h"></span>
                                                        <span class="glyphicon-class"> fa-mars-stroke-h
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mars-stroke-v"></span>
                                                        <span class="glyphicon-class"> fa-mars-stroke-v
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-mercury"></span>
                                                        <span class="glyphicon-class"> fa-mercury</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-neuter"></span>
                                                        <span class="glyphicon-class"> fa-neuter</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-transgender"></span>
                                                        <span class="glyphicon-class"> fa-transgender
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-transgender-alt"></span>
                                                        <span class="glyphicon-class"> fa-transgender-alt
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-venus"></span>
                                                        <span class="glyphicon-class"> fa-venus</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-venus-double"></span>
                                                        <span class="glyphicon-class"> fa-venus-double
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-venus-mars"></span>
                                                        <span class="glyphicon-class"> fa-venus-mars
                                                        </span>
                                                    </li>
                                                </ul>
                                            </section>

                                            <section id="file-type" class="tab-pane">
                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-file"></span>
                                                        <span class="glyphicon-class"> fa-file</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-archive-o"></span>
                                                        <span class="glyphicon-class"> fa-file-archive-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-audio-o"></span>
                                                        <span class="glyphicon-class"> fa-file-audio-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-code-o"></span>
                                                        <span class="glyphicon-class"> fa-file-code-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-excel-o"></span>
                                                        <span class="glyphicon-class"> fa-file-excel-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-image-o"></span>
                                                        <span class="glyphicon-class"> fa-file-image-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-movie-o"></span>
                                                        <span class="glyphicon-class"> fa-file-movie-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-o"></span>
                                                        <span class="glyphicon-class"> fa-file-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-pdf-o"></span>
                                                        <span class="glyphicon-class"> fa-file-pdf-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-photo-o"></span>
                                                        <span class="glyphicon-class"> fa-file-photo-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-picture-o"></span>
                                                        <span class="glyphicon-class"> fa-file-picture-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-powerpoint-o"></span>
                                                        <span class="glyphicon-class"> fa-file-powerpoint-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-sound-o"></span>
                                                        <span class="glyphicon-class"> fa-file-sound-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-text"></span>
                                                        <span class="glyphicon-class"> fa-file-text</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-text-o"></span>
                                                        <span class="glyphicon-class"> fa-file-text-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-video-o"></span>
                                                        <span class="glyphicon-class"> fa-file-video-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-word-o"></span>
                                                        <span class="glyphicon-class"> fa-file-word-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-zip-o"></span>
                                                        <span class="glyphicon-class"> fa-file-zip-o
                                                            <span class="text-muted">(alias)</span></li>
                                                </ul>

                                            </section>

                                            <section id="spinner" class="tab-pane">

                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-circle-o-notch"></span>
                                                        <span class="glyphicon-class"> fa-circle-o-notch
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cog"></span>
                                                        <span class="glyphicon-class"> fa-cog</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gear"></span>
                                                        <span class="glyphicon-class"> fa-gear <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-refresh"></span>
                                                        <span class="glyphicon-class"> fa-refresh</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-spinner"></span>
                                                        <span class="glyphicon-class"> fa-spinner</span>
                                                    </li>
                                                </ul>
                                            </section>

                                            <section id="form-control" class="tab-pane">
                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-check-square"></span>
                                                        <span class="glyphicon-class"> fa-check-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-check-square-o"></span>
                                                        <span class="glyphicon-class"> fa-check-square-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-circle"></span>
                                                        <span class="glyphicon-class"> fa-circle</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-circle-o"></span>
                                                        <span class="glyphicon-class"> fa-circle-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-dot-circle-o"></span>
                                                        <span class="glyphicon-class"> fa-dot-circle-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-minus-square"></span>
                                                        <span class="glyphicon-class"> fa-minus-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-minus-square-o"></span>
                                                        <span class="glyphicon-class"> fa-minus-square-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-plus-square"></span>
                                                        <span class="glyphicon-class"> fa-plus-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-plus-square-o"></span>
                                                        <span class="glyphicon-class"> fa-plus-square-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-square"></span>
                                                        <span class="glyphicon-class"> fa-square</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-square-o"></span>
                                                        <span class="glyphicon-class"> fa-square-o</span>
                                                    </li>
                                                </ul>
                                            </section>

                                            <section id="payment" class="tab-pane">
                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-cc-amex"></span>
                                                        <span class="glyphicon-class"> fa-cc-amex</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-diners-club"></span>
                                                        <span class="glyphicon-class"> fa-cc-diners-club
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-discover"></span>
                                                        <span class="glyphicon-class"> fa-cc-discover
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-jcb"></span>
                                                        <span class="glyphicon-class"> fa-cc-jcb</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-mastercard"></span>
                                                        <span class="glyphicon-class"> fa-cc-mastercard
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-paypal"></span>
                                                        <span class="glyphicon-class"> fa-cc-paypal</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-stripe"></span>
                                                        <span class="glyphicon-class"> fa-cc-stripe</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-visa"></span>
                                                        <span class="glyphicon-class"> fa-cc-visa</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-credit-card"></span>
                                                        <span class="glyphicon-class"> fa-credit-card
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-google-wallet"></span>
                                                        <span class="glyphicon-class"> fa-google-wallet
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-paypal"></span>
                                                        <span class="glyphicon-class"> fa-paypal</span>
                                                    </li>
                                                </ul>
                                            </section>

                                            <section id="chart" class="tab-pane">
                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-area-chart"></span>
                                                        <span class="glyphicon-class"> fa-area-chart
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bar-chart"></span>
                                                        <span class="glyphicon-class"> fa-bar-chart</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bar-chart-o"></span>
                                                        <span class="glyphicon-class"> fa-bar-chart-o
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-line-chart"></span>
                                                        <span class="glyphicon-class"> fa-line-chart
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pie-chart"></span>
                                                        <span class="glyphicon-class"> fa-pie-chart</span>
                                                    </li>
                                                </ul>
                                            </section>

                                            <section id="currency" class="tab-pane">
                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-bitcoin"></span>
                                                        <span class="glyphicon-class"> fa-bitcoin
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-btc"></span>
                                                        <span class="glyphicon-class"> fa-btc</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cny"></span>
                                                        <span class="glyphicon-class"> fa-cny <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-dollar"></span>
                                                        <span class="glyphicon-class"> fa-dollar
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-eur"></span>
                                                        <span class="glyphicon-class"> fa-eur</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-euro"></span>
                                                        <span class="glyphicon-class"> fa-euro <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gbp"></span>
                                                        <span class="glyphicon-class"> fa-gbp</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gg"></span>
                                                        <span class="glyphicon-class"> fa-gg</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gg-circle"></span>
                                                        <span class="glyphicon-class"> fa-gg-circle</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-ils"></span>
                                                        <span class="glyphicon-class"> fa-ils</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-inr"></span>
                                                        <span class="glyphicon-class"> fa-inr</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-jpy"></span>
                                                        <span class="glyphicon-class"> fa-jpy</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-krw"></span>
                                                        <span class="glyphicon-class"> fa-krw</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-money"></span>
                                                        <span class="glyphicon-class"> fa-money</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-rmb"></span>
                                                        <span class="glyphicon-class"> fa-rmb <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-rouble"></span>
                                                        <span class="glyphicon-class"> fa-rouble
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-rub"></span>
                                                        <span class="glyphicon-class"> fa-rub</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-ruble"></span>
                                                        <span class="glyphicon-class"> fa-ruble <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-rupee"></span>
                                                        <span class="glyphicon-class"> fa-rupee <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-shekel"></span>
                                                        <span class="glyphicon-class"> fa-shekel
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sheqel"></span>
                                                        <span class="glyphicon-class"> fa-sheqel
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-try"></span>
                                                        <span class="glyphicon-class"> fa-try</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-turkish-lira"></span>
                                                        <span class="glyphicon-class"> fa-turkish-lira
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-usd"></span>
                                                        <span class="glyphicon-class"> fa-usd</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-won"></span>
                                                        <span class="glyphicon-class"> fa-won <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-yen"></span>
                                                        <span class="glyphicon-class"> fa-yen</span>
                                                    </li>
                                                </ul>
                                            </section>

                                            <section id="text-editor" class="tab-pane">

                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-align-center"></span>
                                                        <span class="glyphicon-class"> fa-align-center
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-align-justify"></span>
                                                        <span class="glyphicon-class"> fa-align-justify
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-align-left"></span>
                                                        <span class="glyphicon-class"> fa-align-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-align-right"></span>
                                                        <span class="glyphicon-class"> fa-align-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bold"></span>
                                                        <span class="glyphicon-class"> fa-bold</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chain"></span>
                                                        <span class="glyphicon-class"> fa-chain <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chain-broken"></span>
                                                        <span class="glyphicon-class"> fa-chain-broken
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-clipboard"></span>
                                                        <span class="glyphicon-class"> fa-clipboard</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-columns"></span>
                                                        <span class="glyphicon-class"> fa-columns</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-copy"></span>
                                                        <span class="glyphicon-class"> fa-copy <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cut"></span>
                                                        <span class="glyphicon-class"> fa-cut <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-dedent"></span>
                                                        <span class="glyphicon-class"> fa-dedent
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-eraser"></span>
                                                        <span class="glyphicon-class"> fa-eraser</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file"></span>
                                                        <span class="glyphicon-class"> fa-file</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-o"></span>
                                                        <span class="glyphicon-class"> fa-file-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-text"></span>
                                                        <span class="glyphicon-class"> fa-file-text</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-file-text-o"></span>
                                                        <span class="glyphicon-class"> fa-file-text-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-files-o"></span>
                                                        <span class="glyphicon-class"> fa-files-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-floppy-o"></span>
                                                        <span class="glyphicon-class"> fa-floppy-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-font"></span>
                                                        <span class="glyphicon-class"> fa-font</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-header"></span>
                                                        <span class="glyphicon-class"> fa-header</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-indent"></span>
                                                        <span class="glyphicon-class"> fa-indent</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-italic"></span>
                                                        <span class="glyphicon-class"> fa-italic</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-link"></span>
                                                        <span class="glyphicon-class"> fa-link</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-list"></span>
                                                        <span class="glyphicon-class"> fa-list</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-list-alt"></span>
                                                        <span class="glyphicon-class"> fa-list-alt</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-list-ol"></span>
                                                        <span class="glyphicon-class"> fa-list-ol</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-list-ul"></span>
                                                        <span class="glyphicon-class"> fa-list-ul</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-outdent"></span>
                                                        <span class="glyphicon-class"> fa-outdent</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-paperclip"></span>
                                                        <span class="glyphicon-class"> fa-paperclip</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-paragraph"></span>
                                                        <span class="glyphicon-class"> fa-paragraph</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-paste"></span>
                                                        <span class="glyphicon-class"> fa-paste <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-repeat"></span>
                                                        <span class="glyphicon-class"> fa-repeat</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-rotate-left"></span>
                                                        <span class="glyphicon-class"> fa-rotate-left
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-rotate-right"></span>
                                                        <span class="glyphicon-class"> fa-rotate-right
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-save"></span>
                                                        <span class="glyphicon-class"> fa-save <span class="text-muted">(alias)</span>
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-scissors"></span>
                                                        <span class="glyphicon-class"> fa-scissors</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-strikethrough"></span>
                                                        <span class="glyphicon-class"> fa-strikethrough
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-subscript"></span>
                                                        <span class="glyphicon-class"> fa-subscript</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-superscript"></span>
                                                        <span class="glyphicon-class"> fa-superscript
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-table"></span>
                                                        <span class="glyphicon-class"> fa-table</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-text-height"></span>
                                                        <span class="glyphicon-class"> fa-text-height
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-text-width"></span>
                                                        <span class="glyphicon-class"> fa-text-width
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-th"></span>
                                                        <span class="glyphicon-class"> fa-th</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-th-large"></span>
                                                        <span class="glyphicon-class"> fa-th-large</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-th-list"></span>
                                                        <span class="glyphicon-class"> fa-th-list</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-underline"></span>
                                                        <span class="glyphicon-class"> fa-underline</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-undo"></span>
                                                        <span class="glyphicon-class"> fa-undo</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-unlink"></span>
                                                        <span class="glyphicon-class"> fa-unlink</span>
                                                    </li>
                                                </ul>
                                            </section>

                                            <section id="directional" class="tab-pane">
                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-angle-double-down"></span>
                                                        <span class="glyphicon-class"> fa-angle-double-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-angle-double-left"></span>
                                                        <span class="glyphicon-class"> fa-angle-double-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-angle-double-right"></span>
                                                        <span class="glyphicon-class"> fa-angle-double-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-angle-double-up"></span>
                                                        <span class="glyphicon-class"> fa-angle-double-up
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-angle-down"></span>
                                                        <span class="glyphicon-class"> fa-angle-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-angle-left"></span>
                                                        <span class="glyphicon-class"> fa-angle-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-angle-right"></span>
                                                        <span class="glyphicon-class"> fa-angle-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-angle-up"></span>
                                                        <span class="glyphicon-class"> fa-angle-up</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-circle-down"></span>
                                                        <span class="glyphicon-class"> fa-arrow-circle-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-circle-left"></span>
                                                        <span class="glyphicon-class"> fa-arrow-circle-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-circle-o-down"></span>
                                                        <span class="glyphicon-class"> fa-arrow-circle-o-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-circle-o-left"></span>
                                                        <span class="glyphicon-class"> fa-arrow-circle-o-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-circle-o-right"></span>
                                                        <span class="glyphicon-class"> fa-arrow-circle-o-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-circle-o-up"></span>
                                                        <span class="glyphicon-class"> fa-arrow-circle-o-up
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-circle-right"></span>
                                                        <span class="glyphicon-class"> fa-arrow-circle-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-circle-up"></span>
                                                        <span class="glyphicon-class"> fa-arrow-circle-up
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-down"></span>
                                                        <span class="glyphicon-class"> fa-arrow-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-left"></span>
                                                        <span class="glyphicon-class"> fa-arrow-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-right"></span>
                                                        <span class="glyphicon-class"> fa-arrow-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrow-up"></span>
                                                        <span class="glyphicon-class"> fa-arrow-up</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrows"></span>
                                                        <span class="glyphicon-class"> fa-arrows</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrows-alt"></span>
                                                        <span class="glyphicon-class"> fa-arrows-alt
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrows-h"></span>
                                                        <span class="glyphicon-class"> fa-arrows-h</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-arrows-v"></span>
                                                        <span class="glyphicon-class"> fa-arrows-v</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-down"></span>
                                                        <span class="glyphicon-class"> fa-caret-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-left"></span>
                                                        <span class="glyphicon-class"> fa-caret-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-right"></span>
                                                        <span class="glyphicon-class"> fa-caret-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-square-o-down"></span>
                                                        <span class="glyphicon-class"> fa-caret-square-o-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-square-o-left"></span>
                                                        <span class="glyphicon-class"> fa-caret-square-o-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-square-o-right"></span>
                                                        <span class="glyphicon-class"> fa-caret-square-o-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-square-o-up"></span>
                                                        <span class="glyphicon-class"> fa-caret-square-o-up
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-caret-up"></span>
                                                        <span class="glyphicon-class"> fa-caret-up</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chevron-circle-down"></span>
                                                        <span class="glyphicon-class"> fa-chevron-circle-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chevron-circle-left"></span>
                                                        <span class="glyphicon-class"> fa-chevron-circle-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chevron-circle-right"></span>
                                                        <span class="glyphicon-class"> fa-chevron-circle-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chevron-circle-up"></span>
                                                        <span class="glyphicon-class"> fa-chevron-circle-up
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chevron-down"></span>
                                                        <span class="glyphicon-class"> fa-chevron-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chevron-left"></span>
                                                        <span class="glyphicon-class"> fa-chevron-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chevron-right"></span>
                                                        <span class="glyphicon-class"> fa-chevron-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chevron-up"></span>
                                                        <span class="glyphicon-class"> fa-chevron-up
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-exchange"></span>
                                                        <span class="glyphicon-class"> fa-exchange</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-o-down"></span>
                                                        <span class="glyphicon-class"> fa-hand-o-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-o-left"></span>
                                                        <span class="glyphicon-class"> fa-hand-o-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-o-right"></span>
                                                        <span class="glyphicon-class"> fa-hand-o-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hand-o-up"></span>
                                                        <span class="glyphicon-class"> fa-hand-o-up</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-long-arrow-down"></span>
                                                        <span class="glyphicon-class"> fa-long-arrow-down
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-long-arrow-left"></span>
                                                        <span class="glyphicon-class"> fa-long-arrow-left
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-long-arrow-right"></span>
                                                        <span class="glyphicon-class"> fa-long-arrow-right
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-long-arrow-up"></span>
                                                        <span class="glyphicon-class"> fa-long-arrow-up
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-toggle-down"></span>
                                                        <span class="glyphicon-class"> fa-toggle-down
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-toggle-left"></span>
                                                        <span class="glyphicon-class"> fa-toggle-left
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-toggle-right"></span>
                                                        <span class="glyphicon-class"> fa-toggle-right
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-toggle-up"></span>
                                                        <span class="glyphicon-class"> fa-toggle-up</span>
                                                    </li>
                                                </ul>
                                            </section>

                                            <section id="video-player" class="tab-pane">
                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-arrows-alt"></span>
                                                        <span class="glyphicon-class"> fa-arrows-alt
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-backward"></span>
                                                        <span class="glyphicon-class"> fa-backward</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-compress"></span>
                                                        <span class="glyphicon-class"> fa-compress</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-eject"></span>
                                                        <span class="glyphicon-class"> fa-eject</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-expand"></span>
                                                        <span class="glyphicon-class"> fa-expand</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-fast-backward"></span>
                                                        <span class="glyphicon-class"> fa-fast-backward
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-fast-forward"></span>
                                                        <span class="glyphicon-class"> fa-fast-forward
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-forward"></span>
                                                        <span class="glyphicon-class"> fa-forward</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pause"></span>
                                                        <span class="glyphicon-class"> fa-pause</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-play"></span>
                                                        <span class="glyphicon-class"> fa-play</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-play-circle"></span>
                                                        <span class="glyphicon-class"> fa-play-circle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-play-circle-o"></span>
                                                        <span class="glyphicon-class"> fa-play-circle-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-random"></span>
                                                        <span class="glyphicon-class"> fa-random</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-step-backward"></span>
                                                        <span class="glyphicon-class"> fa-step-backward
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-step-forward"></span>
                                                        <span class="glyphicon-class"> fa-step-forward
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-stop"></span>
                                                        <span class="glyphicon-class"> fa-stop</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-youtube-play"></span>
                                                        <span class="glyphicon-class"> fa-youtube-play
                                                        </span>
                                                    </li>
                                                </ul>
                                            </section>

                                            <section id="brand" class="tab-pane">
                                                <ul class="bs-glyphicons">
                                                    <li><span class="fa fa-fw fa-500px"></span>
                                                        <span class="glyphicon-class"> fa-500px</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-adn"></span>
                                                        <span class="glyphicon-class"> fa-adn</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-amazon"></span>
                                                        <span class="glyphicon-class"> fa-amazon</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-android"></span>
                                                        <span class="glyphicon-class"> fa-android</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-angellist"></span>
                                                        <span class="glyphicon-class"> fa-angellist</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-apple"></span>
                                                        <span class="glyphicon-class"> fa-apple</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-behance"></span>
                                                        <span class="glyphicon-class"> fa-behance</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-behance-square"></span>
                                                        <span class="glyphicon-class"> fa-behance-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bitbucket"></span>
                                                        <span class="glyphicon-class"> fa-bitbucket</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bitbucket-square"></span>
                                                        <span class="glyphicon-class"> fa-bitbucket-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-bitcoin"></span>
                                                        <span class="glyphicon-class"> fa-bitcoin
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-black-tie"></span>
                                                        <span class="glyphicon-class"> fa-black-tie</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-btc"></span>
                                                        <span class="glyphicon-class"> fa-btc</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-buysellads"></span>
                                                        <span class="glyphicon-class"> fa-buysellads
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-amex"></span>
                                                        <span class="glyphicon-class"> fa-cc-amex</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-diners-club"></span>
                                                        <span class="glyphicon-class"> fa-cc-diners-club
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-discover"></span>
                                                        <span class="glyphicon-class"> fa-cc-discover
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-jcb"></span>
                                                        <span class="glyphicon-class"> fa-cc-jcb</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-mastercard"></span>
                                                        <span class="glyphicon-class"> fa-cc-mastercard
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-paypal"></span>
                                                        <span class="glyphicon-class"> fa-cc-paypal</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-stripe"></span>
                                                        <span class="glyphicon-class"> fa-cc-stripe</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-cc-visa"></span>
                                                        <span class="glyphicon-class"> fa-cc-visa</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-chrome"></span>
                                                        <span class="glyphicon-class"> fa-chrome</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-codepen"></span>
                                                        <span class="glyphicon-class"> fa-codepen</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-connectdevelop"></span>
                                                        <span class="glyphicon-class"> fa-connectdevelop
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-contao"></span>
                                                        <span class="glyphicon-class"> fa-contao</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-css3"></span>
                                                        <span class="glyphicon-class"> fa-css3</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-dashcube"></span>
                                                        <span class="glyphicon-class"> fa-dashcube</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-delicious"></span>
                                                        <span class="glyphicon-class"> fa-delicious</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-deviantart"></span>
                                                        <span class="glyphicon-class"> fa-deviantart
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-digg"></span>
                                                        <span class="glyphicon-class"> fa-digg</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-dribbble"></span>
                                                        <span class="glyphicon-class"> fa-dribbble</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-dropbox"></span>
                                                        <span class="glyphicon-class"> fa-dropbox</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-drupal"></span>
                                                        <span class="glyphicon-class"> fa-drupal</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-empire"></span>
                                                        <span class="glyphicon-class"> fa-empire</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-expeditedssl"></span>
                                                        <span class="glyphicon-class"> fa-expeditedssl
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-facebook"></span>
                                                        <span class="glyphicon-class"> fa-facebook</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-facebook-f"></span>
                                                        <span class="glyphicon-class"> fa-facebook-f
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-facebook-official"></span>
                                                        <span class="glyphicon-class"> fa-facebook-official
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-facebook-square"></span>
                                                        <span class="glyphicon-class"> fa-facebook-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-firefox"></span>
                                                        <span class="glyphicon-class"> fa-firefox</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-flickr"></span>
                                                        <span class="glyphicon-class"> fa-flickr</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-fonticons"></span>
                                                        <span class="glyphicon-class"> fa-fonticons</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-forumbee"></span>
                                                        <span class="glyphicon-class"> fa-forumbee</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-foursquare"></span>
                                                        <span class="glyphicon-class"> fa-foursquare
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-ge"></span>
                                                        <span class="glyphicon-class"> fa-ge
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-get-pocket"></span>
                                                        <span class="glyphicon-class"> fa-get-pocket
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gg"></span>
                                                        <span class="glyphicon-class"> fa-gg</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gg-circle"></span>
                                                        <span class="glyphicon-class"> fa-gg-circle</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-git"></span>
                                                        <span class="glyphicon-class"> fa-git</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-git-square"></span>
                                                        <span class="glyphicon-class"> fa-git-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-github"></span>
                                                        <span class="glyphicon-class"> fa-github</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-github-alt"></span>
                                                        <span class="glyphicon-class"> fa-github-alt
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-github-square"></span>
                                                        <span class="glyphicon-class"> fa-github-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gittip"></span>
                                                        <span class="glyphicon-class"> fa-gittip
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-google"></span>
                                                        <span class="glyphicon-class"> fa-google</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-google-plus"></span>
                                                        <span class="glyphicon-class"> fa-google-plus
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-google-plus-square"></span>
                                                        <span class="glyphicon-class"> fa-google-plus-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-google-wallet"></span>
                                                        <span class="glyphicon-class"> fa-google-wallet
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-gratipay"></span>
                                                        <span class="glyphicon-class"> fa-gratipay</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hacker-news"></span>
                                                        <span class="glyphicon-class"> fa-hacker-news
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-houzz"></span>
                                                        <span class="glyphicon-class"> fa-houzz</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-html5"></span>
                                                        <span class="glyphicon-class"> fa-html5</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-instagram"></span>
                                                        <span class="glyphicon-class"> fa-instagram</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-internet-explorer"></span>
                                                        <span class="glyphicon-class"> fa-internet-explorer
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-ioxhost"></span>
                                                        <span class="glyphicon-class"> fa-ioxhost</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-joomla"></span>
                                                        <span class="glyphicon-class"> fa-joomla</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-jsfiddle"></span>
                                                        <span class="glyphicon-class"> fa-jsfiddle</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-lastfm"></span>
                                                        <span class="glyphicon-class"> fa-lastfm</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-lastfm-square"></span>
                                                        <span class="glyphicon-class"> fa-lastfm-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-leanpub"></span>
                                                        <span class="glyphicon-class"> fa-leanpub</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-linkedin"></span>
                                                        <span class="glyphicon-class"> fa-linkedin</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-linkedin-square"></span>
                                                        <span class="glyphicon-class"> fa-linkedin-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-linux"></span>
                                                        <span class="glyphicon-class"> fa-linux</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-maxcdn"></span>
                                                        <span class="glyphicon-class"> fa-maxcdn</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-meanpath"></span>
                                                        <span class="glyphicon-class"> fa-meanpath</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-medium"></span>
                                                        <span class="glyphicon-class"> fa-medium</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-odnoklassniki"></span>
                                                        <span class="glyphicon-class"> fa-odnoklassniki
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-odnoklassniki-square"></span>
                                                        <span class="glyphicon-class"> fa-odnoklassniki-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-opencart"></span>
                                                        <span class="glyphicon-class"> fa-opencart</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-openid"></span>
                                                        <span class="glyphicon-class"> fa-openid</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-opera"></span>
                                                        <span class="glyphicon-class"> fa-opera</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-optin-monster"></span>
                                                        <span class="glyphicon-class"> fa-optin-monster
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pagelines"></span>
                                                        <span class="glyphicon-class"> fa-pagelines</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-paypal"></span>
                                                        <span class="glyphicon-class"> fa-paypal</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pied-piper"></span>
                                                        <span class="glyphicon-class"> fa-pied-piper
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pied-piper-alt"></span>
                                                        <span class="glyphicon-class"> fa-pied-piper-alt
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pinterest"></span>
                                                        <span class="glyphicon-class"> fa-pinterest</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pinterest-p"></span>
                                                        <span class="glyphicon-class"> fa-pinterest-p
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-pinterest-square"></span>
                                                        <span class="glyphicon-class"> fa-pinterest-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-qq"></span>
                                                        <span class="glyphicon-class"> fa-qq</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-ra"></span>
                                                        <span class="glyphicon-class"> fa-ra
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-rebel"></span>
                                                        <span class="glyphicon-class"> fa-rebel</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-reddit"></span>
                                                        <span class="glyphicon-class"> fa-reddit</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-reddit-square"></span>
                                                        <span class="glyphicon-class"> fa-reddit-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-renren"></span>
                                                        <span class="glyphicon-class"> fa-renren</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-safari"></span>
                                                        <span class="glyphicon-class"> fa-safari</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-sellsy"></span>
                                                        <span class="glyphicon-class"> fa-sellsy</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-share-alt"></span>
                                                        <span class="glyphicon-class"> fa-share-alt</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-share-alt-square"></span>
                                                        <span class="glyphicon-class"> fa-share-alt-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-shirtsinbulk"></span>
                                                        <span class="glyphicon-class"> fa-shirtsinbulk
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-simplybuilt"></span>
                                                        <span class="glyphicon-class"> fa-simplybuilt
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-skyatlas"></span>
                                                        <span class="glyphicon-class"> fa-skyatlas</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-skype"></span>
                                                        <span class="glyphicon-class"> fa-skype</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-slack"></span>
                                                        <span class="glyphicon-class"> fa-slack</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-slideshare"></span>
                                                        <span class="glyphicon-class"> fa-slideshare
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-soundcloud"></span>
                                                        <span class="glyphicon-class"> fa-soundcloud
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-spotify"></span>
                                                        <span class="glyphicon-class"> fa-spotify</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-stack-exchange"></span>
                                                        <span class="glyphicon-class"> fa-stack-exchange
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-stack-overflow"></span>
                                                        <span class="glyphicon-class"> fa-stack-overflow
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-steam"></span>
                                                        <span class="glyphicon-class"> fa-steam</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-steam-square"></span>
                                                        <span class="glyphicon-class"> fa-steam-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-stumbleupon"></span>
                                                        <span class="glyphicon-class"> fa-stumbleupon
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-stumbleupon-circle"></span>
                                                        <span class="glyphicon-class"> fa-stumbleupon-circle
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tencent-weibo"></span>
                                                        <span class="glyphicon-class"> fa-tencent-weibo
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-trello"></span>
                                                        <span class="glyphicon-class"> fa-trello</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tripadvisor"></span>
                                                        <span class="glyphicon-class"> fa-tripadvisor
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tumblr"></span>
                                                        <span class="glyphicon-class"> fa-tumblr</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-tumblr-square"></span>
                                                        <span class="glyphicon-class"> fa-tumblr-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-twitch"></span>
                                                        <span class="glyphicon-class"> fa-twitch</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-twitter"></span>
                                                        <span class="glyphicon-class"> fa-twitter</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-twitter-square"></span>
                                                        <span class="glyphicon-class"> fa-twitter-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-viacoin"></span>
                                                        <span class="glyphicon-class"> fa-viacoin</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-vimeo"></span>
                                                        <span class="glyphicon-class"> fa-vimeo</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-vimeo-square"></span>
                                                        <span class="glyphicon-class"> fa-vimeo-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-vine"></span>
                                                        <span class="glyphicon-class"> fa-vine</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-vk"></span>
                                                        <span class="glyphicon-class"> fa-vk</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-wechat"></span>
                                                        <span class="glyphicon-class"> fa-wechat
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-weibo"></span>
                                                        <span class="glyphicon-class"> fa-weibo</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-weixin"></span>
                                                        <span class="glyphicon-class"> fa-weixin</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-whatsapp"></span>
                                                        <span class="glyphicon-class"> fa-whatsapp</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-wikipedia-w"></span>
                                                        <span class="glyphicon-class"> fa-wikipedia-w
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-windows"></span>
                                                        <span class="glyphicon-class"> fa-windows</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-wordpress"></span>
                                                        <span class="glyphicon-class"> fa-wordpress</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-xing"></span>
                                                        <span class="glyphicon-class"> fa-xing</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-xing-square"></span>
                                                        <span class="glyphicon-class"> fa-xing-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-y-combinator"></span>
                                                        <span class="glyphicon-class"> fa-y-combinator
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-y-combinator-square"></span>
                                                        <span class="glyphicon-class"> fa-y-combinator-square <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-yahoo"></span>
                                                        <span class="glyphicon-class"> fa-yahoo</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-yc"></span>
                                                        <span class="glyphicon-class"> fa-yc
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-yc-square"></span>
                                                        <span class="glyphicon-class"> fa-yc-square
                                                            <span class="text-muted">(alias)</span></span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-yelp"></span>
                                                        <span class="glyphicon-class"> fa-yelp</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-youtube"></span>
                                                        <span class="glyphicon-class"> fa-youtube</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-youtube-play"></span>
                                                        <span class="glyphicon-class"> fa-youtube-play
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-youtube-square"></span>
                                                        <span class="glyphicon-class"> fa-youtube-square
                                                        </span>
                                                    </li>
                                                </ul>
                                            </section>

                                            <section id="medical" class="tab-pane">
                                                <ul class="bs-glyphicons">

                                                    <li><span class="fa fa-fw fa-ambulance"></span>
                                                        <span class="glyphicon-class"> fa-ambulance</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-h-square"></span>
                                                        <span class="glyphicon-class"> fa-h-square</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-heart"></span>
                                                        <span class="glyphicon-class"> fa-heart</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-heart-o"></span>
                                                        <span class="glyphicon-class"> fa-heart-o</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-heartbeat"></span>
                                                        <span class="glyphicon-class"> fa-heartbeat</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-hospital-o"></span>
                                                        <span class="glyphicon-class"> fa-hospital-o
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-medkit"></span>
                                                        <span class="glyphicon-class"> fa-medkit</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-plus-square"></span>
                                                        <span class="glyphicon-class"> fa-plus-square
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-stethoscope"></span>
                                                        <span class="glyphicon-class"> fa-stethoscope
                                                        </span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-user-md"></span>
                                                        <span class="glyphicon-class"> fa-user-md</span>
                                                    </li>
                                                    <li><span class="fa fa-fw fa-wheelchair"></span>
                                                        <span class="glyphicon-class"> fa-wheelchair
                                                        </span>
                                                    </li>
                                                </ul>

                                            </section>
                                    <!-- /#fa-icons -->

                                    <!-- glyphicons-->
                                    <div class="tab-pane" id="glyphicons">
                                        <ul class="bs-glyphicons">
                                            <li>
                                                <span class="glyphicon glyphicon-asterisk"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-asterisk</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-plus"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-plus</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-euro"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-euro</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-eur"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-eur</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-minus"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-minus</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-cloud"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-cloud</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-envelope"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-envelope</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-pencil"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-pencil</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-glass"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-glass</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-music"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-music</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-search"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-search</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-heart"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-heart</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-star"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-star</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-star-empty"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-star-empty</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-user"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-user</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-film"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-film</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-th-large"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-th-large</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-th"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-th</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-th-list"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-th-list</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-ok"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-ok</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-remove"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-remove</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-zoom-in"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-zoom-in</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-zoom-out"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-zoom-out</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-off"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-off</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-signal"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-signal</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-cog"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-cog</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-trash"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-trash</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-home"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-home</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-file"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-file</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-time"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-time</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-road"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-road</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-download-alt"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-download-alt</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-download"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-download</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-upload"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-upload</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-inbox"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-inbox</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-play-circle"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-play-circle</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-repeat"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-repeat</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-refresh"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-refresh</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-list-alt"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-list-alt</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-lock"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-lock</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-flag"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-flag</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-headphones"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-headphones</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-volume-off"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-volume-off</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-volume-down"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-volume-down</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-volume-up"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-volume-up</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-qrcode"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-qrcode</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-barcode"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-barcode</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-tag"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-tag</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-tags"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-tags</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-book"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-book</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-bookmark"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-bookmark</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-print"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-print</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-camera"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-camera</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-font"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-font</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-bold"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-bold</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-italic"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-italic</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-text-height"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-text-height</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-text-width"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-text-width</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-align-left"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-align-left</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-align-center"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-align-center</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-align-right"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-align-right</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-align-justify"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-align-justify</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-list"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-list</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-indent-left"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-indent-left</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-indent-right"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-indent-right</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-facetime-video"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-facetime-video</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-picture"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-picture</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-map-marker"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-map-marker</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-adjust"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-adjust</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-tint"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-tint</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-edit"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-edit</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-share"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-share</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-check"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-check</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-move"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-move</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-step-backward"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-step-backward</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-fast-backward"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-fast-backward</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-backward"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-backward</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-play"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-play</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-pause"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-pause</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-stop"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-stop</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-forward"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-forward</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-fast-forward"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-fast-forward</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-step-forward"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-step-forward</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-eject"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-eject</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-chevron-left"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-chevron-left</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-chevron-right"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-chevron-right</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-plus-sign"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-plus-sign</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-minus-sign"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-minus-sign</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-remove-sign"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-remove-sign</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-ok-sign"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-ok-sign</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-question-sign"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-question-sign</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-info-sign"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-info-sign</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-screenshot"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-screenshot</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-remove-circle"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-remove-circle</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-ok-circle"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-ok-circle</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-ban-circle"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-ban-circle</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-arrow-left"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-arrow-left</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-arrow-right"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-arrow-right</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-arrow-up"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-arrow-up</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-arrow-down"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-arrow-down</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-share-alt"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-share-alt</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-resize-full"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-resize-full</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-resize-small"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-resize-small</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-exclamation-sign"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-exclamation-sign</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-gift"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-gift</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-leaf"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-leaf</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-fire"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-fire</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-eye-open"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-eye-open</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-eye-close"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-eye-close</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-warning-sign"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-warning-sign</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-plane"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-plane</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-calendar"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-calendar</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-random"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-random</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-comment"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-comment</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-magnet"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-magnet</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-chevron-up"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-chevron-up</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-chevron-down"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-chevron-down</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-retweet"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-retweet</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-shopping-cart"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-shopping-cart</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-folder-close"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-folder-close</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-folder-open"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-folder-open</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-resize-vertical"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-resize-vertical</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-resize-horizontal"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-resize-horizontal</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-hdd"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-hdd</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-bullhorn"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-bullhorn</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-bell"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-bell</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-certificate"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-certificate</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-thumbs-up"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-thumbs-up</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-thumbs-down"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-thumbs-down</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-hand-right"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-hand-right</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-hand-left"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-hand-left</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-hand-up"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-hand-up</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-hand-down"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-hand-down</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-circle-arrow-right"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-circle-arrow-right</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-circle-arrow-left"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-circle-arrow-left</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-circle-arrow-up"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-circle-arrow-up</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-circle-arrow-down"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-circle-arrow-down</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-globe"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-globe</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-wrench"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-wrench</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-tasks"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-tasks</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-filter"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-filter</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-briefcase"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-briefcase</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-fullscreen"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-fullscreen</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-dashboard"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-dashboard</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-paperclip"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-paperclip</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-heart-empty"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-heart-empty</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-link"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-link</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-phone"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-phone</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-pushpin"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-pushpin</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-usd"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-usd</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-gbp"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-gbp</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sort"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sort</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sort-by-alphabet"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sort-by-alphabet</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sort-by-alphabet-alt"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sort-by-alphabet-alt</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sort-by-order"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sort-by-order</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sort-by-order-alt"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sort-by-order-alt</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sort-by-attributes"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sort-by-attributes</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sort-by-attributes-alt"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sort-by-attributes-alt</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-unchecked"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-unchecked</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-expand"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-expand</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-collapse-down"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-collapse-down</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-collapse-up"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-collapse-up</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-log-in"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-log-in</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-flash"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-flash</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-log-out"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-log-out</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-new-window"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-new-window</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-record"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-record</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-save"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-save</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-open"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-open</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-saved"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-saved</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-import"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-import</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-export"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-export</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-send"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-send</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-floppy-disk"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-floppy-disk</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-floppy-saved"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-floppy-saved</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-floppy-remove"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-floppy-remove</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-floppy-save"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-floppy-save</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-floppy-open"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-floppy-open</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-credit-card"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-credit-card</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-transfer"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-transfer</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-cutlery"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-cutlery</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-header"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-header</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-compressed"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-compressed</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-earphone"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-earphone</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-phone-alt"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-phone-alt</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-tower"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-tower</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-stats"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-stats</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sd-video"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sd-video</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-hd-video"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-hd-video</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-subtitles"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-subtitles</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sound-stereo"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sound-stereo</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sound-dolby"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sound-dolby</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sound-5-1"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sound-5-1</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sound-6-1"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sound-6-1</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sound-7-1"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sound-7-1</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-copyright-mark"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-copyright-mark</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-registration-mark"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-registration-mark</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-cloud-download"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-cloud-download</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-cloud-upload"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-cloud-upload</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-tree-conifer"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-tree-conifer</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-tree-deciduous"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-tree-deciduous</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-cd"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-cd</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-save-file"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-save-file</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-open-file"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-open-file</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-level-up"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-level-up</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-copy"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-copy</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-paste"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-paste</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-alert"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-alert</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-equalizer"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-equalizer</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-king"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-king</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-queen"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-queen</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-pawn"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-pawn</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-bishop"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-bishop</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-knight"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-knight</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-baby-formula"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-baby-formula</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-tent"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-tent</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-blackboard"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-blackboard</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-bed"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-bed</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-apple"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-apple</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-erase"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-erase</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-hourglass"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-hourglass</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-lamp"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-lamp</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-duplicate"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-duplicate</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-piggy-bank"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-piggy-bank</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-scissors"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-scissors</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-bitcoin"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-bitcoin</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-btc"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-btc</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-xbt"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-xbt</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-yen"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-yen</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-jpy"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-jpy</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-ruble"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-ruble</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-rub"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-rub</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-scale"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-scale</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-ice-lolly"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-ice-lolly</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-ice-lolly-tasted"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-ice-lolly-tasted</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-education"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-education</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-option-horizontal"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-option-horizontal</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-option-vertical"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-option-vertical</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-menu-hamburger"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-menu-hamburger</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-modal-window"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-modal-window</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-oil"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-oil</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-grain"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-grain</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-sunglasses"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-sunglasses</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-text-size"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-text-size</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-text-color"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-text-color</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-text-background"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-text-background</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-object-align-top"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-object-align-top</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-object-align-bottom"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-object-align-bottom</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-object-align-horizontal"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-object-align-horizontal</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-object-align-left"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-object-align-left</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-object-align-vertical"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-object-align-vertical</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-object-align-right"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-object-align-right</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-triangle-right"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-triangle-right</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-triangle-left"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-triangle-left</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-triangle-bottom"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-triangle-bottom</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-triangle-top"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-triangle-top</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-console"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-console</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-superscript"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-superscript</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-subscript"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-subscript</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-menu-left"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-menu-left</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-menu-right"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-menu-right</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-menu-down"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-menu-down</span>
                                            </li>
                                            <li>
                                                <span class="glyphicon glyphicon-menu-up"></span>
                                                <span class="glyphicon-class">glyphicon glyphicon-menu-up</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- /#ion-icons -->

                                </div>
                                <!-- /.tab-content -->
                            </div>
        
        <?php Modal::end(); ?>

<?php JSRegister::begin([
        'id' => 'icons',
    ]);
?>
<script>
    $('.bs-glyphicons').on('click', 'li', function() {
        console.log($(this).find('span').attr('class'))
        $("#iconinput").val($(this).find('span').attr('class'))
        var cl = $(this).find('span').attr('class')
        $('#iconyl').html("<i class='"+cl+"'></i>")
        $('#dqxztb').html("<i class='"+cl+"'></i>")
        
        $("#w1").modal({
                'show': false
        }) 
    });
</script>

<?php JSRegister::end();
?>