<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\db\ActiveRecordInterface;
use yii\helpers\StringHelper;

/**
 * @var $this yii\web\View
 * @var $generator yii\gii\generators\crud\Generator
 */


$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}
$model = new $generator->modelClass;
$tableColumnInfo = $model->getTableColumnInfo();
/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use yii\data\Pagination;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else: ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends BaseController
{
	public $layout = "lte_main";

    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = <?= $modelClass ?>::find();
         $querys = Yii::$app->request->get('query');
        if(empty($querys)== false && count($querys) > 0){
            $condition = "";
            $parame = array();
            foreach($querys as $key=>$value){
                $value = trim($value);
                if(empty($value) == false){
                    $parame[":{$key}"]=$value;
                    if(empty($condition) == true){
                        $condition = " {$key}=:{$key} ";
                    }
                    else{
                        $condition = $condition . " AND {$key}=:{$key} ";
                    }
                }
            }
            if(count($parame) > 0){
                $query = $query->where($condition, $parame);
            }
        }

        $pagination = new Pagination([
            'totalCount' =>$query->count(), 
            'pageSize' => '10', 
            'pageParam'=>'page', 
            'pageSizeParam'=>'per-page']
        );
        
        $orderby = Yii::$app->request->get('orderby', '');
        if(empty($orderby) == false){
            $query = $query->orderBy($orderby);
        }
        
        
        $models = $query
        ->offset($pagination->offset)
        ->limit($pagination->limit)
        ->all();
        return $this->render('index', [
            'models'=>$models,
            'pages'=>$pagination,
            'query'=>$querys,
        ]);
    }

    /**
     * Displays a single <?= $modelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionView(<?= $actionParams ?>)
    {
        $model = $this->findModel($id);
        $data = $model->getAttributes();
        
<?php foreach($tableColumnInfo as $key=>$column){ 
      if($column['inputType'] == 'file'){
?>
		if(empty($model-><?=$key?>) == false){
            $model-><?=$key?> = \yii\helpers\Url::base(true). $model-><?=$key?>;
        }
        if(empty($data['<?=$key?>']) == false){
            $data['initialPreview'] = array(\yii\helpers\Url::base(true).$data['<?=$key?>']);
            $data['initialPreviewConfig'] = array(array('url'=>\yii\helpers\Url::toRoute('test-user/delete-file').'&id='.$data['id']));
        }
        else{
            $data['initialPreview'] = array();
            $data['initialPreviewConfig'] = array();
        }
        	
<?php }
      } ?>
        
        return $this->asJson($data);

    }

    /**
     * Creates a new <?= $modelClass ?> model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new <?= $modelClass ?>();
        if ($model->load(Yii::$app->request->post())) {
        <?php 
        echo "\n";
foreach($tableColumnInfo as $key=>$column){
    if(empty($column['defaultValue']) == false){
        $defaultValue = $column['phpType'] == 'string' ? "'{$column['defaultValue']}'" : $column['defaultValue'];
        echo "              if(empty(\$model->$key) == true){\n";
        echo "                  \$model->$key = ".$defaultValue.";\n";
        echo "              }\n";
    }
    if($column['inputType'] == 'checkbox'){
        echo "             if(empty(\$model->$key) == false && is_array(\$model->$key) == true){\n";
        echo "               \$model->$key = json_encode(\$model->$key); \n";
        echo "             }\n";
    }
    switch($key){
        case 'create_user':
            echo "              \$model->create_user = Yii::\$app->user->identity->uname;\n";
            break;
        case 'create_date':
            echo "              \$model->create_date = date('Y-m-d H:i:s');\n";
            break;
        case 'update_user':
            echo "              \$model->update_user = Yii::\$app->user->identity->uname;\n";
            break;
        case 'update_date':
            echo "              \$model->update_date = date('Y-m-d H:i:s');";
            break;
    }
}
        ?>
        
            if($model->validate() == true && $model->save()){
                $msg = array('errno'=>0, 'msg'=>'保存成功');
                return $this->asJson($msg);
            }
            else{
                $msg = array('errno'=>2, 'data'=>$model->getErrors());
                return $this->asJson($msg);
            }
        } else {
            $msg = array('errno'=>2, 'msg'=>'数据出错');
            return $this->asJson($msg);
        }
    }

    /**
     * Updates an existing <?= $modelClass ?> model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
        <?php 
            echo "\n";
            foreach($tableColumnInfo as $key=>$column){
                if(empty($column['defaultValue']) == false){
                    $defaultValue = $column['phpType'] == 'string' ? "'{$column['defaultValue']}'" : $column['defaultValue'];
                    echo "             if(empty(\$model->$key) == true){\n";
                    echo "                 \$model->$key = ".$defaultValue.";\n";
                    echo "             }\n";
                }
                if($column['inputType'] == 'checkbox'){
                    echo "             if(empty(\$model->$key) == false && is_array(\$model->$key) == true){\n";
                    echo "               \$model->$key = json_encode(\$model->$key); \n";
                    echo "             }\n";
                }
                switch($key){
                    case 'update_user':
                        echo "              \$model->update_user = Yii::\$app->user->identity->uname;\n";
                        break;
                    case 'update_date':
                        echo "              \$model->update_date = date('Y-m-d H:i:s');";
                        break;
                }
            }
        ?>
        
        
            if($model->validate() == true && $model->save()){
                $msg = array('errno'=>0, 'msg'=>'保存成功');
                return $this->asJson($msg);
            }
            else{
                $msg = array('errno'=>2, 'data'=>$model->getErrors());
                return $this->asJson($msg);
            }
        } else {
            $msg = array('errno'=>2, 'msg'=>'数据出错');
            return $this->asJson($msg);
        }
    
    }

    /**
     * Deletes an existing <?= $modelClass ?> model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionDelete(array $ids)
    {
        if(count($ids) > 0){
            $c = <?= $modelClass ?>::deleteAll(['in', 'id', $ids]);
            return $this->asJson(array('errno'=>0, 'data'=>$c, 'msg'=>json_encode($ids)));
        }
        else{
            return $this->asJson(array('errno'=>2, 'msg'=>''));
        }
    }

	<?php foreach($tableColumnInfo as $key=>$column){ 
	       if($column['inputType'] == 'file'){?>
	   	       /**
                 * 上传图片
                 */
                public function actionUploadImages(){
                    $webroot = Yii::$app->basePath.'/web';
                    $images = \yii\web\UploadedFile::getInstancesByName('<?=$key?>_file');
                    $result = array();
                    foreach($images as $file){
                        $fileName = uniqid() . '.'.$file->extension;
                        $filePath = '/resource/images/'.date('Ymd/H');
                        $savePath = $webroot.$filePath;
                        if(file_exists($savePath)  == false){
                            mkdir($savePath,0755,true);
                            chmod($savePath,0755);
                        }
                        $savePath = "$savePath/{$fileName}";
                        $file->saveAs($savePath);
                        $filePath = "$filePath/$fileName";
                        $result = array(
                            'image'=>$filePath,
                            'url'=>\yii\helpers\Url::base(true).$filePath
                        );
                    }
                    return $this->asJson($result);
                }
                
                    /**
                     * 删除图片
                     */
                    public function actionDeleteFile(){
                        $id = Yii::$app->request->get('id');
                        $model = $this->findModel($id);
                        $filePath = $model-><?=$key?>;
                        $webroot = Yii::$app->basePath.'/web';
                        $imagePath = Yii::$app->basePath.'/web/resource/images/';
                        $file = $webroot.$filePath;
                        $result = array('errno'=>0, 'msg'=>'删除成功');
                        if(strpos($filePath, $imagePath) !== false){
                            if(is_file($file) == true){
                                if(unlink($file) == false){
                                    $result = array('errno'=>2, 'msg'=>'删除失败');
                                }
                            }
                        }
                        return $this->asJson($result);
                    }
	<?php  break;
	       }
	      } ?>

	 

    /**
     * Finds the <?= $modelClass ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?=                   $modelClass ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
<?php
if (count($pks) === 1) {
    $condition = '$id';
} else {
    $condition = [];
    foreach ($pks as $pk) {
        $condition[] = "'$pk' => \$$pk";
    }
    $condition = '[' . implode(', ', $condition) . ']';
}
?>
        if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
