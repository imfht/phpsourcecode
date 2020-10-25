<?php
namespace OT\Builder;

abstract class Builder extends \Think\Controller{

	protected $_type;
	protected $_title;
	protected $_suggest;
	protected $_data = array();
	protected $_keyList = array();
	protected $_buttonList;

	public function __construct($type){
		parent::__construct();
		$this->_type = $type;
	}

	public function title($title){
		$this->_title = $title;
		$this->meta_title=$title;
		return $this;
	}

	/**
	* suggest  页面标题边上的提示信息
	* @param $suggest
	* @return $this
	*/
	public function suggest($suggest){
		$this->_suggest = $suggest;
		return $this;
	}

	/**加入一个按钮
	 * @param $title
	 * @param $attr
	 * @return $this
	 * @auth 陈一枭
	 */
	public function button($title, $attr){
		$this->_buttonList[] = array('title' => $title, 'attr' => $attr);
		return $this;
	}

	public function ajaxButton($url, $params, $title, $attr = array()){
		$attr['class'] = 'btn btn-primary ajax-post';
		$attr['url'] = $this->addUrlParam($url, $params);
		$attr['target-form'] = 'ids';
		return $this->button($title, $attr);
	}

	/**加入新增按钮
	 * @param        $href
	 * @param string $title
	 * @param array  $attr
	 * @return AdminListBuilder
	 * @auth 陈一枭
	 */
	public function buttonNew($href, $title = '新增', $attr = array()){
		$attr['href'] = $href;
		return $this->button($title, $attr);
	}


    /**加入模态弹窗按钮
     * @param $url
     * @param $params
     * @param $title
     * @param array $attr
     * @return $this
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function modalPopupButton($url, $params, $title, $attr = array()){
        //$attr中可选参数，data-title：模态框标题，target-form：要传输的数据
        $attr['modal-url'] = $this->addUrlParam($url, $params);
        $attr['data-role']='modal_popup';
        return $this->button($title, $attr);
    }

    public function buttonSetStatus($url, $status, $title, $attr){
        $attr['class'] = isset($attr['class']) ? $attr['class'] : 'btn btn-default ajax-post';
        $attr['url'] = $this->addUrlParam($url, array('status' => $status));
        $attr['target-form'] = 'ids';
        return $this->button($title, $attr);
    }

    public function buttonDisable($url = null, $title = '禁用', $attr = array()){
        if (!$url) $url = $this->_setStatusUrl;
        return $this->buttonSetStatus($url, 0, $title, $attr);
    }

    public function buttonEnable($url = null, $title = '启用', $attr = array()){
        if (!$url) $url = $this->_setStatusUrl;
        return $this->buttonSetStatus($url, 1, $title, $attr);
    }

    /**
     * 删除到回收站
     */
    public function buttonDelete($url = null, $title = '删除', $attr = array()){
        if (!$url) $url = $this->_setStatusUrl;
        $attr = array_merge($attr,array('class'=>'btn-danger ajax-post'));
        return $this->buttonSetStatus($url, -1, $title, $attr);
    }

    public function buttonRestore($url = null, $title = '还原', $attr = array()){
        if (!$url) $url = $this->_setStatusUrl;
        return $this->buttonSetStatus($url, 1, $title, $attr);
    }

    /**彻底删除回收站
     * @param null $url
     * @return $this
     * @author 陈一枭 -> 郑钟良<zzl@ourstu.com>
     */
    public function buttonClear($url=null){
        if (!$url) $url = $this->_setClearUrl;
        $attr['class'] = 'btn btn-primary ajax-post tox-confirm';
        $attr['data-confirm']='您确实要彻底删除吗？（彻底删除后不可恢复）';
        $attr['url'] = $url;
        $attr['target-form'] = 'ids';
        return $this->button('彻底删除', $attr);
    }

    public function buttonSort($href, $title = '排序', $attr = array()){
        $attr['href'] = $href;
        return $this->button($title, $attr);
    }

	public function buttonSubmit($url, $title='确定') {
		$this->savePostUrl($url);

		$attr = array();
		$attr['class'] = "sort_confirm btn btn-primary submit-btn";
		$attr['type'] = 'button';
		$attr['target-form'] = 'form-horizontal';
		return $this->button($title, $attr);
	}

	public function buttonBack($url=null, $title='返回') {
		//默认返回当前页面
		if(!$url) {
			$url = $_SERVER['HTTP_REFERER'];
		}

		//添加按钮
		$attr = array();
		$attr['href'] = $url;
		$attr['onclick'] = 'javascript: location.href=$(this).attr("href");';
		$attr['class'] = 'sort_cancel btn btn-default btn-return';
		return $this->button($title, $attr);
	}

	public function data($list){
		$this->_data = $list;
	    return $this;
	}

	public function display($template){
		if ($template) {
			$template = $template;
		}else{
			$template = T(MODULE_NAME."@Builder/".strtolower($this->_type));
		}
		parent::display($template);
	}

	protected function compileHtmlAttr($attr) {
		$result = array();
		foreach($attr as $key=>$value) {
			$value = htmlspecialchars($value);
			$result[] = "$key=\"$value\"";
		}
		$result = implode(' ', $result);
		return $result;
	}
}