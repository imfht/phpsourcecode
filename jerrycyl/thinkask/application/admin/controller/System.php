<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\admin\controller;
use app\common\controller\AdminBase;
use think\Db;
use think\Cache;
class System extends AdminBase {


	/*
	 * 表格导入
	 * @author rainfer <81818832@qq.com>
	 */
	public function excel_runimport(){
		if (! empty ( $_FILES ['file_stu'] ['name'] )){
			$tmp_file = $_FILES ['file_stu'] ['tmp_name'];
			$file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
			$file_type = $file_types [count ( $file_types ) - 1];
			/*判别是不是.xls文件，判别是不是excel文件*/
			if (strtolower ( $file_type ) != "xls"){
				$this->error ( '不是Excel文件，重新上传',url('excel_import'));
			}
			/*设置上传路径*/
			$savePath =ROOT_PATH. 'public/excel/';
			/*以时间来命名上传的文件*/
			$str = time ();
			$file_name = $str . "." . $file_type;
			if (! copy ( $tmp_file, $savePath . $file_name )){
				$this->error ('上传失败',url('excel_import'));
			}
			$res = read ( $savePath . $file_name );
			if (!$res){
				$this->error ('数据处理失败',url('excel_import'));
			}
			$titles=array();
			foreach ( $res as $k => $v ){
				if ($k != 1){
					$data=array();
					foreach($titles as $ColumnIndex=>$title){
						//排除主键
						if($title!='n_id'){
							$data[$title]=$v[$ColumnIndex];
						}
					}
					$result = Db::name ('news')->insert($data);
					if (!$result){
						$this->error ('导入数据库失败',url('excel_import'));
					}
				}else{
					$titles=$v;
				}
			}
			$this->success ('导入数据库成功',url('excel_import'));
		}
	}
	/*
	 * 数据导出功能
	 * @author rainfer <81818832@qq.com>
	 */
	public function excel_runexport($table){
		export2excel($table);
	}
	//清除缓存
	public function clear(){
		Cache::clear();
		$this->success ('清理缓存成功');
	}
	
	
	
}