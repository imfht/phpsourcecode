/**
 * echarts图表类：饼图
 *
 * @desc echarts基于Canvas，纯Javascript图表库，提供直观，生动，可交互，可个性化定制的数据统计图表。
 * @author Kener (@Kener-林峰, linzhifeng@baidu.com)
 *
 */
define(function (require) {
    var ComponentBase = require('../component/base');
    var ChartBase = require('./base');

    // 图形依赖
    var TextShape = require('zrender/shape/Text');
    var RingShape = require('zrender/shape/Ring');
    var CircleShape = require('zrender/shape/Circle');
    var SectorShape = require('zrender/shape/Sector');
    var BrokenLineShape = require('zrender/shape/BrokenLine');

    var ecConfig = require('../config');
    var ecData = require('../util/ecData');
    var zrUtil = require('zrender/tool/util');
    var zrMath = require('zrender/tool/math');
    var zrColor = require('zrender/tool/color');

    /**
     * 构造函数
     * @param {Object} messageCenter echart消息中心
     * @param {ZRender} zr zrender实例
     * @param {Object} series 数据
     * @param {Object} component 组件
     */
    function Pie(ecTheme, messageCenter, zr, option, myChart) {
        // 基类
        ComponentBase.call(this, ecTheme, messageCenter, zr, option, myChart);
        // 图表基类
        ChartBase.call(this);

        var self = this;
        /**
         * 输出动态视觉引导线
         */
        self.shapeHandler.onmouseover = function (param) {
            var shape = param.target;
            var seriesIndex = ecData.get(shape, 'seriesIndex');
            var dataIndex = ecData.get(shape, 'dataIndex');
            var percent = ecData.get(shape, 'special');
            var lastAddRadius = shape._lastAddRadius;

            var startAngle = shape.style.startAngle;
            var endAngle = shape.style.endAngle;
            var defaultColor = shape.highlightStyle.color;

            // 文本标签，需要显示则会有返回
            var label = self.getLabel(
                seriesIndex, dataIndex, percent, lastAddRadius,
                startAngle, endAngle, defaultColor,
                true
            );
            if (label) {
                self.zr.addHoverShape(label);
            }

            // 文本标签视觉引导线，需要显示则会有返回
            var labelLine = self.getLabelLine(
                seriesIndex, dataIndex, lastAddRadius,
                shape.style.r0, shape.style.r,
                startAngle, endAngle, defaultColor,
                true
            );
            if (labelLine) {
                self.zr.addHoverShape(labelLine);
            }
        };

        this.refresh(option);
    }

    Pie.prototype = {
        type: ecConfig.CHART_TYPE_PIE,
        /**
         * 绘制图形
         */
        _buildShape: function () {
            var series = this.series;
            var legend = this.component.legend;
            this.selectedMap = {};
            this._selected = {};
            var center;
            var radius;

            var pieCase;        // 饼图箱子
            this._selectedMode = false;
            var serieName;
            for (var i = 0, l = series.length; i < l; i++) {
                if (series[i].type == ecConfig.CHART_TYPE_PIE) {
                    series[i] = this.reformOption(series[i]);
                    serieName = series[i].name || '';
                    // 系列图例开关
                    this.selectedMap[serieName] =
                        legend ? legend.isSelected(serieName) : true;
                    if (!this.selectedMap[serieName]) {
                        continue;
                    }

                    center = this.parseCenter(this.zr, series[i].center);
                    radius = this.parseRadius(this.zr, series[i].radius);
                    this._selectedMode = this._selectedMode || series[i].selectedMode;
                    this._selected[i] = [];
                    if (this.deepQuery([series[i], this.option], 'calculable')) {
                        pieCase = {
                            zlevel: this._zlevelBase,
                            hoverable: false,
                            style: {
                                x: center[0],          // 圆心横坐标
                                y: center[1],          // 圆心纵坐标
                                // 圆环内外半径
                                r0: radius[0] <= 10 ? 0 : radius[0] - 10,
                                r: radius[1] + 10,
                                brushType: 'stroke',
                                lineWidth: 1,
                                strokeColor: series[i].calculableHolderColor
                                    || this.ecTheme.calculableHolderColor
                            }
                        };
                        ecData.pack(pieCase, series[i], i, undefined, -1);
                        this.setCalculable(pieCase);

                        pieCase = radius[0] <= 10
                            ? new CircleShape(pieCase)
                            : new RingShape(pieCase);
                        this.shapeList.push(pieCase);
                    }
                    this._buildSinglePie(i);
                    this.buildMark(i);
                }
            }

            this.addShapeList();
        },

        /**
         * 构建单个饼图
         *
         * @param {number} seriesIndex 系列索引
         */
        _buildSinglePie: function (seriesIndex) {
            var series = this.series;
            var serie = series[seriesIndex];
            var data = serie.data;
            var legend = this.component.legend;
            var itemName;
            var totalSelected = 0;               // 迭代累计选中且非0个数
            var totalSelectedValue0 = 0;         // 迭代累计选中0只个数
            var totalValue = 0;                  // 迭代累计
            var maxValue = Number.NEGATIVE_INFINITY;

            // 计算需要显示的个数和总值
            for (var i = 0, l = data.length; i < l; i++) {
                itemName = data[i].name;
                if (legend) {
                    this.selectedMap[itemName] = legend.isSelected(itemName);
                } else {
                    this.selectedMap[itemName] = true;
                }
                if (this.selectedMap[itemName] && !isNaN(data[i].value)) {
                    if (+data[i].value !== 0) {
                        totalSelected++;
                    }
                    else {
                        totalSelectedValue0++;
                    }
                    totalValue += +data[i].value;
                    maxValue = Math.max(maxValue, +data[i].value);
                }
            }

            var percent = 100;
            var lastPercent;    // 相邻细角度优化
            var lastAddRadius = 0;
            var clockWise = serie.clockWise;
            var startAngle = serie.startAngle.toFixed(2) - 0;
            var endAngle;
            var minAngle = serie.minAngle || 0.01; // #bugfixed
            var totalAngle = 360 - (minAngle * totalSelected)
                - 0.01 * totalSelectedValue0;
            var defaultColor;
            var roseType = serie.roseType;
            var radius;
            var r0;     // 扇形内半径
            var r1;     // 扇形外半径

            for (var i = 0, l = data.length; i < l; i++) {
                itemName = data[i].name;
                if (!this.selectedMap[itemName] || isNaN(data[i].value)) {
                    continue;
                }
                // 默认颜色策略
                if (legend) {
                    // 有图例则从图例中获取颜色定义
                    defaultColor = legend.getColor(itemName);
                }
                else {
                    // 全局颜色定义
                    defaultColor = this.zr.getColor(i);
                }

                lastPercent = percent;
                percent = data[i].value / totalValue;
                if (roseType != 'area') {
                    endAngle = clockWise
                        ? (startAngle - percent * totalAngle - (percent !== 0 ? minAngle : 0.01))
                        : (percent * totalAngle + startAngle + (percent !== 0 ? minAngle : 0.01));
                }
                else {
                    endAngle = clockWise
                        ? (startAngle - 360 / l)
                        : (360 / l + startAngle);
                }
                endAngle = endAngle.toFixed(2) - 0;
                percent = (percent * 100).toFixed(2);

                radius = this.parseRadius(this.zr, serie.radius);
                r0 = +radius[0];
                r1 = +radius[1];

                if (roseType == 'radius') {
                    r1 = data[i].value / maxValue * (r1 - r0) * 0.8
                        + (r1 - r0) * 0.2
                        + r0;
                }
                else if (roseType == 'area') {
                    r1 = Math.sqrt(data[i].value / maxValue) * (r1 - r0) + r0;
                }

                if (clockWise) {
                    var temp;
                    temp = startAngle;
                    startAngle = endAngle;
                    endAngle = temp;
                }

                // 当前小角度需要检查前一个是否也是小角度，如果是得调整长度，不能完全避免，但能大大降低覆盖概率
                if (i > 0
                    && Math.abs(startAngle - endAngle) < 15       // 约15度
                    && lastPercent < 4
                    && this._needLabel(serie, data[i], false)
                    && this.deepQuery(
                    [data[i], serie], 'itemStyle.normal.label.position'
                ) != 'center'
                    ) {
                    // 都小就延长，前小后大就缩短
                    lastAddRadius += (percent < 4 ? 20 : -20);
                }
                else {
                    lastAddRadius = 0;
                }

                this._buildItem(
                    seriesIndex, i, percent, lastAddRadius, // 相邻最小角度优化
                    data[i].selected,
                    r0, r1,
                    startAngle, endAngle, defaultColor
                );
                if (!clockWise) {
                    startAngle = endAngle;
                }
            }
        },

        /**
         * 构建单个扇形及指标
         */
        _buildItem: function (seriesIndex, dataIndex, percent, lastAddRadius, isSelected, r0, r1, startAngle, endAngle, defaultColor) {
            var series = this.series;
            // 扇形
            var sector = this.getSector(
                seriesIndex, dataIndex, percent, isSelected,
                r0, r1,
                startAngle, endAngle, defaultColor
            );
            // 图形需要附加的私有数据
            ecData.pack(
                sector,
                series[seriesIndex], seriesIndex,
                series[seriesIndex].data[dataIndex], dataIndex,
                series[seriesIndex].data[dataIndex].name,
                percent
            );
            sector._lastAddRadius = lastAddRadius;
            this.shapeList.push(sector);

            // 文本标签，需要显示则会有返回
            var label = this.getLabel(
                seriesIndex, dataIndex, percent, lastAddRadius,
                startAngle, endAngle, defaultColor,
                false
            );
            if (label) {
                ecData.pack(
                    label,
                    series[seriesIndex], seriesIndex,
                    series[seriesIndex].data[dataIndex], dataIndex,
                    series[seriesIndex].data[dataIndex].name,
                    percent
                );
                label._dataIndex = dataIndex;
                this.shapeList.push(label);
            }

            // 文本标签视觉引导线，需要显示则会有返回
            var labelLine = this.getLabelLine(
                seriesIndex, dataIndex, lastAddRadius,
                r0, r1,
                startAngle, endAngle, defaultColor,
                false
            );
            if (labelLine) {
                ecData.pack(
                    labelLine,
                    series[seriesIndex], seriesIndex,
                    series[seriesIndex].data[dataIndex], dataIndex,
                    series[seriesIndex].data[dataIndex].name,
                    percent
                );
                labelLine._dataIndex = dataIndex;
                this.shapeList.push(labelLine);
            }
        },

        /**
         * 构建扇形
         */
        getSector: function (seriesIndex, dataIndex, percent, isSelected, r0, r1, startAngle, endAngle, defaultColor) {
            var series = this.series;
            var serie = series[seriesIndex];
            var data = serie.data[dataIndex];
            var queryTarget = [data, serie];
            var center = this.parseCenter(this.zr, serie.center);

            // 多级控制
            var normal = this.deepMerge(
                queryTarget,
                'itemStyle.normal'
            ) || {};
            var emphasis = this.deepMerge(
                queryTarget,
                'itemStyle.emphasis'
            ) || {};
            var normalColor = this.getItemStyleColor(normal.color, seriesIndex, dataIndex, data)
                || defaultColor;

            var emphasisColor = this.getItemStyleColor(emphasis.color, seriesIndex, dataIndex, data)
                || (typeof normalColor == 'string'
                ? zrColor.lift(normalColor, -0.2)
                : normalColor
                );

            var sector = {
                zlevel: this._zlevelBase,
                clickable: true,
                style: {
                    x: center[0],          // 圆心横坐标
                    y: center[1],          // 圆心纵坐标
                    r0: r0,         // 圆环内半径
                    r: r1,          // 圆环外半径
                    startAngle: startAngle,
                    endAngle: endAngle,
                    brushType: 'both',
                    color: normalColor,
                    lineWidth: normal.borderWidth,
                    strokeColor: normal.borderColor,
                    lineJoin: 'round'
                },
                highlightStyle: {
                    color: emphasisColor,
                    lineWidth: emphasis.borderWidth,
                    strokeColor: emphasis.borderColor,
                    lineJoin: 'round'
                },
                _seriesIndex: seriesIndex,
                _dataIndex: dataIndex
            };

            if (isSelected) {
                var midAngle =
                    ((sector.style.startAngle + sector.style.endAngle) / 2)
                        .toFixed(2) - 0;
                sector.style._hasSelected = true;
                sector.style._x = sector.style.x;
                sector.style._y = sector.style.y;
                var offset = this.query(serie, 'selectedOffset');
                sector.style.x += zrMath.cos(midAngle, true) * offset;
                sector.style.y -= zrMath.sin(midAngle, true) * offset;

                this._selected[seriesIndex][dataIndex] = true;
            }
            else {
                this._selected[seriesIndex][dataIndex] = false;
            }


            if (this._selectedMode) {
                sector.onclick = this.shapeHandler.onclick;
            }

            if (this.deepQuery([data, serie, this.option], 'calculable')) {
                this.setCalculable(sector);
                sector.draggable = true;
            }

            // “normal下不显示，emphasis显示”添加事件响应
            if (this._needLabel(serie, data, true)          // emphasis下显示文本
                || this._needLabelLine(serie, data, true)   // emphasis下显示引导线
                ) {
                sector.onmouseover = this.shapeHandler.onmouseover;
            }

            sector = new SectorShape(sector);
            return sector;
        },

        /**
         * 需要显示则会有返回构建好的shape，否则返回undefined
         */
        getLabel: function (seriesIndex, dataIndex, percent, lastAddRadius, startAngle, endAngle, defaultColor, isEmphasis) {
            var series = this.series;
            var serie = series[seriesIndex];
            var data = serie.data[dataIndex];

            // 特定状态下是否需要显示文本标签
            if (!this._needLabel(serie, data, isEmphasis)) {
                return;
            }

            var status = isEmphasis ? 'emphasis' : 'normal';

            // serie里有默认配置，放心大胆的用！
            var itemStyle = zrUtil.merge(
                zrUtil.clone(data.itemStyle) || {},
                serie.itemStyle
            );
            // label配置
            var labelControl = itemStyle[status].label;
            var textStyle = labelControl.textStyle || {};

            var center = this.parseCenter(this.zr, serie.center);
            var centerX = center[0];                      // 圆心横坐标
            var centerY = center[1];                      // 圆心纵坐标
            var x;
            var y;
            var midAngle = ((endAngle + startAngle) / 2 + 360) % 360; // 中值
            var radius = this.parseRadius(this.zr, serie.radius);  // 标签位置半径
            var textAlign;
            var textBaseline = 'middle';
            labelControl.position = labelControl.position
                || itemStyle.normal.label.position;
            if (labelControl.position == 'center') {
                // center显示
                radius = radius[1];
                x = centerX;
                y = centerY;
                textAlign = 'center';
            }
            else if (labelControl.position == 'inner') {
                // 内部显示
                radius = (radius[0] + radius[1]) / 2 + lastAddRadius;
                x = Math.round(
                    centerX + radius * zrMath.cos(midAngle, true)
                );
                y = Math.round(
                    centerY - radius * zrMath.sin(midAngle, true)
                );
                defaultColor = '#fff';
                textAlign = 'center';

            }
            else {
                // 外部显示，默认 labelControl.position == 'outer')
                radius = radius[1]
                    - (-itemStyle[status].labelLine.length)
                    //- (-textStyle.fontSize)
                    + lastAddRadius;
                x = centerX + radius * zrMath.cos(midAngle, true);
                y = centerY - radius * zrMath.sin(midAngle, true);
                textAlign = (midAngle >= 90 && midAngle <= 270)
                    ? 'right' : 'left';
            }

            if (labelControl.position != 'center'
                && labelControl.position != 'inner'
                ) {
                x += textAlign == 'left' ? 20 : -20;
            }
            data.__labelX = x - (textAlign == 'left' ? 5 : -5);
            data.__labelY = y;

            return new TextShape({
                zlevel: this._zlevelBase + 1,
                hoverable: false,
                style: {
                    x: x,
                    y: y,
                    color: textStyle.color || defaultColor,
                    text: this.getLabelText(
                        seriesIndex, dataIndex, percent, status
                    ),
                    textAlign: textStyle.align || textAlign,
                    textBaseline: textStyle.baseline || textBaseline,
                    textFont: this.getFont(textStyle)
                },
                highlightStyle: {
                    brushType: 'fill'
                },
                _seriesIndex: seriesIndex,
                _dataIndex: dataIndex
            });
        },

        /**
         * 根据lable.format计算label text
         */
        getLabelText: function (seriesIndex, dataIndex, percent, status) {
            var series = this.series;
            var serie = series[seriesIndex];
            var data = serie.data[dataIndex];
            var formatter = this.deepQuery(
                [data, serie],
                'itemStyle.' + status + '.label.formatter'
            );

            if (formatter) {
                if (typeof formatter == 'function') {
                    return formatter.call(
                        this.myChart,
                        serie.name,
                        data.name,
                        data.value,
                        percent
                    );
                }
                else if (typeof formatter == 'string') {
                    formatter = formatter.replace('{a}', '{a0}')
                        .replace('{b}', '{b0}')
                        .replace('{c}', '{c0}')
                        .replace('{d}', '{d0}');
                    formatter = formatter.replace('{a0}', serie.name)
                        .replace('{b0}', data.name)
                        .replace('{c0}', data.value)
                        .replace('{d0}', percent);

                    return formatter;
                }
            }
            else {
                return data.name;
            }
        },

        /**
         * 需要显示则会有返回构建好的shape，否则返回undefined
         */
        getLabelLine: function (seriesIndex, dataIndex, lastAddRadius, r0, r1, startAngle, endAngle, defaultColor, isEmphasis) {
            var series = this.series;
            var serie = series[seriesIndex];
            var data = serie.data[dataIndex];

            // 特定状态下是否需要显示文本标签
            if (this._needLabelLine(serie, data, isEmphasis)) {
                var status = isEmphasis ? 'emphasis' : 'normal';

                // serie里有默认配置，放心大胆的用！
                var itemStyle = zrUtil.merge(
                    zrUtil.clone(data.itemStyle) || {},
                    serie.itemStyle
                );
                // labelLine配置
                var labelLineControl = itemStyle[status].labelLine;
                var lineStyle = labelLineControl.lineStyle || {};

                var center = this.parseCenter(this.zr, serie.center);
                var centerX = center[0];                    // 圆心横坐标
                var centerY = center[1];                    // 圆心纵坐标
                // 视觉引导线起点半径
                var midRadius = r1;
                // 视觉引导线终点半径
                var maxRadius = this.parseRadius(this.zr, serie.radius)[1]
                    - (-labelLineControl.length)
                    + lastAddRadius;
                var midAngle = ((endAngle + startAngle) / 2) % 360; // 角度中值
                var cosValue = zrMath.cos(midAngle, true);
                var sinValue = zrMath.sin(midAngle, true);
                // 三角函数缓存已在zrender/tool/math中做了
                return new BrokenLineShape({
                    // shape : 'brokenLine',
                    zlevel: this._zlevelBase + 1,
                    hoverable: false,
                    style: {
                        pointList: [
                            [
                                centerX + midRadius * cosValue,
                                centerY - midRadius * sinValue
                            ],
                            [
                                centerX + maxRadius * cosValue,
                                centerY - maxRadius * sinValue
                            ],
                            [
                                data.__labelX,
                                data.__labelY
                            ]
                        ],
                        //xStart : centerX + midRadius * cosValue,
                        //yStart : centerY - midRadius * sinValue,
                        //xEnd : centerX + maxRadius * cosValue,
                        //yEnd : centerY - maxRadius * sinValue,
                        strokeColor: lineStyle.color || defaultColor,
                        lineType: lineStyle.type,
                        lineWidth: lineStyle.width
                    },
                    _seriesIndex: seriesIndex,
                    _dataIndex: dataIndex
                });
            }
            else {
                return;
            }
        },

        /**
         * 返回特定状态（normal or emphasis）下是否需要显示label标签文本
         * @param {Object} serie
         * @param {Object} data
         * @param {boolean} isEmphasis true is 'emphasis' and false is 'normal'
         */
        _needLabel: function (serie, data, isEmphasis) {
            return this.deepQuery(
                [data, serie],
                'itemStyle.'
                    + (isEmphasis ? 'emphasis' : 'normal')
                    + '.label.show'
            );
        },

        /**
         * 返回特定状态（normal or emphasis）下是否需要显示labelLine标签视觉引导线
         * @param {Object} serie
         * @param {Object} data
         * @param {boolean} isEmphasis true is 'emphasis' and false is 'normal'
         */
        _needLabelLine: function (serie, data, isEmphasis) {
            return this.deepQuery(
                [data, serie],
                'itemStyle.'
                    + (isEmphasis ? 'emphasis' : 'normal')
                    + '.labelLine.show'
            );
        },

        /**
         * 参数修正&默认值赋值，重载基类方法
         * @param {Object} opt 参数
         */
        reformOption: function (opt) {
            // 常用方法快捷方式
            var _merge = zrUtil.merge;
            opt = _merge(
                opt || {},
                this.ecTheme.pie
            );

            // 通用字体设置
            opt.itemStyle.normal.label.textStyle = _merge(
                opt.itemStyle.normal.label.textStyle || {},
                this.ecTheme.textStyle
            );
            opt.itemStyle.emphasis.label.textStyle = _merge(
                opt.itemStyle.emphasis.label.textStyle || {},
                this.ecTheme.textStyle
            );

            return opt;
        },

        /**
         * 刷新
         */
        refresh: function (newOption) {
            if (newOption) {
                this.option = newOption;
                this.series = newOption.series;
            }

            this.backupShapeList();
            this._buildShape();
        },

        /**
         * 动态数据增加动画
         */
        addDataAnimation: function (params) {
            var series = this.series;
            var aniMap = {}; // seriesIndex索引参数
            for (var i = 0, l = params.length; i < l; i++) {
                aniMap[params[i][0]] = params[i];
            }

            // 构建新的饼图匹配差异做动画
            var sectorMap = {};
            var textMap = {};
            var lineMap = {};
            var backupShapeList = this.shapeList;
            this.shapeList = [];

            var seriesIndex;
            var isHead;
            var dataGrow;
            var deltaIdxMap = {};   // 修正新增数据后会对dataIndex产生错位匹配
            for (var i = 0, l = params.length; i < l; i++) {
                seriesIndex = params[i][0];
                isHead = params[i][2];
                dataGrow = params[i][3];
                if (series[seriesIndex]
                    && series[seriesIndex].type == ecConfig.CHART_TYPE_PIE
                    ) {
                    if (isHead) {
                        if (!dataGrow) {
                            sectorMap[
                                seriesIndex
                                    + '_'
                                    + series[seriesIndex].data.length
                                ] = 'delete';
                        }
                        deltaIdxMap[seriesIndex] = 1;
                    }
                    else {
                        if (!dataGrow) {
                            sectorMap[seriesIndex + '_-1'] = 'delete';
                            deltaIdxMap[seriesIndex] = -1;
                        }
                        else {
                            deltaIdxMap[seriesIndex] = 0;
                        }
                    }
                    this._buildSinglePie(seriesIndex);
                }
            }
            var dataIndex;
            var key;
            for (var i = 0, l = this.shapeList.length; i < l; i++) {
                seriesIndex = this.shapeList[i]._seriesIndex;
                dataIndex = this.shapeList[i]._dataIndex;
                key = seriesIndex + '_' + dataIndex;
                // map映射让n*n变n
                switch (this.shapeList[i].type) {
                    case 'sector' :
                        sectorMap[key] = this.shapeList[i];
                        break;
                    case 'text' :
                        textMap[key] = this.shapeList[i];
                        break;
                    case 'broken-line' :
                        lineMap[key] = this.shapeList[i];
                        break;
                }
            }
            this.shapeList = [];
            var targeSector;
            for (var i = 0, l = backupShapeList.length; i < l; i++) {
                seriesIndex = backupShapeList[i]._seriesIndex;
                if (aniMap[seriesIndex]) {
                    dataIndex = backupShapeList[i]._dataIndex
                        + deltaIdxMap[seriesIndex];
                    key = seriesIndex + '_' + dataIndex;
                    targeSector = sectorMap[key];
                    if (!targeSector) {
                        continue;
                    }
                    if (backupShapeList[i].type == 'sector') {
                        if (targeSector != 'delete') {
                            // 原有扇形
                            this.zr.animate(backupShapeList[i].id, 'style')
                                .when(
                                400,
                                {
                                    startAngle: targeSector.style.startAngle,
                                    endAngle: targeSector.style.endAngle
                                }
                            )
                                .start();
                        }
                        else {
                            // 删除的扇形
                            this.zr.animate(backupShapeList[i].id, 'style')
                                .when(
                                    400,
                                    deltaIdxMap[seriesIndex] < 0
                                        ? {
                                        startAngle: backupShapeList[i].style.startAngle
                                    }
                                        : {
                                        endAngle: backupShapeList[i].style.endAngle
                                    }
                                )
                                .start();
                        }
                    }
                    else if (backupShapeList[i].type == 'text'
                        || backupShapeList[i].type == 'broken-line'
                        ) {
                        if (targeSector == 'delete') {
                            // 删除逻辑一样
                            this.zr.delShape(backupShapeList[i].id);
                        }
                        else {
                            // 懒得新建变量了，借用一下
                            switch (backupShapeList[i].type) {
                                case 'text':
                                    targeSector = textMap[key];
                                    this.zr.animate(backupShapeList[i].id, 'style')
                                        .when(
                                        400,
                                        {
                                            x: targeSector.style.x,
                                            y: targeSector.style.y
                                        }
                                    )
                                        .start();
                                    break;
                                case 'broken-line':
                                    targeSector = lineMap[key];
                                    this.zr.animate(backupShapeList[i].id, 'style')
                                        .when(
                                        400,
                                        {
                                            pointList: targeSector.style.pointList
                                        }
                                    )
                                        .start();
                                    break;
                            }

                        }
                    }
                }
            }
            this.shapeList = backupShapeList;
        },

        onclick: function (param) {
            var series = this.series;
            if (!this.isClick || !param.target) {
                // 没有在当前实例上发生点击直接返回
                return;
            }
            this.isClick = false;
            var offset;             // 偏移
            var target = param.target;
            var style = target.style;
            var seriesIndex = ecData.get(target, 'seriesIndex');
            var dataIndex = ecData.get(target, 'dataIndex');

            for (var i = 0, len = this.shapeList.length; i < len; i++) {
                if (this.shapeList[i].id == target.id) {
                    seriesIndex = ecData.get(target, 'seriesIndex');
                    dataIndex = ecData.get(target, 'dataIndex');
                    // 当前点击的
                    if (!style._hasSelected) {
                        var midAngle =
                            ((style.startAngle + style.endAngle) / 2)
                                .toFixed(2) - 0;
                        target.style._hasSelected = true;
                        this._selected[seriesIndex][dataIndex] = true;
                        target.style._x = target.style.x;
                        target.style._y = target.style.y;
                        offset = this.query(
                            series[seriesIndex],
                            'selectedOffset'
                        );
                        target.style.x += zrMath.cos(midAngle, true)
                            * offset;
                        target.style.y -= zrMath.sin(midAngle, true)
                            * offset;
                    }
                    else {
                        // 复位
                        target.style.x = target.style._x;
                        target.style.y = target.style._y;
                        target.style._hasSelected = false;
                        this._selected[seriesIndex][dataIndex] = false;
                    }

                    this.zr.modShape(target.id, target);
                }
                else if (this.shapeList[i].style._hasSelected
                    && this._selectedMode == 'single'
                    ) {
                    seriesIndex = ecData.get(this.shapeList[i], 'seriesIndex');
                    dataIndex = ecData.get(this.shapeList[i], 'dataIndex');
                    // 单选模式下需要取消其他已经选中的
                    this.shapeList[i].style.x = this.shapeList[i].style._x;
                    this.shapeList[i].style.y = this.shapeList[i].style._y;
                    this.shapeList[i].style._hasSelected = false;
                    this._selected[seriesIndex][dataIndex] = false;
                    this.zr.modShape(
                        this.shapeList[i].id, this.shapeList[i]
                    );
                }
            }

            this.messageCenter.dispatch(
                ecConfig.EVENT.PIE_SELECTED,
                param.event,
                {
                    selected: this._selected,
                    target: ecData.get(target, 'name')
                },
                this.myChart
            );
            this.zr.refresh();
        }
    };

    zrUtil.inherits(Pie, ChartBase);
    zrUtil.inherits(Pie, ComponentBase);

    // 图表注册
    require('../chart').define('pie', Pie);

    return Pie;
});