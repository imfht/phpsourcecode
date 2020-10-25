<?php

namespace Student\Controller;
use Think\Page;
use Think\Controller;


/**
 * 学生管理控制器
 * @author水月居 <singliang@163.com>
 */
class IndexController extends Controller {

    /**
     * 学生管理首页
    */
    public function index(){
    	//$this->redirect('/Student/Homework/index');
    	$classModel=D('XsClass');
    	$classlist=$classModel->field('bj_code,bj_name')->select();
	    $student=D('XsStudent');
	    $map['bj_code']=I('bj_code',201001);
	    if(empty($map['bj_code'])) unset ($map['bj_code']);
	    $st_name=I('st_name','');
	    if(empty($st_name)){
	    	unset ($map['st_name']);
	      }else{
	      	$map['st_name']=array('like', '%' . $st_name . '%');
	      	$this->assign('st_name',$st_name);
	      }
          $bj_name=get_bjname('201101');
          dump($bj_name);
	    $order=array('bj_code'=>'desc','st_code'=>'asc');
	    $tlist=$student->field("id,bj_code,st_code,st_name,sex,update_time")->where($map)->order($order)
	       ->limit('0,50')->select();
	    $this->assign('thisclass',$map['bj_code']);
		$this->assign('list', $tlist);
		$this->assign('classlist',$classlist);
        $this->display();
    }

    //编辑学生信息

   public function edit(){
    $id=I('get.id');
    $homework=D('JxHomework');
     $this->cur=D('JxHomework')->find($id);
           if(IS_POST){
           //dump($_POST) ;          
          //保存表单数据                 
            $result = $homework->save(I("post."));
             if(!$result ){               
                  $this->error($homework->getError());
                }else{
               // echo '数据更新失败！';
                $this->success('更新成功', Cookie('__forward__'));               
                }              
        }
     $this->display();
   }

    //上传方法
    public function upload()
    {
        header("Content-Type:text/html;charset=utf-8");        
        $config = array(
            'maxSize'    =>   30*1024*1024, // 设置附件上传大小
            'rootPath'   => './Uploads/',
            'savePath'   =>     'Tmp/',// 设置附件上传目录
            'saveName'   =>   array('uniqid'),//iconv('utf-8', 'gbk', $save),
            'exts'       =>    array('xls', 'xlsx'),// 设置附件上传类
            'autoSub'    =>    true,
            'subName'    =>    array('date','Y'),
            );
        $upload = new \Think\Upload($config);// 实例化上传类

        // 上传文件
        $info   =   $upload->uploadOne($_FILES['excelData']);      
        $filename = './Uploads/'.$info['savepath'].$info['savename'];
        $exts = $info['ext'];
        //print_r($info);exit;
        if(!$info) {// 上传错误提示错误信息
              $this->error($upload->getError());
          }else{// 上传成功

           $this->student_import($filename, $exts);
        }
    }
 //导入数据方法
    protected function student_import($filename, $exts='xls')
    {
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        vendor("PHPExcel"); 
        //创建PHPExcel对象，注意，不能少了\ 
        $objPHPExcel = new \PHPExcel();  
        //如果excel文件后缀名为.xls，导入这个类
        if($exts == 'xls'){
            $objReader = \PHPExcel_IOFactory::createReader('Excel5'); 

        }else if($exts == 'xlsx'){
        	$objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        }
        $objReader->setReadDataOnly(true);   
        //载入文件
        $objPHPExcel = $objReader->load($filename); 
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet=$objPHPExcel->getSheet(0);
        //获取总列数
        $allColumn=$currentSheet->getHighestColumn();
        //获取总行数
        $allRow=$currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        for($currentRow=3;$currentRow<=$allRow;$currentRow++){
            //从哪列开始，A表示第一列
            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
                //读取到的数据，保存到数组$arr中
                $cell=$currentSheet->getCell($address)->getValue();                
                if($cell instanceof PHPExcel_RichText){//富文本转换字符串
                    $cell  = $cell->__toString();
                }
                $data[$currentRow][$currentColumn]=$cell;
                //print_r($cell);
            } 
        }
        
        $this->save_import($data);
    }

    //保存导入数据
    public function save_import($data)
    {
        //dump($data);exit;

        $student = D('XsStudent');
      // dump($student);exit; 
       $xsclass= D('XsClass');       
        foreach ($data as $k=>$v){
            if($k >= 3){
                $st_code=$v['B'];
                $info[$k-3]['st_code'] = $st_code;

                $st_name=$v['C'];
                $info[$k-3]['st_name'] = $st_name;

                $sex=$v['D'];
                $info[$k-3]['sex']=$sex;

                $bj_name=$v['A'];
                $stmap=array(
                	'bj_name'=>$bj_name,
                	'status'=>1,
                	);
                $bj_code = $xsclass->where($stmap)->getField('bj_code');
                //获取最后一次查询
                //dump($xsclass->_sql());
                
                //dump($bj_code);exit;
                if($bj_code){
                    $info[$k-3]['bj_code'] = $bj_code;
                }else{
                	$info[$k-3]['bj_code'] = 0;//班级代号为0表示找不到
                }

                $status=$v['E'];
                $info[$k-3]['status'] = $status;                
                $result = $student->where(array('bj_code' => $bj_code,'st_name'=>$st_name,'st_code'=>$st_code))->find();
                    
                    //

                if($result){
                	//更新操作
                	$info[$k-3]['update_time']=NOW_TIME;
                    $result = $student->where(array('bj_code' => $bj_code,'st_name'=>$st_name,'st_code'=>$st_code))
                    ->save($info[$k-3]);
                }else{
                	//入库操作   
                    // $info[$k-3]['add_time'] = NOW_TIME;
                    // $info[$k-3]['update_time']=NOW_TIME;
                    //dump($info[$k-3]);要实现自动增加自动验证需调用create函数 
                    $data=$student->create($info[$k-3]);
                   // dump($data);exit;
                    $result = $student->add($data);
                }
                //echo "当前操作";            
                //print_r($info);exit;
            }
        }

        if(false !== $result || 0 !== $result){
            $this->success('学生导入成功', 'Student/index/index');
        }else{
            $this->error('学生导入失败');
        }
        //print_r($info);

    }
        
  public function admin(){

      $this->display();
	}




  
}