//绘制饼图

$.fn.chart = function (options) {
    options = $.extend({
        color: ["#FFAA00", "#00AABB", "#FF4400", '#E800E8', '#FFBFFF', '#02DF82', "#00FF21"],
        data: [], //[{text:'',data:123,color:''}...] color如果是null，将使用color中的颜色
        totalData: 100,
        totalText: '总共',
        totalColor: '#4B0091',
        unitText: '' //单位
    }, options || {});

    var c = $(this)[0];
    draw();

    function draw() {
        var ctx = c.getContext("2d");

        var radius = c.height / 2 - 20; //半径
        var ox = radius + 20, oy = radius + 20; //圆心

        var width = 10, height = 10; //图例宽和高
        var posX = ox * 2 + 20, posY = 30;   //
        var textX = posX + width + 5, textY = posY + 10;

        var startAngle = 0; //起始弧度
        var endAngle = 0;   //结束弧度

        ctx.font = 'bold 16px 微软雅黑';
        ctx.fillStyle = options.totalColor;
        ctx.fillRect(posX, posY, width, height);
        ctx.moveTo(posX, posY);
        ctx.fillText(options.totalText + ':' + options.totalData + options.unitText, textX, textY);//绘制total

        for (var i = 0; i < options.data.length; i++) {
            //绘制饼图
            endAngle = endAngle + options.data[i].data / options.total * Math.PI * 2; //结束弧度
            ctx.fillStyle = options.data[i].color ? options.data[i].color : options.color[i];
            ctx.beginPath();
            ctx.moveTo(ox, oy); //移动到到圆心
            ctx.arc(ox, oy, radius, startAngle, endAngle, false);
            ctx.closePath();
            ctx.fill();
            startAngle = endAngle; //设置起始弧度

            //绘制比例图及文字
            ctx.fillStyle = options.data[i].color ? options.data[i].color : options.color[i];
            ctx.fillRect(posX, posY + 20 * (i + 1), width, height);
            ctx.moveTo(posX, posY + 20 * (i + 1));
            ctx.font = 'bold 12px 微软雅黑';    //斜体 30像素 微软雅黑字体
            ctx.fillStyle = options.color[i]; //"#000000";
            var percent = options.data[i].text + "：" + options.data[i].data + options.unitText;
            ctx.fillText(percent, textX, textY + 20 * (i + 1));
        }
    }

}
