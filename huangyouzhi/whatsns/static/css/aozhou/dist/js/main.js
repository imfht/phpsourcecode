/**
 * Created by Administrator on 2017/12/5.
 */

$("#site_hot_tag a").each(function(){
    x=0;
    y=9;
        var rand = parseInt(Math.random() * (x - y + 1) + y);

        $(this).css({'font-weight':"bold",'color':randcolor()});

    });
function randcolor(){
    //var m = Math.floor(Math.random()*90) + 100;//可以生成随机颜色值
    //return m;
    var arr=['#a39a92','#94bfe2','#f5933f','#5aacb2','#f4b5a4','#bbd7ce','#9c9c9c','#629a24','#d67cd2','#d7c5a5','#109d31','#74aedb'];
    return arr[GetRandomNum(0,arr.length-1)];
}
function GetRandomNum(Min,Max)
{
    var Range = Max - Min;
    var Rand = Math.random();
    return(Min + Math.round(Rand * Range));
}