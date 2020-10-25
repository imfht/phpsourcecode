<?php
namespace App\Model;

use Swoole;

class City extends Swoole\Model
{

    public $table = 'city';

    public $primary = 'id';

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '城市名称',
            'imgs' => '图片',
            'desc' => '描述',
            'code' => '邮编',
            'ctime' => '创建时间',
        ];
    }

    public function search($param)
    {
        return $this->gets($param);
    }

    public function create($data)
    {
        return $this->put($data);
    }

    public function update($id, $data)
    {
        return $this->set($id, $data);
    }

    public function delete($param)
    {
        return $this->dels($param);
    }

    public function getPage($param)
    {
        $list = $this->gets($param);
        if ($list) {
            foreach ($list as &$one) {
                //$one['type_name'] = self::$typeMap[$one['type']];
                $one['ctime'] = date('Y-m-d H:i:s', $one['ctime']);
            }
        }
        return $list;
    }

    public function getCityForSelect()
    {
        $where['status'] = 1;
        $list = $this->gets($where);
        foreach ($list as $item) {
            $result[$item['id']] = $item['name'];
        }
        return $result;
    }

    public function getData($old = null)
    {
        $data = [];
        $att = $this->attributeLabels();
        foreach ($att as $key => $value) {
            if (getRequest($key)) {
                $data[$key] = getRequest($key);
            }
        }
        //上传图片
        if (!empty($_FILES)) {
            $imgs = [];
            $uploadPath = WEBPATH . "/local/";
            $num = count($_FILES['imgs']['name']);
            for ($i = 0; $i < $num; $i++) {
                $path = $_FILES['imgs']['tmp_name'][$i];
                if (!$path) {
                    continue;
                }
                $file_name = md5($path . time()) . $_FILES['imgs']['name'][$i];
                if (!file_exists($uploadPath . $file_name)) {
                    move_uploaded_file($path, $uploadPath . $file_name);
                    $imgs[] = $file_name;
                }
            }
            if (!empty($imgs)) {
                $data['imgs'] = json_encode($imgs);
            } else {
                $data['imgs'] = $old['imgs'];
            }
        }
        return $data;
    }

    public function getByIds($ids)
    {
        $result = [];
        $where['in'] = ['id', implode(',', $ids)];
        $list = $this->gets($where);
        if ($list) {
            foreach ($list as $item) {
                $result[$item['id']] = $item['name'];
            }
        }
        return $result;
    }
}	
