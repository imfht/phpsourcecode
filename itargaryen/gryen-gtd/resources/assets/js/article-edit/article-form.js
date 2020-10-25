/**
 * Created by gcy77 on 2016/3/17.
 */
import 'jquery-serializejson';
import Editor from 'tui-editor';
import laravelAlert from '../helpers/alert';
import upload from '../helpers/upload';

const trArtclFrm = $('.tar-article-form');
const articleForm = trArtclFrm.find('form');
const textarea = document.querySelector('#content-textarea');

const coverInput = articleForm.find('#tCoverFile'); // 选择图片文件
const tCoverFile = articleForm.find('#tCover'); // 保存成功上传图片后的链接
const tEditCover = articleForm.find('#tEditCover'); // 封面图
const tCovrProgrs = articleForm.find('#tCovrProgrs'); // 封面图上传进度

const tTagInput = articleForm.find('#tTagInput'); // 手动输入标签的 input 组件
const tTagBox = articleForm.find('#tTagBox'); // 选中的标签存放容器
const tTags = articleForm.find('#tTags'); // 表单中要提交的标签 input
const tLblBox = articleForm.find('#tLblBox'); // 系统中的标签列表容器
const title = articleForm.find('#title');

const submitArticle = articleForm.find('#submit-article');
const saveArticle = articleForm.find('#save-article');
const unpubArticle = articleForm.find('#unpubArticle');

let tTagsArray = []; // 标签数组，用于判断标签数量和是否已经重复添加标签

const editorInstance = new Editor({
    el: textarea,
    initialEditType: 'markdown',
    previewStyle: 'tab',
    height: 'auto',
    minHeight: '600px'
});

/**
 *
 * @param {*} articleId
 */
const _getArticleContent = (articleId) => {
    $.ajax({
        type: 'GET',
        url: `/api/articles/content/${articleId}`,
        success: res => {
            editorInstance.setHtml(res.content);
        }
    });
}

/**
 * 获取 ID
 */
const _getArticleId = () => {
    const pathArr = window.location.pathname.split('/');

    return parseInt(pathArr[pathArr.length - 1], 10);
}

/**
 * 更新文章状态
 * @param {} status
 */
const _updatestatus = (status) => {
    $.post('/articles/updatestatus', { id: _getArticleId(), status}, res => {
        if (res && res.code === 200) {
            laravelAlert.show({
                type: res.type,
                message: res.message
            });

            if (res.type !== 'success') {
                return;
            }

            setTimeout(() => {
                location.reload();
            });
        } else {
            laravelAlert.show({
                type: 'danger',
                message: 'NetWork Error'
            });
        }
    });
}

/**
 * 发送提交或者保存文章请求
 * @param status
 * @private
 */
const _postArticle = () => {
    let postData = Object.assign(articleForm.serializeJSON(), {
        title: title.text(),
        content: editorInstance.getHtml()
    });

    $.ajax({
        type: 'POST',
        url: articleForm.attr('action'),
        data: postData,
        success: function (result) {
            if (result && result.code === 200) {
                laravelAlert.show({
                    type: result.type,
                    message: result.message
                });

                if (result.href) {
                    setTimeout(function () {
                        location.href = result.href;
                    }, 500);
                }
            } else {
                laravelAlert.show({
                    type: 'danger',
                    message: '没有返回或者状态码不对'
                });
            }
        },
        error: function (xhr) {
            laravelAlert.show({
                type: 'danger',
                message: xhr.responseJSON.message
            });
        }
    });
};

/**
 * 上传进度处理
 */
const _onprogress = (evt) => {
    let percent = Math.floor(100 * evt.loaded / evt.total) + '%';

    tCovrProgrs.css('width', percent);
    tCovrProgrs.find('span').html('封面图上传完成' + percent);
};

/**
 * 上传封面图成功
 * @param result
 * @private
 */
const _upCoverSuccess = (result) => {
    coverInput.val(null);
    tCovrProgrs.removeClass('active');
    tCoverFile.val(result.file_path);
    tEditCover.attr('src',  result.file_path + '?imageView2/1/interlace/1/w/800/h/400/q/95');
};

/**
 * 上传封面图失败
 * @private
 */
const _upCoverError = () => {
    coverInput.val(null);
    laravelAlert.show({
        type: 'danger',
        massage: '上传失败！'
    });
};

/**
 * 标签重新计算
 * @returns {Array}
 */
const _tTagBoxTagsToString = () => {
    let tTagsArray = [];
    let tTagBoxTags = tTagBox.find('.t-tag');

    tTagBoxTags.each((index, elem) => {
        tTagsArray.push($(elem).text());
    });

    return tTagsArray;
};

/**
 * 保存文章
 */
saveArticle.click(() => {
    _postArticle();
});

/**
 * 发表文章
 */
submitArticle.click(() => {
    _updatestatus(1);
});

/**
 * 撤回文章
 */
unpubArticle.click(() => {
    _updatestatus(0);
});

/**
 * 选择封面图触发上传操作
 */
coverInput.on('change', function () {
    let cover = coverInput.prop('files')[0];

    upload(cover, {
        paramName: 'cover',
        url: '/articles/cover/upload'
    }, _upCoverSuccess, _upCoverError, _onprogress);
});

/**
 * 点选添加标签
 */
tLblBox.find('.t-tag').on('click', function () {
    let tag = $.trim($(this).text());

    if (tTagBox.children().length > 7) {
        laravelAlert.show({
            type: 'warning',
            message: '标签最多 7 个'
        });
        return;
    }

    if ($.inArray(tag, tTagsArray) > -1) {
        laravelAlert.show({
            type: 'warning',
            message: `[${tag}]已经添加过了`
        });
        return;
    }

    tTagBox.append($(this));
    tTagsArray = _tTagBoxTagsToString();
    tTags.val(tTagsArray.join(','));
});

/**
 * 输入框添加标签
 * 删除标签
 */

tTagInput.keydown(event => {
    // 添加标签
    if (event.which === 9) {
        if (tTagInput.is(":focus") && tTagInput.val().length > 0 && tTagBox.children().length < 7) {
            let tag = $.trim(tTagInput.val());

            if ($.inArray(tTagInput.val(), tTagsArray) < 0) {
                tTagBox.append(`<span class="t-tag label label-default">${tag}</span>`);
                tTagsArray = _tTagBoxTagsToString();
                tTags.val(tTagsArray.join(','));
            } else {
                laravelAlert.show({
                    type: 'warning',
                    message: '已经添加过了'
                });
            }
        } else {
            laravelAlert.show({
                type: 'danger',
                message: '已经添加过了'
            });
        }
        tTagInput.val(null);
        return false;
    }

    // 删除标签
    if (tTagInput.val().length < 1 && event.which === 8) {
        let tags = tTagBox.children();

        if (tags.length > 0) {
            let lastChild = tags.eq(tags.length - 1);

            if (lastChild.hasClass('t-tag')) {
                tLblBox.append(lastChild);
            } else {
                tags.eq(tags.length - 1).remove();
            }
            tTagsArray = _tTagBoxTagsToString();
            tTags.val(tTagsArray.join(','));
        } else {
            laravelAlert.show({
                type: 'warning',
                message: '没有标签'
            });
        }
        return false;
    }
});

$(() => {
    $('.navbar-default').fadeOut();
    const articleId = _getArticleId();

    if (articleId) {
        _getArticleContent(articleId);
    }
});
