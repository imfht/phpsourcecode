var AllImgNum=imgdatas.length;
var ListImgs='';
for(var i=0;i<AllImgNum;i++){
	ListImgs+='<li><img  onclick="changeShowImg('+i+')" src="'+imgdatas[i].picurl+'"/></li>';
}
$('.ShowImgsBox .ListImgs ul').html(ListImgs);
var BigImgBox=$('.ShowImgsBox .BigImg div');
var ImgBoxWidth=parseInt($('.ShowImgsBox').width());
var Left_num=Math.floor(ImgBoxWidth / 240);
var TheImgs=$('.ShowImgsBox .ListImgs ul li');
var NowShowNum=0;
function changeShowImg(num){
	NowShowNum=num;
	BigImgBox.hide();
	var thiscontent='';
	if(imgdatas[num].content!=''){
		thiscontent='<p><span>'+imgdatas[num].content+'</span></p>';
	}
	BigImgBox.html('<img src="'+imgdatas[num].picurl+'">'+thiscontent);
	BigImgBox.fadeIn();
	TheImgs.removeClass('ck');
	TheImgs.eq(num).addClass('ck');
	//$('.ShowImgsBox .ShowCnt').html(imgdatas[num].content);
	var Move_left=0;
	if(num>Left_num&&num<=(AllImgNum-Left_num)){
		Move_left=(num-Left_num)*120;
	}else if(num>(AllImgNum-Left_num)){
		Move_left=(AllImgNum-Left_num)*120;
	}
	$('.ShowImgsBox .ListImgs ul').animate({"left":"-"+Move_left+"px"},300);
}
function nextShowImg(){
	NowShowNum++;
	if(NowShowNum<AllImgNum){
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
$('.ShowImgsBox .BigImg').hover(
	function(){stopShowImg();},
	function(){autoShowIngs();}
);
$('.ShowImgsBox .BigImg div').click(function(){
	if($(this).hasClass("hideP")){
		$(this).removeClass("hideP");
	}else{
		$(this).addClass("hideP");
	}
});