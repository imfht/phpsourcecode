/**
 * 判断变量是否空值
 * undefined, null, '', false, 0,'0', [], {} 均返回true，否则返回false
 */
function empty(v) {
    switch (typeof v) {
        case 'undefined' :
            return true;
        case 'string'    :
            if (trim(v).length === 0 || v === '0') return true;
            break;
        case 'boolean'   :
            if (!v) return true;
            break;
        case 'number'    :
            if (0 === v) return true;
            break;
        case 'object'    :
            if (null === v) return true;
            if (undefined !== v.length && v.length === 0) return true;
            for (var k in v) {
                return false;
            }
            return true;
            break;
    }
    return false;
}

//用正则清除空格分左右
function ltrim(s) {
    return s.replace(/^(\s*|　*)/, "");
}

function rtrim(s) {
    return s.replace(/(\s*|　*)$/, "");
}

function trim(s) {
    return ltrim(rtrim(s));
}