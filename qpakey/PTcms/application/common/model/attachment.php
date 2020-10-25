<?php

class attachmentModel extends PT_Model {

    protected $imgext = array('jpg', 'png', 'gif', 'bmp', 'jpeg');

    /**
     * 插入数据
     *
     * @param $param
     * @return mixed
     */
    public function add($param) {
        $res = $this->insert($param);
        $this->imageWater($param);
        return $res;
    }

    /**
     * 修改
     *
     * @param $param
     * @return mixed
     */
    public function edit($param) {
        $res = $this->update($param);
        $this->imageWater($param);
        return $res;
    }

    /**
     * 删除数据
     *
     * @param $where
     * @return mixed
     */
    public function del($where) {
        $info = $this->where($where)->find();
        if ($info) {
            $res = $this->where($where)->delete();
            $this->storage->remove($info['path']);
            return $res;
        }
        return false;
    }

    public function imageWater($data) {
        //如果是图片 则增加水印 todo
        if (in_array($data['ext'], $this->imgext)) {
                $image = new image($this->storage->getPath($data['path']));
            if ($this->config->get('water_type')==1){
                $image->water(PT_ROOT.'/'.$this->config->get('water_image'),$this->config->get('water_position'),$this->config->get('water_alpha'));
            }elseif($this->config->get('water_type')==2){
                $image->text($this->config->get('water_text'),PT_ROOT.'/public/font/'.$this->config->get('water_font'),$this->config->get('water_fontsize'),$this->config->get('water_color'),$this->config->get('water_position'));
            }
            $this->storage->write($data['path'], $image->save());
        }
    }

    public function getlist() {
        $list = (array)$this->select();
        foreach ($list as &$v) {
            //后台
            $v['size']=file_size_format($v['size'],2);
            $v['create_username'] = $this->model->get('user', $v['create_user_id'], 'name');
            $v['create_time'] = $v['create_time'] ? date('Y-m-d H:i', $v['create_time']) : '';
        }
        return $list;
    }


}