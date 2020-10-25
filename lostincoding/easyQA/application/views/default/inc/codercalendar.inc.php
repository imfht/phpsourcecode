<?php

/**
 * view原作者：http://sandbox.runjs.cn/show/ydp3it7b/
 */

?><div class="codercalendar">
    <div class="title">程序员老黄历<sup>beta</sup></div>
    <div class="date">今天是<?=$codercalendar['today']?></div>
    <div class="good">
        <div class="title">
            <table>
                <tbody>
                    <tr>
                        <td>宜</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="calendar_content">
            <ul>
                <?php foreach ($codercalendar['good_lists'] as $_v): ?>
                    <li>
                        <div class="name"><?=$_v[0]?></div>
                        <div class="description"><?=$_v[1]?></div>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="split"></div>
    <div class="bad">
        <div class="title">
            <table>
                <tbody>
                    <tr>
                        <td>不宜</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="calendar_content">
            <ul>
                <?php foreach ($codercalendar['bad_lists'] as $_v): ?>
                    <li>
                        <div class="name"><?=$_v[0]?></div>
                        <div class="description"><?=$_v[1]?></div>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="split"></div>
    <div class="line-tip">
        <strong>座位朝向：</strong>
        面向<span class="direction_value">「<?=$codercalendar['direction']?>」</span>写程序，bug最少
    </div>
    <div class="line-tip">
        <strong>今日宜饮：</strong>
        <span class="drink_value"><?=implode('、', $codercalendar['todayDrink_lists'])?></span>
    </div>
    <div class="line-tip">
        <strong>女神亲近指数：</strong><span class="goddes_value"><?=$codercalendar['star']?></span>
    </div>
</div>