<?php
/**
 * 幻灯片插件配置文件
 * @author JYmusic
 */

	return array(
		'height'=>array(
			'title'=>'幻灯片高度',
			'type'=>'text',
			'value'=>'300px',
			'tip'=>'如果设置像素加上单位-PX'
		),
		'width'=>array(
			'title'=>'幻灯片宽度',
			'type'=>'text',
			'value'=>'100%',
			'tip'=>'如果设置像素加上单位-PX'
		),
		'Speed'=>array(
			'title'=>'切换速度',
			'type'=>'text',
			'value'=>'3000',
			'tip'=>'自动播放速度单位毫秒'
		),
		'animationTime'=>array(
			'title'=>'效果延时',
			'type'=>'text',
			'value'=>'3000',
			'tip'=>'动画淡入淡出效果延时'
		),
		'animation'=>array(
			'title'=>'效果延时',
			'type'=>'text',
			'value'=>'slide',
			'tip'=>'"fade" or "slide"图片变换方式：淡入淡出或者滑动'
		),
		'slideshow'=>array(
			'title'=>'自动播放',
			'type'=>'text',
			'value'=>'true',
			'tip'=>'"true" or "false"载入页面时，是否自动播放'
		),
		'show_model'=>array(
			'title'=>'显示模式',
			'type'=>'select',
			'options'=>array(
				'1'=>'幻灯片+试听记录',
				'2'=>'幻灯片+音乐统计',
				'3'=>'幻灯片',
			),
			'value'=>'1'
		),
		
	);