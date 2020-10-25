function selectFile(obj, callback)
{
    var url = $(obj).attr('data-url');
    var imgID = v.id.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
    $('#' + imgID).val(url);
    $('#ajaxModal').modal('hide');
    if($.isFunction(callback)) return callback();
}
