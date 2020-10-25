/*
 * @Author:Tommy
 * @Description: 社会化分享
 */

function socialShare(type = 'qzone',url = '',title = '',content = '',pic = '')
{
    var _href;
    switch (type) {
        case 'qzone':
            _href = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + url + '&title=' + title + '&pics=' + pic + '&summary=' + content;
            break;
        case 'sina':
            _href = 'http://service.weibo.com/share/share.php?url=' + url + '&title=' + title + '&pic=' + pic + '&searchPic=false';
            break;
        case 'qq':
            _href = 'http://share.v.t.qq.com/index.php?c=share&a=index&url=' + url + '&title=' + title + '&appkey=801cf76d3cfc44ada52ec13114e84a96';
            break;
        case 'douban':
            _href = 'http://www.douban.com/share/service?href=' + url + '&name=' + title + '&text=' + content + '&image=' + pic;
            break;
        case 'weixin':
            _href = 'http://qr.liantu.com/api.php?text=' + url;
            break;
        default:
            _href = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + url + '&title=' + title + '&pics=' + pic + '&summary=' + content;
    }
    window.location.href = _href;
}
