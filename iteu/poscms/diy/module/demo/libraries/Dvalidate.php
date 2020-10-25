<?php



/**
 * ������У��
 */

class Dvalidate {
	
	private $ci;

	/**
     * ���캯��
     */
    public function __construct() {
		$this->ci = &get_instance();
    }

	/**
	 * ��������
	 *
	 * @param   $value	��ǰ�ֶ��ύ��ֵ
	 * @param   �Զ����ֶβ���1
	 * @param   �Զ����ֶβ���2
	 * @param   �Զ����ֶβ���3 ...
	 * @return  true��ͨ�� , falseͨ��
	 */
	public function __test($value,  $p1) {
		return TRUE;
	}
	
	/**
	 * ��֤��Ա�����Ƿ����
	 *
	 * @param   $value	��ǰ�ֶ��ύ��ֵ
	 * @param   �Զ����ֶβ���1
	 * @param   �Զ����ֶβ���2
	 * @param   �Զ����ֶβ���3 ...
	 * @return  true��ͨ�� , falseͨ��
	 */
	public function check_member($value) {
		if (!$value) return TRUE;
		return $this->ci->db->where('username', $value)->count_all_results($this->ci->db->dbprefix('member')) ? FALSE : TRUE;
	}
}