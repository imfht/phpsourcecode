<?php 
namespace Admin\Model;
use Think\Model;

class QuestionsModel extends Model
{
	protected $_validate = array(
		array('name', 'require', '请输入问题内容', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('options', '/{.+}/', '请配置问题选项', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('standard', 'require', '请配置标准答案', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('score', 'require', '请配置问题分数', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('score', 'number', '问题分数必须配置为正整数', self::EXISTS_VALIDATE, 'regex', self::MODEL_BOTH),
		array('questionnaire_id', 'require', '未明确的问卷', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('sort', 'number', '问题排序必须配置为正整数', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
	);

	/**
	 * 更新问题的排序
	 * @param AssocArray $sort  关联数组，键是问题问题ID，值是最新排序
	 * @return bool  成功返回true, 失败返回false
	 */
	public function sortQuestions($sort)
	{
		foreach ($sort as $questionID => $sortIndex) {
			$data = array(
				'id' => $questionID,
				'sort' => $sortIndex, 
			);

			$status = $this->save($data);
			if( $status === false )
				return false; //当前问题更新排序出错，立即返回
		}

		return true; //所有问题更新排序成功
	}
	
	/**
	 * 取得标准答案的关联数组
	 * @param string $standard  数据库中记录的标准答案字符串
	 * @param bool $isText  是否是输入文本型的问题
	 * @return 如果是文本型问题，直接返回答案字符串。如果是选项型问题，返回关联数组，键为选项号，值为空串(一般选项)或者文本(其他选项)
	 */
	public function getStandardList($standard, $isText)
	{
		if( $isText ){ //当前问题是一个文本输入型的问题
			return $standard;
		}else{
			$list = explode(',', $standard);

			foreach ($list as $value) {
				$sep = explode(':', $value);
				$key = $sep[0];
				$value = isset($sep[1]) ? $sep[1] : '';

				$standardList[$key] = $value;
			}

			return $standardList;
		}
	}

}
?>