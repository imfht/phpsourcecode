<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\controller\front;

use think\facade\Db;
use \app\model\Form as FormModel;
use \app\model\FormAttr;

class Form extends Base {

	/**
	 * @title 表单页
	 * @return [type] [description]
	 */
	public function index() {
		if ($this->request->isAjax()) {
			$data = $this->request->post();
			$id = $this->request->param('id');

			$info = FormModel::where('id', $id)->find();

			$result = Db::name('Form' . ucfirst($info['name']))->save($data);
			if (false !== $result) {
				$url = (isset($info['redirect_url']) && $info['redirect_url']) ? $info['redirect_url'] : '';
				return $this->success("提交成功！", $url);
			} else {
				return $this->error("提交失败！");
			}
		} else {
			$id = $this->request->param('id');
			$name = $this->request->param('name');

			$map = [];

			$map[] = ['id', '=', $id];

			$info = FormModel::where($map)->find();
			$attr = FormAttr::where('form_id', $info['id'])->select();

			if ($info['relation'] && strpos($info['relation'], ":")) {
				list($model, $relation) = explode(":", $info['relation']);
				$relation = explode(",", $relation);
				if (is_array($relation)) {
					$rmap['id'] = ['IN', $relation];
				} elseif (is_string($relation)) {
					$rmap['id'] = $relation;
				}
				$info['relation_list'] = Db::name(ucfirst($model))->where($rmap)->order('sort desc, id asc')->select();
			}

			$this->data = [
				'info' => $info,
				'attr' => $attr,
			];
			return $this->fetch();
		}
	}

}
