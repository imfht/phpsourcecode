<?php

namespace App\Http\Controllers;

use DB;
use Storage;
use Hash;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request AS UploadRequest;

class ApiController extends BaseController
{
    /**
     * @api {get} /:table/config GetTableConfig
     * @apiVersion 0.2.0
     * @apiName GetTableConfig
     * @apiGroup common
     * @apiPermission admin
     *
     * @apiDescription 可获取任意数据表的配置信息
     *
     * @apiParamExample {json} Request-Example:
     *     {   
     *     }
     *
     * @apiSuccess {Boolean} error 是否错误.
     * @apiSuccess {Object} result 配置数据.
     * @apiSuccess {Object} result.base 表基础配置信息.
     * @apiSuccess {String} result.base.name 表名.
     * @apiSuccess {String} result.base.comment 表注释.
     * @apiSuccess {Object[]} result.fields 各字段配置信息(Array of Objects).
     * @apiSuccess {String} result.fields.name 字段名.
     * @apiSuccess {String} result.fields.type 字段类型.
     * @apiSuccess {String} message 提示消息.
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *     }
     *
     * @apiError TableNotFound 表不存在.
     *
     * @apiErrorExample Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *     }
     *
     * @apiSampleRequest http://192.168.1.120:82/api/im_inform/config
     */
    public function config($table)
    {
        $result = $this::tableConfig($table);
        return $this::jsonResponse(false,$result);
    }


    /**
     * @api {get} /:table GetIndex
     * @apiVersion 0.2.0
     * @apiName GetIndex
     * @apiGroup common
     * @apiPermission admin
     *
     * @apiDescription 可获取任意数据表的列表信息
     *
     * @apiParam {Number} currentPage 当前页数.
     * @apiParam {Number} countPerPage 每页条数.
     * @apiParam {Boolean} no_relate 是否查找关联数据.
     * @apiParam {String} filter_str SQL过滤字符串.
     * @apiParam {Object[]} filter 过滤数组(Array of Objects).
     * @apiParam {String} filter.0 过滤字段.
     * @apiParam {String} filter.1 过滤符号.
     * @apiParam {String} filter.2 过滤值.
     *
     * @apiSuccess {Boolean} error 是否错误.
     * @apiSuccess {Object} result 配置数据.
     * @apiSuccess {Number} result.count 总条数.
     * @apiSuccess {Number} result.currentPage 当前页数.
     * @apiSuccess {Number} result.countPerPage 每页条数.
     * @apiSuccess {Number} result.lastPage 最后一页页码.
     * @apiSuccess {Object[]} result.data 列表数据(Array of Objects).
     * @apiSuccess {Object} result.data.title 某字段值
     * @apiSuccess {String} message 提示消息.
     *
     * @apiError TableNotFound 表不存在.
     * @apiSampleRequest http://192.168.1.120:82/api/im_inform
     */
    public function index(Request $request,$table)
    {
        $data           = $request->all();
        $configResult   = $this::tableConfig($table);
        
        $filedStr   = ''; //字段过滤
        $relateStr  = ''; //表关联
        $filterStr  = ''; //内容过滤
        $orderStr   = ''; //排序规则

        //字段过滤 表关联
        foreach ($configResult['fields'] as $field) {
            $fieldName = $field['name'];
            if($field['in_list'] || $field['key'] == "PRI"){          
                $filedStr   .= "$table.$fieldName,"; 
            }
            $sameTableNum = 1;
            if(isset($field['relate']) && !isset($data['no_relate'])){
                $relateTable    = $field['relate']['table'];
                $relateField    = $field['relate']['field'];
                $relateSelect   = $field['relate']['select'];
                
                $relateTableOrigin = $relateTable;
                if($relateTable == $table){
                    $relateTable = $table.$sameTableNum++;
                }
                foreach ($relateSelect as $relateSelectField) {
                    $filedStr   .= "$relateTable.$relateSelectField AS ".$relateTableOrigin."_$relateSelectField,";
                }
                $relateStr  .= "LEFT JOIN $relateTableOrigin AS $relateTable ON $relateTable.$relateField = $table.$fieldName ";
            }
        }
        $filedStr  = substr($filedStr, 0, -1);

        //内容过滤
        if(isset($data['filter'])){
            foreach ($data['filter'] as $filter) {
                $filterField  = $filter[0];
                $filterSignal = $filter[1];
                $filterValue  = $filter[2];
                if($filterSignal == 'LIKE'){
                    $filterStr .= "$table.$filterField $filterSignal '%$filterValue%' AND ";
                }else{
                    $filterStr .= "$table.$filterField $filterSignal '$filterValue' AND ";
                }
            }
        }
        $filterStr = $filterStr==''?'1':substr($filterStr, 0, -5);
        $filterStr .= isset($data["filter_str"])?" AND ".$data["filter_str"]:"";

        //分页
        $currentPage    = isset($data["currentPage"])?$data["currentPage"]:1;
        $countPerPage   = isset($data["countPerPage"])?$data["countPerPage"]:10;
        $countResult    = DB::select("select count(*) as count from $table where $filterStr");
        $count          = $countResult[0]->count;
        $lastPage       = ceil($count/$countPerPage);
        $currentPage    = $currentPage>isset($lastPage)?$lastPage:$currentPage;
        $result = [
            'countPerPage'  => $countPerPage,
            'count'         => $count,
            'lastPage'      => $lastPage,
            'currentPage'   => $currentPage
        ];
        $numStart       = $countPerPage*($currentPage-1);

        //排序规则
        if(isset($data['sortField'])){
            $field = $data['sortField'];
            $order = $data['sortValue'] == 'true' ? 'DESC' : 'ASC';
            $orderStr .= " ORDER BY $field $order";
        }

        //执行
        $result['data'] = DB::select("

            SELECT $filedStr FROM $table
            $relateStr
            WHERE $filterStr
            $orderStr
            LIMIT $numStart,$countPerPage
            
        ");

        //值转换
        foreach ($configResult['fields'] as $field) {
            $fieldName = $field['name'];
            if($field['in_list']){
                if($field["type"] == "boolean"){          
                    foreach ($result['data'] as $key => $value){
                        $result['data'][$key]->$fieldName = $value->$fieldName?true:false;
                    }
                }
            }
        }

        return $this::jsonResponse(false,$result);
    }


    /**
     * @api {get} /:table/:id GetData
     * @apiVersion 0.2.0
     * @apiName GetData
     * @apiGroup common
     * @apiPermission admin
     *
     * @apiDescription 可获取任意数据表的行信息
     *
     * @apiSuccess {Boolean} error 是否错误.
     * @apiSuccess {Object} result 行数据.
     * @apiSuccess {String} result.title 某字段值.
     * @apiSuccess {String} message 提示消息. 
     *
     * @apiError TableNotFound 表不存在.
     * @apiError IdNotFound 数据行id不存在.
     * @apiSampleRequest http://192.168.1.120:82/api/im_inform/1
     */
    public function show($table,$id)
    {   
        //字段过滤
        $configResult = $this::tableConfig($table);
        $filedStr     = "";
        foreach ($configResult['fields'] as $field) {
            $fieldName = $field['name'];
            $filedStr .= "$fieldName,";
        }
        $filedStr  = substr($filedStr, 0, -1);

        $result = DB::select("SELECT $filedStr FROM $table WHERE id=$id");
        $result = empty($result)?[]:$result[0];

        //值转换
        foreach ($result as $key => $value) {
            if($configResult['fields'][$key]["type"] == "boolean") $result->$key = $value?true:false;
        }

        return $this::jsonResponse(false,$result);
    }

    /**
     * @api {post} /:table StoreData
     * @apiVersion 0.2.0
     * @apiName StoreData
     * @apiGroup common
     * @apiPermission admin
     *
     * @apiDescription 可向任意数据表存储信息
     *
     * @apiParam {String} title 某字段值.
     *
     * @apiSuccess {Boolean} error 是否错误.
     * @apiSuccess {Number} result 新建id.
     * @apiSuccess {String} message 提示消息.
     *
     * @apiError TableNotFound 表不存在.
     * @apiError ParamFound 参数错误.
     * @apiSampleRequest http://192.168.1.120:82/api/im_inform
     */
    public function store(Request $request,$table)
    {
        $data = $this->dataFileter($table,$request->all(),"store");
        if(empty($data)){
            return $this::jsonResponse(true,[],'参数错误');
        }

        $id = DB::table($table)->insertGetId($data);
        return $this::jsonResponse(false,$id);
    }

    /**
     * @api {put} /:table/:id UpdateData
     * @apiVersion 0.2.0
     * @apiName UpdateData
     * @apiGroup common
     * @apiPermission admin
     *
     * @apiDescription 可修改任意数据表中信息
     *
     * @apiParam {String} title 某字段值.
     *
     * @apiSuccess {Boolean} error 是否错误.
     * @apiSuccess {Boolean} result 是否成功.
     * @apiSuccess {String} message 提示消息.
     *
     * @apiError TableNotFound 表不存在.
     * @apiError IdNotFound 数据行id不存在.
     * @apiError ParamFound 参数错误.
     * @apiSampleRequest http://192.168.1.120:82/api/im_inform/1
     */
    public function update(Request $request,$table,$id)
    {
        $data = $this->dataFileter($table,$request->all(),"update");
        if(empty($data)){
            return $this::jsonResponse(true,[],'参数错误');
        }

        $result = DB::table($table)->where("id",$id)->update($data);
        return $this::jsonResponse(false,$result);
    }

    /**
     * @api {delete} /:table/:id DeleteData
     * @apiVersion 0.2.0
     * @apiName DeleteData
     * @apiGroup common
     * @apiPermission admin
     *
     * @apiDescription 可删除任意数据表中信息
     *
     * @apiSuccess {Boolean} error 是否错误.
     * @apiSuccess {Boolean} result 是否成功.
     * @apiSuccess {String} message 提示消息.
     *
     * @apiError TableNotFound 表不存在.
     * @apiError IdNotFound 数据行id不存在.
     * @apiSampleRequest http://192.168.1.120:82/api/im_inform/1
     */
    public function destroy($table,$id)
    {
        $result = DB::delete("DELETE FROM $table WHERE id=$id");
        return $this::jsonResponse(false,$result);
    }

    function dataFileter($table,$input,$function){

        $inputKeys    = array_keys($input);
        $tableConfig  = $this::tableConfig($table);
        $data   = [];
        foreach ($tableConfig["fields"] as $key => $value) {
            $fieldName = $value["name"];
            
            if($function == "store" && !$value["in_store"]) continue;
            //if($function == "update" && !$value["in_store"])  continue;
            if(!in_array($fieldName,$inputKeys) || $input[$fieldName] === null){
                if($value["null"] == 'NO' && $function == "store") return [];
                continue;
            }

            $data[$fieldName] = $input[$fieldName];
        }

        return $data;
    }

    /**
     * @api {post} /file UploudFile
     * @apiVersion 0.2.0
     * @apiName UploudFile
     * @apiGroup common
     * @apiPermission admin
     *
     * @apiDescription 文件上传
     *
     * @apiParam {String} file 上传文件.
     *
     * @apiSuccess {Boolean} error 是否错误.
     * @apiSuccess {String} result 文件路径.
     * @apiSuccess {String} message 提示消息.
     *
     * @apiSampleRequest http://192.168.1.120:82/api/file
     */
    public function upload(Request $request){
        if ($file = UploadRequest::file('file')) {

            $fileExtension  = $file->getClientOriginalExtension();
            $folderType     = in_array($fileExtension, ["png", "jpg", "gif"])?"images":"files";


            $urlPath     = "/uploads/$folderType/".date("Ymd")."/";
            $serverPath  = $_SERVER["DOCUMENT_ROOT"].$urlPath;
            $fileName    = md5(date("Ymd").str_random(10)).'.'.$fileExtension;
            
            $file->move($serverPath, $fileName);
            
            if(!file_exists($serverPath.$fileName)){
                return $this::jsonResponse(true,'上传失败。');
            }

            return $this::jsonResponse(false,"http://".$_SERVER["HTTP_HOST"].$urlPath.$fileName,'上传成功。');
            
        }else{
            return $this::jsonResponse(true,[],'无上传文件。'); 
        }
        
    }

}

