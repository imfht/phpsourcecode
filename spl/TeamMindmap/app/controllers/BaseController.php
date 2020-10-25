<?php

use \Illuminate\Database\Eloquent\Model;

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    /**
     * 提取模型实例中的指定数据库字段或模型中的关系.
     *
     * 注意，关系会被转化为关联数组
     *
     * @param Model $model  待查询的模型实例
     * @param array $keys 待查询的数据库字段名
     * @return array 返回的结果关联数组
     *
     * 示例：
     *  $currTask = ProjectTask::findTaskInProjectOrFail($project_id, $id);
     *  $rep = $this->modelRelationsToArray($currTask, ['name', 'creater'] );
     *
     *  则$rep的形式为：
     *
     *  array (size=2)
     *    'name' => string 'task one' (length=8)
     *    'creater' =>
     *      array (size=7)
     *       'id' => int 1
     *       'username' => string 'admin' (length=5)
     *       'email' => string 'admin@example.com' (length=17)
     *       'description' => null
     *       'head_image' => null
     *       'created_at' => string '2014-11-20 00:00:00' (length=19)
     *       'updated_at' => string '2014-11-20 01:47:00' (length=19)
     *
     */
    public function getSectionalValuesFromModel(Model $model, array $keys)
    {
        $rep = [];

        foreach($keys as $currentKey){
            $rep[ $currentKey ] = $model->getAttribute( $currentKey );
            if( $rep[ $currentKey ] instanceof \Illuminate\Support\Contracts\ArrayableInterface ){

                $rep[ $currentKey ] = $rep[ $currentKey ]->toArray();
            }
        }

        return $rep;
    }

    /**
     * 提取Validator实例中的错误信息，将其转换为字符串形式以方便发送给前端.
     *
     * @param \Illuminate\Support\MessageBag $messages 错误信息的实例
     * @param null $keys 指定所提取的字段信息，如不指定则返回全部
     * @param string $separator 信息的分隔符
     * @return string
     */
    public function changeValidatorMessageToString(\Illuminate\Support\MessageBag $messages, $keys = null, $separator = ',')
    {
        $resp = '';
        $messagesArray = $messages->toArray();

        if( ! is_array($keys) ){
            $keys = array_keys($messagesArray);
        }

        foreach($keys as $currentKey){
            $resp .= implode($separator, $messagesArray[$currentKey] ). ' '. $separator. ' ';
        }

        return rtrim($resp, $separator.' ');
    }

}
