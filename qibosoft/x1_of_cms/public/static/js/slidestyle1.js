var thisSlideH1=parseInt($('.qb_ui_SlideStyle1').attr("height"));
if(thisSlideH1>50){
	$('.qb_ui_SlideStyle1 .slide').css({'height':thisSlideH1+'px'});
}else{
	thisSlideH1=thisSlideH1*10;
	$('.qb_ui_SlideStyle1 .slide').css({'padding-top':thisSlideH1+'%'});
}
var thisSlideNnm1=$('.qb_ui_SlideStyle1 .slide ul li').length;
var ListSideNum1="<div class='lists'>";
for(var i=0;i<thisSlideNnm1;i++){
	var listnum=i+1;
	ListSideNum1+="<span onclick='ShowSlideNum1("+i+")'>"+listnum+"</span>";
}
ListSideNum1+="</div>";
$('.qb_ui_SlideStyle1 .slide').append(ListSideNum1);
var thisSlideWidth1=parseInt($('.qb_ui_SlideStyle1 .slide ul li').width());
$(document).ready(function(){
  thisSlideWidth1=parseInt($('.qb_ui_SlideStyle1 .slide ul li').width());
});
function ShowSlideNum1(num){
	var MoveLeft=thisSlideWidth1*num;
	$('.qb_ui_SlideStyle1 .slide ul').animate({'left':'-'+MoveLeft+'px'},300);
	$('.qb_ui_SlideStyle1 .slide .lists span').removeClass('ck');
	$('.qb_ui_SlideStyle1 .slide .lists span').eq(num).addClass('ck');
}
var beginnum1=0;
function next_changSlide1(){
	beginnum1++;
	if(beginnum1>thisSlideNnm1-1){
		beginnum1=0;
	}
	ShowSlideNum1(beginnum1);
}
function parv_changSlide1(){
	beginnum1--;
	if(beginnum1<0){
		beginnum1=thisSlideNnm1-1;
	}
	ShowSlideNum1(beginnum1);
}
ShowSlideNum1(beginnum1);
var slideing1;
function autoSlide1(){
	slideing1 = setInterval("next_changSlide1()",5000);
}
function stopSlide1() {
	clearInterval(slideing1);
}
autoSlide1();
$('.qb_ui_SlideStyle1').hover(
	function(){stopSlide1();},
	function(){autoSlide1();}
);