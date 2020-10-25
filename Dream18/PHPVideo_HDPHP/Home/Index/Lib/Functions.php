<?php
/**
 * 对字段数据处理
 * a) 规范链接
 * b) 图片地址等
 * @param $field 文章数据
 * @return array
 */
function content_field($field)
{
    $cache = S('field' . $field['mid']);
    foreach ($field as $name => $value) {
        if (!isset($cache[$name])) {
            continue;
        }
        switch ($cache[$name]['field_type']) {
            case 'thumb':
                $field[$name] = $field[$name] ? __ROOT__ . '/' . $field[$name] : __ROOT__ . '/Home/Static/image/thumb.jpg';
                break;
            case 'image':
                $field[$name] = $field[$name] ? __ROOT__ . '/' . $field[$name] : '';
                break;
            case 'images':
                $images = unserialize($field[$name]);
                if (is_array($images)) {
                    foreach ($images as $id => $data) {
                        $images[$id]['url'] = __ROOT__ . '/' . $data['path'];
                    }
                }
                $field[$name] = $images;
                break;
            case 'files':
                $files = unserialize($field[$name]);
                if (is_array($files)) {
                    foreach ($files as $id => $data) {
                        if (!empty($data['path'])) {
                            $pathinfo=pathinfo(basename($data['path']));
                            $files[$id]['url'] =U("Index/Download/download",array('cid'=>$field['cid'],'filename'=>$pathinfo['filename']));
                        }
                    }
                }
                $field[$name] = $files;
                break;
        }
    }
    //头像
    if (empty($field['icon'])) {
        $field['icon'] = __STATIC__ . "/image/user.png";
    }
    //URL地址
    $field['url'] = Url::content($field);
    //栏目图片
    if (empty($field['catimage'])) {
        $field['catimage'] = __ROOT__ . '/' . $field['catimage'];
    }
    //栏目url
    $field['caturl'] = Url::category($field);
    //发表时间
    $field['time'] = date("Y-m-d", $field['addtime']);
    //多久前发表
    $field['date_before'] = date_before($field['addtime']);
    return $field;
}