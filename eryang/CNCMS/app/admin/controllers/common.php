<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * 公共控制器
 *
 * @category core
 * @author 二　阳°(QQ:707069100)
 * @link http://weibo.com/513778937?topnav=1&wvr=5
 */
class Common extends Admin_Controller {
	function __construct() {
		parent::__construct ();
	}
	// ------------------------------------------------------------------------
	function index() {
		exit ( '非法错误' );
	}
	// ------------------------------------------------------------------------
	
	/**
	 * 在线编辑器文件上传../
	 */
	function editor_upload() {
        $manager = $this -> auth -> CI -> manager_session -> userdata('manager');
        if(!$manager){
            $result = array('error'=>1,'message'=>lang('nopur'));
            echo json_encode($result);
            exit;
        }
		// require_once '..'.SITE_ADMIN_EDITOR . '/php/upload_json.php';
		require_once dirname ( __FILE__ ) . '/../upload/upload_json.php';
	}
	// ------------------------------------------------------------------------
	
	/**
	 * 在线编辑器文件管理
	 */
	function editor_manager() {
        $manager = $this -> auth -> CI -> manager_session -> userdata('manager');
        if(!$manager){
            $result = array('error'=>1,'message'=>lang('nopur'));
            echo json_encode($result);
            exit;
        }
		// require_once '..'.SITE_ADMIN_EDITOR . '/php/file_manager_json.php';
		require_once dirname ( __FILE__ ) . '/../upload/file_manager_json.php';
	}

	// ------------------------------------------------------------------------


    /**
     * 上传
     */
    function upload(){
        $manager = $this -> auth -> CI -> manager_session -> userdata('manager');
        if(!$manager){
            $result = array('error'=>1,'message'=>lang('nopur'));
            echo json_encode($result);
            exit;
        }
        $save_path ='..'. SITE_ADMIN_UPLOADS . '/';//文件上传路径

        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'rm', 'rmvb'),
            'file' => array('gif', 'jpg', 'jpeg', 'png', 'bmp','doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
        );
        //$attrconfig = $this->Cache_model->loadConfig('attr');
        $dir_name = $this->input->get('dir');
        if(!in_array($dir_name,array('image','flash','media','file'))){
            $result = array('error'=>1,'message'=>"Invalid Directory name.");
            echo json_encode($result);
        }
        $save_path .= $dir_name.'/';
        if (!file_exists($save_path)) {
            mkdir($save_path);
        }
        $save_path .= date('Ymd').'/';
        if (!file_exists($save_path)) {
            mkdir($save_path);
        }
        $uploadconfig['upload_path'] = $save_path;
        $uploadconfig['allowed_types'] = implode('|',$ext_arr[$dir_name]);
        $uploadconfig['max_size'] = SITE_ADMIN_UPLOAD_IMAGE_SIZE?SITE_ADMIN_UPLOAD_IMAGE_SIZE:1;
        $uploadconfig['encrypt_name']  = TRUE;
        $uploadconfig['remove_spaces']  = TRUE;
        $this->load->library('upload', $uploadconfig);
        if(!$this->upload->do_upload('imgFile')){
            $result = array('error'=>1,'message'=>$this->upload->display_errors('',''));
        }else{
            $data = $this->upload->data();
//            if($this->input->post('iswater')==1&&$dir_name=='image'&&$attrconfig['water_type']>0){
//                $this->load->library('image_lib');
//                $waterconfig['source_image'] = $save_path.$data['file_name'];
//                $waterconfig['quality'] = $attrconfig['water_quality'];
//                $waterconfig['wm_padding'] = $attrconfig['water_padding'];
//
//                switch($attrconfig['water_position']){
//                    case 'topleft':
//                        $waterconfig['wm_vrt_alignment'] = 'top';
//                        $waterconfig['wm_hor_alignment'] = 'left';
//                        break;
//                    case 'topcenter':
//                        $waterconfig['wm_vrt_alignment'] = 'top';
//                        $waterconfig['wm_hor_alignment'] = 'center';
//                        break;
//                    case 'topright':
//                        $waterconfig['wm_vrt_alignment'] = 'top';
//                        $waterconfig['wm_hor_alignment'] = 'right';
//                        break;
//                    case 'middleleft':
//                        $waterconfig['wm_vrt_alignment'] = 'middle';
//                        $waterconfig['wm_hor_alignment'] = 'left';
//                        break;
//                    case 'middlecenter':
//                        $waterconfig['wm_vrt_alignment'] = 'middle';
//                        $waterconfig['wm_hor_alignment'] = 'center';
//                        break;
//                    case 'middleright':
//                        $waterconfig['wm_vrt_alignment'] = 'middle';
//                        $waterconfig['wm_hor_alignment'] = 'right';
//                        break;
//                    case 'bottomleft':
//                        $waterconfig['wm_vrt_alignment'] = 'bottom';
//                        $waterconfig['wm_hor_alignment'] = 'left';
//                        break;
//                    case 'bottomcenter':
//                        $waterconfig['wm_vrt_alignment'] = 'bottom';
//                        $waterconfig['wm_hor_alignment'] = 'center';
//                        break;
//                    case 'bottomright':
//                        $waterconfig['wm_vrt_alignment'] = 'bottom';
//                        $waterconfig['wm_hor_alignment'] = 'right';
//                        break;
//                    default:
//                        $waterconfig['wm_vrt_alignment'] = 'bottom';
//                        $waterconfig['wm_hor_alignment'] = 'right';
//                        break;
//                }
//                if($attrconfig['water_type']==1){
//                    $waterconfig['wm_type'] = 'overlay';
//                    $waterconfig['wm_overlay_path'] = $attrconfig['water_image_path'];
//                    $waterconfig['wm_opacity'] = $attrconfig['water_opacity'];
//                }elseif($attrconfig['water_type']==2){
//                    $waterconfig['wm_type'] = 'text';
//                    $waterconfig['wm_text'] = $attrconfig['water_text_value'];
//                    $waterconfig['wm_font_path'] = $attrconfig['water_text_font'];
//                    $waterconfig['wm_font_size'] = $attrconfig['water_text_size'];
//                    $waterconfig['wm_font_color'] = $attrconfig['water_text_color'];
//                }
//                $this->image_lib->initialize($waterconfig);
//                $this->image_lib->watermark();
//            }
            $result = array('error'=>0,'url'=>base_url($save_path.$data['file_name']));
        }
        echo json_encode($result);
    }
    // ------------------------------------------------------------------------
}
// ------------------------------------------------------------------------

/* End of file common.php */
/* Location: ../app/admin/controllers/common.php */
