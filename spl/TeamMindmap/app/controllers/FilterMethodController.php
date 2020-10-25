<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-28
 * Time: 下午4:25
 */

/**
 * 此控制器用于返回使用后台过滤机制时的过滤条件.
 *
 * Class FilterMethodController
 */
class FilterMethodController extends \BaseController
{
    /**
     * 根据查询参数获得过滤条件.
     * @param $query
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * 返回的JSON格式如下：
     * {
     *  "过滤项目1" :[
     *      {
     *          "cond": "过滤值",
     *          "label": "过滤值的外部显示文本",
     *      },
     *      //注意是个数组,...
     * ,//注意是个数组
     * }
     */
    public function getIndex($query)
    {
        if( isset( $this->methods[$query] ) ){

            return Response::json(
                $this->buildResp($query)
            );
        }else{
            return Response::make('invalid access', 403);
        }
    }

    /**
     * 构建返回值.
     *
     * @param $query
     * @return array
     */
    protected function buildResp($query)
    {
        $resultArray = App::make($this->methods[$query])->getFilterMethodSet('label');

        $rtn = [];
        foreach($resultArray as $currItemName=>$currItemOpts){

            if( array_keys( $currItemOpts ) !== range(0, count($currItemOpts) - 1) ){
                $rtn[ $currItemName ] = $currItemOpts;
            }else{

                $counter = 0;
                $rtn[ $currItemName ] = [];
                foreach($currItemOpts as $currOpt ){
                    array_push($rtn[ $currItemName ], [
                        'cond'=>$counter++,
                        'label'=>$currOpt
                    ]);
                }
            }
        }

        return $rtn;
    }

    protected $methods = [
      'projectDiscussion'=>'ProjectDiscussion'
    ];
}