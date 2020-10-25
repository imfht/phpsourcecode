const CIGO_EDIT_TYPE_INPUT = '10';//输入框
const CIGO_EDIT_TYPE_INPUT_MULTI = '11';//多个输入框
const CIGO_EDIT_TYPE_TEXTAREA = '12';//多行文本输入框
const CIGO_EDIT_TYPE_DATE_TIME_PICKER = '20';//日期时间
const CIGO_EDIT_TYPE_RADIO_LANDSCAPE = '31';//横向单选
const CIGO_EDIT_TYPE_RADIO_PORTRAIT = '32';//纵向单选
const CIGO_EDIT_TYPE_CHECKBOX_LANDSCAPE = '33';//横向多选
const CIGO_EDIT_TYPE_CHECKBOX_PORTRAIT = '34';//纵向多选
const CIGO_EDIT_TYPE_SELECTOR = '40';//纵向多选
const CIGO_EDIT_TYPE_IMG_SINGLE = '50';//单图上传
const CIGO_EDIT_TYPE_IMG_MULTI = '51';//多图上传
const CIGO_EDIT_TYPE_IMG_SHOW = '52';//图片橱窗
const CIGO_EDIT_TYPE_EDITOR_UEDITOR = '60';//百度编辑器
const CIGO_EDIT_TYPE_EDITOR_CK = '61';//CK编辑器

function cigoEditCreate(pNode) {
    ((undefined === pNode) || ('' === pNode))
        ? pNode = $('body')
        : (
            (pNode instanceof jQuery)
                ? false
                : pNode = $(pNode)
        );

    pNode.find('.cigo-edit-create').each(function () {
        var createConfig = $(this).attr('cigo-edit-create-config');
        if (createConfig != undefined && createConfig != '') {
            createConfig = eval('(' + createConfig + ')');
            switch (createConfig['edit_type']) {
                case CIGO_EDIT_TYPE_INPUT:
                    cigoEditInputCreate($(this), createConfig);
                    break;
                case CIGO_EDIT_TYPE_INPUT_MULTI:
                    cigoEditInputMulti($(this), createConfig);
                    break;
                case CIGO_EDIT_TYPE_TEXTAREA:
                    break;
                case CIGO_EDIT_TYPE_DATE_TIME_PICKER:
                    break;
                case CIGO_EDIT_TYPE_RADIO_LANDSCAPE:
                    break;
                case CIGO_EDIT_TYPE_RADIO_PORTRAIT:
                    break;
                case CIGO_EDIT_TYPE_CHECKBOX_LANDSCAPE:
                    break;
                case CIGO_EDIT_TYPE_CHECKBOX_PORTRAIT:
                    break;
                case CIGO_EDIT_TYPE_SELECTOR:
                    break;
                case CIGO_EDIT_TYPE_IMG_SINGLE:
                    break;
                case CIGO_EDIT_TYPE_IMG_MULTI:
                    break;
                case CIGO_EDIT_TYPE_IMG_SHOW:
                    break;
                case CIGO_EDIT_TYPE_EDITOR_UEDITOR:
                    break;
                case CIGO_EDIT_TYPE_EDITOR_CK:
                    break;
            }
        }
    });
}

function cigoEditInputCreate(node, createConfig) {
    var content = new Array();
    var viewId = getRandStr() + (new Date().getTime());

    content.push(
        '<div id="' + viewId + '" ' +
        '   class="cigo-edit item-input" ' +
        '   cigo-edit-label="' + createConfig['label'] + '" ' +
        '   cigo-edit-type="text" ' +
        '   cigo-edit-class="form-control" ' +
        '   cigo-edit-style="width:260px;"\n' +
        '   cigo-edit-name="' + createConfig['flag'] + '" ' +
        '   cigo-edit-value="' + createConfig['value'] + '" ' +
        '   cigo-edit-placeholder="' + createConfig['place_holder'] + '" ' +
        '   cigo-edit-helpblock="' + createConfig['help_block'] + '" ' +
        '></div>'
    );

    node.html(content.join(''));
    cigoEditCreateInput($('#' + viewId));
}

function cigoEditInputMulti(node, createConfig) {
    var content = new Array();
    var viewId = getRandStr() + (new Date().getTime());

    content.push(
        '<div id="' + viewId + '" ' +
        '   class="cigo-edit item-input" ' +
        '   cigo-edit-label="' + createConfig['label'] + '" ' +
        '   cigo-edit-type="text" ' +
        '   cigo-edit-class="form-control" ' +
        '   cigo-edit-style="width:260px;"\n' +
        '   cigo-edit-name="' + createConfig['flag'] + '" ' +
        '   cigo-edit-value="' + createConfig['value'] + '" ' +
        '   cigo-edit-placeholder="' + createConfig['place_holder'] + '" ' +
        '   cigo-edit-helpblock="' + createConfig['help_block'] + '" ' +
        '></div>'
    );

    node.html(content.join(''));
    cigoEditCreateInput($('#' + viewId));
}

