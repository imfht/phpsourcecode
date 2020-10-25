/*****************************************************************************
                                   日期资料
*****************************************************************************/
var ttime = 0;
var tInfo = new Array(
0x04bd8, 0x04ae0, 0x0a570, 0x054d5, 0x0d260, 0x0d950, 0x16554, 0x056a0, 0x09ad0, 0x055d2,
0x04ae0, 0x0a5b6, 0x0a4d0, 0x0d250, 0x1d255, 0x0b540, 0x0d6a0, 0x0ada2, 0x095b0, 0x14977,
0x04970, 0x0a4b0, 0x0b4b5, 0x06a50, 0x06d40, 0x1ab54, 0x02b60, 0x09570, 0x052f2, 0x04970,
0x06566, 0x0d4a0, 0x0ea50, 0x06e95, 0x05ad0, 0x02b60, 0x186e3, 0x092e0, 0x1c8d7, 0x0c950,
0x0d4a0, 0x1d8a6, 0x0b550, 0x056a0, 0x1a5b4, 0x025d0, 0x092d0, 0x0d2b2, 0x0a950, 0x0b557,
0x06ca0, 0x0b550, 0x15355, 0x04da0, 0x0a5b0, 0x14573, 0x052b0, 0x0a9a8, 0x0e950, 0x06aa0,
0x0aea6, 0x0ab50, 0x04b60, 0x0aae4, 0x0a570, 0x05260, 0x0f263, 0x0d950, 0x05b57, 0x056a0,
0x096d0, 0x04dd5, 0x04ad0, 0x0a4d0, 0x0d4d4, 0x0d250, 0x0d558, 0x0b540, 0x0b6a0, 0x195a6,
0x095b0, 0x049b0, 0x0a974, 0x0a4b0, 0x0b27a, 0x06a50, 0x06d40, 0x0af46, 0x0ab60, 0x09570,
0x04af5, 0x04970, 0x064b0, 0x074a3, 0x0ea50, 0x06b58, 0x055c0, 0x0ab60, 0x096d5, 0x092e0,
0x0c960, 0x0d954, 0x0d4a0, 0x0da50, 0x07552, 0x056a0, 0x0abb7, 0x025d0, 0x092d0, 0x0cab5,
0x0a950, 0x0b4a0, 0x0baa4, 0x0ad50, 0x055d9, 0x04ba0, 0x0a5b0, 0x15176, 0x052b0, 0x0a930,
0x07954, 0x06aa0, 0x0ad50, 0x05b52, 0x04b60, 0x0a6e6, 0x0a4e0, 0x0d260, 0x0ea65, 0x0d530,
0x05aa0, 0x076a3, 0x096d0, 0x04bd7, 0x04ad0, 0x0a4d0, 0x1d0b6, 0x0d250, 0x0d520, 0x0dd45,
0x0b5a0, 0x056d0, 0x055b2, 0x049b0, 0x0a577, 0x0a4b0, 0x0aa50, 0x1b255, 0x06d20, 0x0ada0,
0x14b63);

var solarMonth = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
var Gan = new Array("甲", "乙", "丙", "丁", "戊", "己", "庚", "辛", "壬", "癸");
var Zhi = new Array("子", "丑", "寅", "卯", "辰", "巳", "午", "未", "申", "酉", "戌", "亥");
var Animals = new Array("鼠", "牛", "虎", "兔", "龙", "蛇", "马", "羊", "猴", "鸡", "狗", "猪");
var solarTerm = new Array("小寒", "大寒", "立春", "雨水", "惊蛰", "春分", "清明", "谷雨", "立夏", "小满", "芒种", "夏至", "小暑", "大暑", "立秋", "处暑", "白露", "秋分", "寒露", "霜降", "立冬", "小雪", "大雪", "冬至");
var sTermInfo = new Array(0, 21208, 42467, 63836, 85337, 107014, 128867, 150921, 173149, 195551, 218072, 240693, 263343, 285989, 308563, 331033, 353350, 375494, 397447, 419210, 440795, 462224, 483532, 504758);
var nStr1 = new Array('日', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十');
var nStr2 = new Array('初', '十', '廿', '卅', '□');
var monthName = new Array("一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月");

//国历节日 *表示放假日
var sFtv = new Array(
	"0101*元旦节",
	"0214 情人节",
	"0308 妇女节",
	"0401 愚人节",
	"0501*劳动节",
	"0504 青年节",
	"0601 国际儿童节",
	"0701中共诞辰",
	"0801 建军节",
	"0910 中国教师节",
	"1001*国庆节",
	"1224 平安夜",
	"1225 圣诞节")

//农历节日 *表示放假日
var lFtv = new Array(
	"0101*春节",
	"0115 元宵节",
	"0505*端午节",
	"0707 七夕情人节",
	"0715 中元节",
	"0815*中秋节",
	"0909 重阳节",
	"1208 腊八节",
	"1223 小年",
	"0100*除夕")

//某月的第几个星期几
var wFtv = new Array(
	"0520 国际母亲节",
	"0630 父亲节",
	"1144 感恩节")

/*****************************************************************************
日期计算
*****************************************************************************/

//====================================== 返回农历 y年的总天数

	function lYearDays(y) {
		var i, sum = 348;
		for (i = 0x8000; i > 0x8; i >>= 1) sum += (tInfo[y - 1900] & i) ? 1 : 0;
		return (sum + leapDays(y));
	}

	//====================================== 返回农历 y年闰月的天数

	function leapDays(y) {
		if (leapMonth(y)) return ((tInfo[y - 1900] & 0x10000) ? 30 : 29);
		else return (0);
	}

	//====================================== 返回农历 y年闰哪个月 1-12 , 没闰返回 0

	function leapMonth(y) {
		return (tInfo[y - 1900] & 0xf);
	}

	//====================================== 返回农历 y年m月的总天数

	function monthDays(y, m) {
		return ((tInfo[y - 1900] & (0x10000 >> m)) ? 30 : 29);
	}


	//====================================== 算出农历, 传入日期控件, 返回农历日期控件
	//                                       该控件属性有 .year .month .day .isLeap

	function Lunar(objDate) {

		var i, leap = 0,
			temp = 0;
		var offset = (Date.UTC(objDate.getFullYear(), objDate.getMonth(), objDate.getDate()) - Date.UTC(1900, 0, 31)) / 86400000;

		for (i = 1900; i < 2050 && offset > 0; i++) {
			temp = lYearDays(i);
			offset -= temp;
		}

		if (offset < 0) {
			offset += temp;
			i--;
		}

		this.year = i;

		leap = leapMonth(i); //闰哪个月
		this.isLeap = false;

		for (i = 1; i < 13 && offset > 0; i++) {
			//闰月
			if (leap > 0 && i == (leap + 1) && this.isLeap == false) {
				--i;
				this.isLeap = true;
				temp = leapDays(this.year);
			} else {
				temp = monthDays(this.year, i);
			}

			//解除闰月
			if (this.isLeap == true && i == (leap + 1)) this.isLeap = false;

			offset -= temp;
		}

		if (offset == 0 && leap > 0 && i == leap + 1) if (this.isLeap) {
			this.isLeap = false;
		} else {
			this.isLeap = true;
			--i;
		}

		if (offset < 0) {
			offset += temp;
			--i;
		}

		this.month = i;
		this.day = offset + 1;
	}

	//==============================返回公历 y年某m+1月的天数

	function solarDays(y, m) {
		if (m == 1) return (((y % 4 == 0) && (y % 100 != 0) || (y % 400 == 0)) ? 29 : 28);
		else return (solarMonth[m]);
	}
	//============================== 传入 offset 返回干支, 0=甲子

	function cyclical(num) {
		return (Gan[num % 10] + Zhi[num % 12]);
	}

	//============================== 阴历属性

	function calElement(sYear, sMonth, sDay, week, lYear, lMonth, lDay, isLeap, cYear, cMonth, cDay) {

		this.isToday = false;
		//瓣句
		this.sYear = sYear; //公元年4位数字
		this.sMonth = sMonth; //公元月数字
		this.sDay = sDay; //公元日数字
		this.week = week; //星期, 1个中文
		//农历
		this.lYear = lYear; //公元年4位数字
		this.lMonth = lMonth; //农历月数字
		this.lDay = lDay; //农历日数字
		this.isLeap = isLeap; //是否为农历闰月?
		//八字
		this.cYear = cYear; //年柱, 2个中文
		this.cMonth = cMonth; //月柱, 2个中文
		this.cDay = cDay; //日柱, 2个中文

		this.color = '';

		this.lunarFestival = ''; //农历节日
		this.solarFestival = ''; //公历节日
		this.solarTerms = ''; //节气
	}

	//===== 某年的第n个节气为几日(从0小寒起算)

	function sTerm(y, n) {
		if (y == 2009 && n == 2) {
			sTermInfo[n] = 43467
		}
		var offDate = new Date((31556925974.7 * (y - 1900) + sTermInfo[n] * 60000) + Date.UTC(1900, 0, 6, 2, 5));
		return (offDate.getUTCDate());
	}



	//============================== 返回阴历控件 (y年,m+1月)
	/*
功能说明: 返回整个月的日期资料控件

使用方式: OBJ = new calendar(年,零起算月);

OBJ.length      返回当月最大日
OBJ.firstWeek   返回当月一日星期

由 OBJ[日期].属性名称 即可取得各项值

OBJ[日期].isToday  返回是否为今日 true 或 false

其他 OBJ[日期] 属性参见 calElement() 中的注解
*/
	function calendar(y, m) {

		var sDObj, lDObj, lY, lM, lD = 1,
			lL, lX = 0,
			tmp1, tmp2, tmp3;
		var cY, cM, cD; //年柱,月柱,日柱
		var lDPOS = new Array(3);
		var n = 0;
		var firstLM = 0;

		sDObj = new Date(y, m, 1, 0, 0, 0, 0); //当月一日日期

		this.length = solarDays(y, m); //公历当月天数
		this.firstWeek = sDObj.getDay(); //公历当月1日星期几


		sDObj = new Date(y, m, i + 1 - this.firstWeek); //当月一日日期
		y2 = sDObj.getFullYear();
		m2 = sDObj.getMonth();
		d2 = sDObj.getDate();

		////////年柱 1900年立春后为庚子年(60进制36)
		if (m2 < 2) cY = cyclical(y2 - 1900 + 36 - 1);
		else cY = cyclical(y2 - 1900 + 36);
		var term2 = sTerm(y2, 2); //立春日期

		////////月柱 1900年1月小寒以前为 丙子月(60进制12)
		var firstNode = sTerm(y2, m2 * 2) //返回当月「节」为几日开始
		cM = cyclical((y2 - 1900) * 12 + m2 + 12);

		//当月一日与 1900/1/1 相差天数
		//1900/1/1与 1970/1/1 相差25567日, 1900/1/1 日柱为甲戌日(60进制10)
		var dayCyclical = Date.UTC(y2, m2, d2, 0, 0, 0, 0) / 86400000 + 25567 + 10;

		for (var i = 0; i < 42; i++){
			sDObj = new Date(y, m, i + 1 - this.firstWeek); //当月一日日期
			y2 = sDObj.getFullYear();
			m2 = sDObj.getMonth();
			d2 = sDObj.getDate();
			if (lD > lX) {
				sDObj = new Date(y2, m2, d2); //当月一日日期
				lDObj = new Lunar(sDObj); //农历
				lY = lDObj.year; //农历年
				lM = lDObj.month; //农历月
				lD = lDObj.day; //农历日
				lL = lDObj.isLeap; //农历是否闰月
				lX = lL ? leapDays(lY) : monthDays(lY, lM); //农历当月最后一天

				if (n == 0) firstLM = lM;
				lDPOS[n++] = i - lD + 1;
			}

			//依节气调整二月分的年柱, 以立春为界
			if (m2 == 1 && (d2) == term2) cY = cyclical(y - 1900 + 36);
			//依节气月柱, 以「节」为界
			if (d2 == firstNode) cM = cyclical((y2 - 1900) * 12 + m2 + 13);
			//日柱
			cD = cyclical(dayCyclical + d2);

			//sYear,sMonth,sDay,week,
			//lYear,lMonth,lDay,isLeap,
			//cYear,cMonth,cDay
			this[i] = new calElement(y2, m2 + 1, d2, nStr1[(i + this.firstWeek) % 7],
			lY, lM, lD++, lL,
			cY, cM, cD);
			//公历节日
			for (x in sFtv)
			if (sFtv[x].match(/^(\d{2})(\d{2})([\s\*])(.+)$/)) if (Number(RegExp.$1) == (m2 + 1)) {
				if (d2 == RegExp.$2) {
					this[i].solarFestival += RegExp.$4 + ' ';
					if (RegExp.$3 == '*') this[i].color = 'red';
				}
			}
		}
		//节气
		tmp1 = sTerm(y, m * 2) - 1;
		tmp2 = sTerm(y, m * 2 + 1) - 1;
		this[tmp1 + this.firstWeek].solarTerms = solarTerm[m * 2];
		this[tmp2 + this.firstWeek].solarTerms = solarTerm[m * 2 + 1];
		//guohao
		if (y == 2009 && m == 1) {
			if (tD == 3) {
				this[tmp1].solarTerms = ''
				//this[tmp2].solarTerms = ''
			} else if (tD == 4) {
				this[tmp1].solarTerms = '立春'
				//this[tmp2].solarTerms = ''
			}
		}
		if (m2 == 3) this[tmp1].color = 'red'; //清明颜色

		//月周节日
		for (i in wFtv)
		if (wFtv[i].match(/^(\d{2})(\d)(\d)([\s\*])(.+)$/)) if (Number(RegExp.$1) == (m + 1)) {
			tmp1 = Number(RegExp.$2);
			tmp2 = Number(RegExp.$3);
			if (tmp1 < 5) this[((this.firstWeek > tmp2) ? 7 : 0) + 7 * (tmp1 - 1) + tmp2].solarFestival += RegExp.$5 + ' ';
			else {
				tmp1 -= 5;
				tmp3 = (this.firstWeek + this.length - 1) % 7; //当月最后一天星期?
				this[this.length - tmp3 - 7 * tmp1 + tmp2 - (tmp2 > tmp3 ? 7 : 0) - 1].solarFestival += RegExp.$5 + ' ';
			}
		}

		//农历节日
		for (i in lFtv)
		if (lFtv[i].match(/^(\d{2})(.{2})([\s\*])(.+)$/)) {
			tmp1 = Number(RegExp.$1) - firstLM;
			if (tmp1 == -11) tmp1 = 1;
			if (tmp1 >= 0 && tmp1 < n) {
				tmp2 = lDPOS[tmp1] + Number(RegExp.$2) - 1;
				if (tmp2 >= 0 && tmp2 < this.length && this[tmp2].isLeap != true) {
					this[tmp2].lunarFestival += RegExp.$4 + ' ';
					if (RegExp.$3 == '*') this[tmp2].color = 'red';
				}
			}
		}
		//今日
		if (y == tY && m == tM) this[tD+this.firstWeek-1].isToday = true;
	}

	//======================================= 返回该年的复活节(春分后第一次满月周后的第一主日)

	function easter(y) {

		var term2 = sTerm(y, 5); //取得春分日期
		var dayTerm2 = new Date(Date.UTC(y, 2, term2, 0, 0, 0, 0)); //取得春分的公历日期控件(春分一定出现在3月)
		var lDayTerm2 = new Lunar(dayTerm2); //取得取得春分农历

		if (lDayTerm2.day < 15) //取得下个月圆的相差天数
		var lMlen = 15 - lDayTerm2.day;
		else var lMlen = (lDayTerm2.isLeap ? leapDays(y) : monthDays(y, lDayTerm2.month)) - lDayTerm2.day + 15;

		//一天等于 1000*60*60*24 = 86400000 毫秒
		var l15 = new Date(dayTerm2.getTime() + 86400000 * lMlen); //求出第一次月圆为公历几日
		var dayEaster = new Date(l15.getTime() + 86400000 * (7 - l15.getUTCDay())); //求出下个周日

		this.m = dayEaster.getUTCMonth();
		this.d = dayEaster.getUTCDate();

	}

	//====================== 中文日期

	function cDay(d) {
		var s;

		switch (d) {
			case 10:
				s = '初十';
				break;
			case 20:
				s = '二十';
				break;
				break;
			case 30:
				s = '三十';
				break;
				break;
			default:
				s = nStr2[Math.floor(d / 10)];
				s += nStr1[d % 10];
		}
		return (s);
	}

	///////////////////////////////////////////////////////////////////////////////

var cld;

function drawCld(SY, SM) {
	var i, sD, s, size;
	cld = new calendar(SY, SM);

	if (SY > 1874 && SY < 1909) yDisplay = '光绪' + (((SY - 1874) == 1) ? '元' : SY - 1874);
	if (SY > 1908 && SY < 1912) yDisplay = '宣统' + (((SY - 1908) == 1) ? '元' : SY - 1908);

	if (SY > 1911) yDisplay = '建国' + (((SY - 1949) == 1) ? '元' : SY - 1949);

	GZ.innerHTML = '农历 ' + cyclical(SY - 1900 + 36) + '年 【' + Animals[(SY - 4) % 12] + '年】';

	for (i = 0; i < 42; i++) {
		sObj = eval('SD' + i);
		lObj = eval('LD' + i);
		ulObj = eval('UL' + i);

		sObj.className = '';

		sD = i;
		if (true) { //日期内
			sObj.innerHTML = cld[sD].sDay;

			if (cld[sD].isToday&& sObj.parentNode.className.indexOf("todayColor")==-1){
				sObj.parentNode.className = sObj.parentNode.className+' todayColor'; //今日颜色	
			}
			m = "0" + cld[sD].sMonth;
			d = "0" + cld[sD].sDay;
			ulObj.className = "div_" + cld[sD].sYear + "-" + m.substring(m.length - 2) + "-" + d.substring(d.length - 2);

			sObj.style.color = cld[sD].color; //法定假日颜色

			if (cld[sD].lDay == 1) //显示农历月
			lObj.innerHTML = '<b>' + (cld[sD].isLeap ? '闰' : '') + monthName[cld[sD].lMonth - 1] + '' + (monthDays(cld[sD].lYear, cld[sD].lMonth) == 29 ? '小' : '大') + '</b>';
			else //显示农历日
			lObj.innerHTML = cDay(cld[sD].lDay);

			s = cld[sD].lunarFestival;
			if (s.length > 0) { //农历节日
				if (s.length > 6) s = s.substr(0, 4) + '...';
				s = s.fontcolor('red');
			} else { //公历节日
				s = cld[sD].solarFestival;
				if (s.length > 0) {
					size = (s.charCodeAt(0) > 0 && s.charCodeAt(0) < 128) ? 8 : 4;
					if (s.length > size + 2) s = s.substr(0, size) + '...';
					s = (s == '黑色星期五') ? s.fontcolor('black') : s.fontcolor('blue');
				} else { //廿四节气
					s = cld[sD].solarTerms;
					if (s.length > 0) s = s.fontcolor('limegreen');
				}
			}

			if (cld[sD].solarTerms == '清明') s = '清明节'.fontcolor('red');
			//if(cld[sD].solarTerms=='芒种') s = '芒种'.fontcolor('red');
			//if(cld[sD].solarTerms=='夏至') s = '夏至'.fontcolor('red');
			//if(cld[sD].solarTerms=='冬至') s = '冬至'.fontcolor('red');

			if (s.length > 0) lObj.innerHTML = s;

		} else { //非日期
			sObj.innerHTML = '';
			lObj.innerHTML = '';
		}
	}
}


function changeCld() {
	var y, m;
	y = CLD.SY.selectedIndex + 1900;
	m = CLD.SM.selectedIndex;
	drawCld(y, m);
}

function pushBtm(K) {
	switch (K) {
		case 'YU':
			if (CLD.SY.selectedIndex > 0) CLD.SY.selectedIndex--;
			break;
		case 'YD':
			if (CLD.SY.selectedIndex < 150) CLD.SY.selectedIndex++;
			break;
		case 'MU':
			if (CLD.SM.selectedIndex > 0) {
				CLD.SM.selectedIndex--;
			} else {
				CLD.SM.selectedIndex = 11;
				if (CLD.SY.selectedIndex > 0) CLD.SY.selectedIndex--;
			}
			break;
		case 'MD':
			if (CLD.SM.selectedIndex < 11) {
				CLD.SM.selectedIndex++;
			} else {
				CLD.SM.selectedIndex = 0;
				if (CLD.SY.selectedIndex < 150) CLD.SY.selectedIndex++;
			}
			break;
		default:
			CLD.SY.selectedIndex = tY - 1900;
			CLD.SM.selectedIndex = tM;
	}
	$(".todayColor").removeClass("todayColor");
	changeCld();
	showdata();
}

var Today = new Date();
var tY = Today.getFullYear();
var tM = Today.getMonth();
var tD = Today.getDate();
//////////////////////////////////////////////////////////////////////////////

var width = "130";
var offsetx = 2;
var offsety = 8;

var x = 0;
var y = 0;
var snow = 0;
var sw = 0;
var cnt = 0;

var dStyle;

function tick() {
	var today
	today = new Date()
}

/////////////////////////////////////////////////////////

function initial() {
	CLD.SY.selectedIndex = tY - 1900;
	CLD.SM.selectedIndex = tM;
	drawCld(tY, tM);
	pushBtm('');
	tick();
}