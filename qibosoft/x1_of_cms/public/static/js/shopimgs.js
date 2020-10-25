var imgNums=shopimgs.length;
var ListImgs='';
for(var i=0;i<imgNums;i++){
	ListImgs+='<li><span><img onclick="changeShowImg('+i+')" src="'+shopimgs[i]+'"/></span></li>';
}
$('.Shop_ListImgs ul').html(ListImgs);
var BigImgBox=$('.Shop_BigImg div');
var ImgBoxWidth=parseInt($('.Shop_ListImgs').width());
var ListWidth=parseInt($('.Shop_ListImgs ul li').width());
var Left_num=Math.floor(ImgBoxWidth / (ListWidth*2));
var TheImgs=$('.Shop_ListImgs ul li');
var NowShowNum=0;
function changeShowImg(num){
	NowShowNum=num;
	BigImgBox.hide();
	BigImgBox.html('<img src="'+shopimgs[num]+'">');
	BigImgBox.fadeIn();
	TheImgs.removeClass('ck');
	TheImgs.eq(num).addClass('ck');
	var Move_left=0;
	if(num>Left_num&&num<=(imgNums-Left_num)){
		Move_left=(num-Left_num)*ListWidth;
	}else if(num>(imgNums-Left_num)){
		Move_left=(imgNums-Left_num)*ListWidth;
	}
	$('.Shop_ListImgs ul').animate({"left":"-"+Move_left+"px"},300);
}
function nextShowImg(){
	NowShowNum++;
	if(NowShowNum<imgNums){
		changeShowImg(NowShowNum);
	}else{
		//layer.msg('已是最后一张了！');
		NowShowNum=-1;
	}
}
function pravShowImg(){
	NowShowNum--;
	if(NowShowNum<0){
		layer.msg('已是最前一张了！');
		NowShowNum=0;
	}else{
		changeShowImg(NowShowNum);
	}
}
changeShowImg(NowShowNum);
var changeShowIngs;
function autoShowIngs(){
	changeShowIngs = setInterval("nextShowImg()",5000);
}
function stopShowImg() {
	clearInterval(changeShowIngs);
}
autoShowIngs();
$('.Shop_BigImg').hover(
	function(){stopShowImg();},
	function(){autoShowIngs();}
);