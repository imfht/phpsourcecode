<?php
if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

/**
 * 后端类别管理控制器
 *
 * @category Controllers
 * @author 　二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Category extends Admin_Controller {

    var $category_id = false;

    var $model_name='category';

    var $controller_name='category';

    function __construct() {
        parent::__construct ();
        $this->load->model ( 'category_model' );
        $this->lang->load ( $this->controller_name );
    }

    // ------------------------------------------------------------------------

    /**
     * 类别列表
     */
    function index() {

        $data ['title'] = $this->check_power ( lang ( 'category_list' ) );
        $data ['datas'] = $this->category_model->category_datas ();
        $this->load->view ( $this->config->item ( 'admin_folder' ) . 'categorys', $data );
    }

    // ------------------------------------------------------------------------

    /*
     * 类别表单
     */
    function form($id = false) {
        $this->check_power ( lang ( 'category_add_title' ) );
        $this->load->helper ( 'form' );
        $this->load->library ( 'form_validation' );

        // 获取父级下拉菜单数据
        $data ['pname'] = $this->category_model->get_pid_data ();
        $data ['id'] = '';
        $data ['name'] = '';
        $data ['rank'] = '';
        $data ['pid'] = '';
        $data ['status'] = '';
        $data ['type'] = '';

        if ($id && $id != 1) {
            $this->category_id = $id;
            $data ['title'] = lang ( 'category_edit_title' );
            $category = $this->category_model->get_one ( $this->model_name, array (
                'id' => $id
            ) );
            if (! $category) {
                $this->session->set_flashdata ( 'message', lang ( 'data_not_found' ) );
                redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
            }
            $data ['id'] = $category ['id'];
            $data ['name'] = $category ['name'];
            $data ['pid'] = $category ['pid'];
            $data ['rank'] = $category ['rank'];
            $data ['status'] = $category ['status'];
            $data ['type'] = $category ['type'];
        } else {
            $data ['title'] = lang ( 'category_add_title' );
        }
        $save ['pid'] = $this->input->post ( 'pid' );
        if (empty($save ['pid'])) {
            $save ['pid'] = 0;
            $save ['type'] = 0;
        }

        $save ['type'] = $this->input->post ( 'type' );
        if(empty($save ['type'])){
            $save ['type'] = 1;
        }

        $this->form_validation->set_rules ( 'name', 'lang:category_name', 'trim|required|alpha_chinese_dash_bias|max_length[50]|callback_check_name['.$save['pid'].']');
        $this->form_validation->set_rules ( 'rank', 'lang:category_rank', 'trim|integer' );
        $this->form_validation->set_rules ( 'pid', 'lang:category_pid', 'trim|integer' );
        $this->form_validation->set_rules ( 'type', 'lang:category_type', 'trim|integer' );
        $this->form_validation->set_rules ( 'status', 'lang:category_status', 'trim|integer|max_length[2]' );

        if ($this->form_validation->run () == FALSE) {
            if(!$id){
                $data ['name'] = $this->input->post ( 'name' );
                $data ['pid'] = $this->input->post ( 'pid' );
            }
            $this->load->view ( $this->config->item ( 'admin_folder' ) . 'category_form', $data );
        } else {
            $save ['name'] = $this->input->post ( 'name' );

            if ($id) {
                $save ['status'] = $this->input->post ( 'status' );
                $action = $this->category_model->update ( $this->model_name, $save, array (
                    'id' => $id
                ) );
                $change_str = '';
                foreach ( $save as $str ) {
                    $change_str = $change_str . '[' . $str . ']';
                }
                $this->category_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_update_data_table_span' ) . '('.$this->model_name.'),'  . lang ( 'loggin_manager_update_data_span' ) . $change_str );
                unset ( $change_str );
            } else {
                $rank = $this->db->select_max ( 'rank' )->from ( $this -> db -> dbprefix($this->model_name) )->where ( array (
                    'pid' => $save ['pid']
                ) )->get ()->row_array ();
                $save ['rank'] = $rank ['rank'] + 1;
                $save ['status']=1;
                $action = $this->category_model->insert ( $this->model_name, $save );
                $change_str = '';
                foreach ( $save as $str ) {
                    $change_str = $change_str . '[' . $str . ']';
                }
                $this->category_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_add_data_table_span' ) .'('.$this->model_name.'),'  . lang ( 'loggin_manager_add_data_span' ) . $change_str );
                unset ( $change_str );
            }

            if ($action) {
                $this->session->set_flashdata ( 'message', lang ( 'save_success' ) );
                redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
            }

            $this->session->set_flashdata ( 'error', lang ( 'save_fail' ) );
            redirect ( $this->config->item ( 'admin_folder' ) . 'category/form/' . $id, $data );
        }
    }

    // ------------------------------------------------------------------------

    /**
     * 检查名称
     */
    function check_name($str,$pid=1) {
        $name = $this->category_model->check_table_field ( $this->model_name,'name',$str, $this->category_id,array('pid'=>$pid) );

        if ($name) {
            $this->form_validation->set_message ( 'check_name', lang ( 'error_category_name_taken' ) );
            return FALSE;
        } else {
            return TRUE;
        }
    }
    // ------------------------------------------------------------------------
    /**
     * 删除类别
     */
    public function delete($id) {
        $data ['title'] = $this->check_power ( lang ( 'category_list' ) );
        if ($this->input->is_ajax_request ()) {
            if ($id = 1) {
                redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
            }
            $del_data = $this->category_model->get_one ( $this->model_name, array (
                'id' => $id
            ) );
            if ($del_data) {
                if ($del_data && ! $this->category_model->get_one ( $this->model_name, array (
                        'categoryid' => $del_data ['id']
                    ) ) && $this->category_model->delete ( $this->model_name, array (
                        'id' => $del_data ['id']
                    ) )) {
                    $this->session->set_flashdata ( 'message', lang ( 'delete_success' ) );
                    $change_str = '';
                    foreach ( $del_data as $str ) {
                        $change_str = $change_str . '[' . $str . ']';
                    }
                    $this->category_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) . '('.$this->model_name.'),'  . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
                    unset ( $change_str );
                } else {
                    $this->session->set_flashdata ( 'message', lang ( 'delete_fail' ) );
                }
            } else {
                $this->session->set_flashdata ( 'message', lang ( 'data_not_found' ) );
            }
            redirect ( $this->config->item ( 'admin_folder' ) . $this->controller_name );
        } else {
            show_404 ();
        }
    }

    // ------------------------------------------------------------------------
    /**
     * 删除类别 用于ajax
     */
    public function del() {
        $data ['title'] = $this->check_power ( lang ( 'category_list' ) );
        if ($this->input->is_ajax_request ()) {
            $del_data = $this->category_model->get_one ( $this->model_name, array (
                'id' => $this->uri->segment ( 3 )
            ) );
            if($del_data){
                if ($del_data && ! $this->category_model->get_all ( 'article', array (
                        'categoryid' => $del_data ['id']
                    ) ) && $this->category_model->delete ( $this->model_name, array (
                        'id' => $del_data ['id']
                    ) )) {
                    $msg = array (
                        'msg' => 1,
                        'info' => lang ( 'delete_success' )
                    );
                    $change_str = '';
                    foreach ( $del_data as $str ) {
                        $change_str = $change_str . '[' . $str . ']';
                    }
                    $this->category_model->save_manager_activity_logging ( $this->_manager, lang ( 'loggin_manager_delete_data_table_span' ) . '('.$this->model_name.'),' . lang ( 'loggin_manager_delete_data_span' ) . $change_str );
                    unset ( $change_str );
                } else {
                    $msg = array (
                        'msg' => 0,
                        'info' => lang ( 'delete_fail' )
                    );
                }
            }else{
                $msg = array (
                    'msg' => 2,
                    'info' => lang ( 'data_not_found' )
                );
            }
            echo json_encode ( $msg );
        } else {
            show_404 ();
        }
    }

    // ------------------------------------------------------------------------
}

// ------------------------------------------------------------------------

/* End of file category.php */
/* Location: ./app/admin/controllers/category.php */
