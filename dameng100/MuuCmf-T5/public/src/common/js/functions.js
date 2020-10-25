/**
 * 公共函数js文件
 */
function is_login() {
    return parseInt(MID);
}
/**
 * 模拟Url函数
 * @param url
 * @param params
 * @returns {string}
 * @constructor
 */
function Url(url, params, rewrite) {

    var website = '/';
    url = url.split('/');
    if (url[0] == '' || url[0] == '@')
        url[0] = APPNAME;
    if (!url[1])
        url[1] = 'Index';
    if (!url[2])
        url[2] = 'index';
    website = website + url[0] + '/' + url[1] + '/' + url[2];
    if (params) {
        params = params.join('/');
        website = website + '/' + params;
    }
    if (!rewrite) {
        website = website + '.html';
    }

    return website;
}
/**播放背景音乐
 *
 * @param file 文件路径
 */
function playsound(file) {
    
    $('embed').remove();
    $('body').append('<embed src="' + file + '" autostart="true" hidden="true" loop="false">');
}

/**
 * 友好时间
 * @param sTime
 * @param cTime
 * @returns {string}
 */
function friendlyDate(sTime, cTime) {
    var formatTime = function (num) {
        return (num < 10) ? '0' + num : num;
    };

    if (!sTime) {
        return '';
    }

    var cDate = new Date(cTime * 1000);
    var sDate = new Date(sTime * 1000);
    var dTime = cTime - sTime;
    var dDay = parseInt(cDate.getDate()) - parseInt(sDate.getDate());
    var dMonth = parseInt(cDate.getMonth() + 1) - parseInt(sDate.getMonth() + 1);
    var dYear = parseInt(cDate.getFullYear()) - parseInt(sDate.getFullYear());

    if (dTime < 60) {
        if (dTime < 10) {
            return '刚刚';
        } else {
            return parseInt(Math.floor(dTime / 10) * 10) + '秒前';
        }
    } else if (dTime < 3600) {
        return parseInt(Math.floor(dTime / 60)) + '分钟前';
    } else if (dYear === 0 && dMonth === 0 && dDay === 0) {
        return '今天' + formatTime(sDate.getHours()) + ':' + formatTime(sDate.getMinutes());
    } else if (dYear === 0) {
        return formatTime(sDate.getMonth() + 1) + '月' + formatTime(sDate.getDate()) + '日 ' + formatTime(sDate.getHours()) + ':' + formatTime(sDate.getMinutes());
    } else {
        return sDate.getFullYear() + '-' + formatTime(sDate.getMonth() + 1) + '-' + formatTime(sDate.getDate()) + ' ' + formatTime(sDate.getHours()) + ':' + formatTime(sDate.getMinutes());
    }
}




