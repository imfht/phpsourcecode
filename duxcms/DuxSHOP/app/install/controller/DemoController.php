<?php

namespace app\install\controller;

ignore_user_abort(true);
set_time_limit(1800);


use QL\QueryList;

/**
 * 演示数据
 */
class DemoController extends \dux\kernel\Controller {

    public function classData() {
        return ROOT_PATH . '/app/install/data/class.json';
    }

    public function contentData() {
        return ROOT_PATH . '/app/install/data/content.json';
    }

    public function index() {

        //$urlBase = 'https://youpin.mi.com';

        //栏目采集
        $classJson = QueryList::get('http://you.163.com/xhr/globalinfo//queryTop.json?csrf_token=f8ad69f4b61012a6d581f88e450afdd2&__timestamp=1526527243691')->getHtml();

        $classJson = json_decode($classJson, true);

        $classData = $classJson['data']['cateList'];


        $classArray = [];

        $parentClassData = [];

        foreach ($classData as $key => $vo) {
            $class = [
                'id' => $vo['id'],
                'parent_id' => 0,
                'name' => $vo['name'],
                'image' => $vo['bannerUrl'],
            ];
            $classArray[] = $class;
            $parentClassData[] = $class;
            foreach ($vo['subCateList'] as $v) {
                $classArray[] = [
                    'id' => $v['id'],
                    'parent_id' => $v['superCategoryId'],
                    'name' => $v['name'],
                    'image' => $v['bannerUrl'],
                ];
            }
        }

        file_put_contents($this->classData(), json_encode($classArray));


        //标题采集
        $contentData = [];
        $titleUn = [];
        $i = 0;
        foreach ($parentClassData as $key => $vo) {
            $url = 'you.163.com/item/list?categoryId=' . $vo['id'];
            $html = QueryList::get($url)->getHtml();

            preg_match("/var json_Data=([\s\S]*?)\<\/script\>/i", $html, $matches);

            $matches[1] = str_replace(';', '', $matches[1]);

            $data = json_decode($matches[1], true);

            foreach ($data['categoryItemList'] as $k => $item) {
                if($k > 5) {
                    break;
                }

                foreach ($item['itemList'] as $v) {

                    if(in_array($v['name'], $titleUn)) {
                        continue;
                    }

                    $contentData[] = [
                        'id' => $v['id'],
                        'name' => $v['name'],
                        'image' => $v['listPicUrl'],
                        'class_id' => $item['category']['id']

                    ];
                    $titleUn[] = $v['name'];

                    $i++;

                    file_put_contents(ROOT_PATH . '/app/install/data/title.log', $i);
                }

            }
        }


        //内容采集
        $xx = 0;
        foreach ($contentData as $key => $vo) {
            $xx++;
            $url = 'http://you.163.com/item/detail?id=' . $vo['id'];
            $html = QueryList::get($url)->getHtml();
            preg_match("/var JSON_DATA_FROMFTL = ([\s\S]*?)\}\;/i", $html, $matches);
            $json = json_decode($matches[1].'}', true);

            $contentData[$key]['keywords'] = $json['item']['simpleDesc'];
            $contentData[$key]['time'] = date('Y-m-d H:i:s');
            $contentData[$key]['content'] = html_in($json['item']['itemDetail']['detailHtml']);
            $contentData[$key]['unit'] = $json['item']['pieceUnitDesc'];



            $contentData[$key]['images'] = [[
                'url' => [$json['item']['itemDetail']['picUrl1'], $json['item']['itemDetail']['picUrl2'], $json['item']['itemDetail']['picUrl3'], $json['item']['itemDetail']['picUrl4']]
            ]];

            $skuData = [];
            foreach ($json['item']['skuList'] as $k => $v) {
                $skuData['id'][$k] = '';
                $skuData['spec'][$k] = [];
                foreach ($v['specList'] as $i => $value) {
                    $skuData['spec'][$k][] = json_encode([
                        'id' => $i,
                        'name' => $value['specName'],
                        'value' => $value['specValue']
                    ]);
                }
                $skuData['goods_no'][$k] = $v['id'];
                $skuData['barcode'][$k] = $v['id'];
                $skuData['sell_price'][$k] = $v['calcPrice'];
                $skuData['market_price'][$k] = $v['counterPrice'];
                $skuData['cost_price'][$k] = $v['calcPrice'];
                $skuData['store'][$k] = 500;
                $skuData['weight'][$k] = 1000;
            }

            $contentData[$key]['sku'] = $skuData;



            file_put_contents(ROOT_PATH . '/app/install/data/content.log', $xx);
        }
        file_put_contents($this->contentData(), json_encode($contentData));

        echo '演示数据采集完毕！';
    }

    public function test() {
        $class = json_decode(file_get_contents($this->classData()), true);
        $content = json_decode(file_get_contents($this->contentData()), true);


        $data = [];
        foreach ($class as $key => $vo) {
            $data[$vo['id']] = $vo;
        }

        $class = $data;

        $classData = [];
        $subData = [];
        foreach ($class as $key => $vo) {
            if(!$vo['parent_id']) {
                $classData[$key] = $vo;
            }else {
                $subData[$key] = $vo;
            }
        }

        foreach ($subData as $vo) {
            $classData[$vo['parent_id']]['subClass'][] = $vo;
        }


        foreach ($classData as $key => $vo) {
            $_POST = [
                'name' => $vo['name'],
                'image' => $vo['image']
            ];
            $classId = target('mall/MallClass')->saveData('add');
            if(!$classId) {
                echo target('mall/MallClass')->getError();
                exit;
            }
            $class[$vo['id']]['class_id'] = $classId;
            foreach ($vo['subClass'] as $k => $v) {
                $_POST = [
                    'name' => $v['name'],
                    'image' => $v['image'],
                    'parent_id' => $classId
                ];
                $subId = target('mall/MallClass')->saveData('add');
                if(!$subId) {
                    echo target('mall/MallClass')->getError();
                    exit;
                }
                $class[$v['id']]['class_id'] = $subId;
            }
        }


        foreach ($content as $key => $vo) {
            $_POST = [
                'title' => $vo['name'],
                'class_id' => $class[$vo['class_id']]['class_id'],
                'images' => $vo['images'][0],
                'keyword' => $vo['keywords'],
                'content' => $vo['content'],
                'unit' => $vo['unit'],
                'create_time' => $vo['time'],
                'data' => $vo['sku'],
                'status' => 1
            ];
            $status = target('mall/Mall')->saveData('add');
            if(!$status) {
                echo target('mall/Mall')->getError();
                exit;
            }
        }

        echo '演示数据导入成功！';
    }

    public function xml_encode($data, $encoding = 'utf-8', $root = 'demo') {
        $xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
        $xml .= '<' . $root . '>';
        $xml .= $this->data_to_xml($data);
        $xml .= '</' . $root . '>';
        return $xml;
    }

    public function data_to_xml($data) {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml .= "<$key>";
            $xml .= (is_array($val) || is_object($val)) ? $this->data_to_xml($val) : $val;
            list($key,) = explode(' ', $key);
            $xml .= "</$key>";
        }
        return $xml;
    }

}