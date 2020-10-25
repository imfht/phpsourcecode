<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 15-5-5
 * Time: 下午3:18
 */
namespace App\Http\Controllers;

use Helper\ResponseHelper;
use Helper\SightHelper;
use Helper\ValidateHelper;
use Illuminate\Http\Request;
use App\Sight;

class SightController extends Controller
{
    use SightHelper;

    public function index()
    {

    }

    /**
     * 新建景点
     *
     * 请求的数据格式：
     *  province: 景点所在的省份【必填】
     *  city: 景点所在的地级市【必填】
     *  loc: 景点所在的地理坐标， GeoJSON格式【必填】
     *  name: 景点的名称【必填】
     *  address: 景点的详细位置，文字描述【必填】
     *  description: 景点的描述【可选】
     *
     * 返回的数据格式：
     *  保存失败（400）：
     *  {
     *    "error": "相关的错误提示信息"
     *  }
     *  保存成功（201）：
     *  {
     *    "_id": "对应景点的id"
     *  }
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {

        $validator = $this->getStoreValidator($request->all());

        if( $validator->fails() ){
            return ResponseHelper::responseErrorMessageOnJson($request->all(), $validator->getMessageBag());
        } else {
            $postData = $this->buildStoreData($request->all());
            $addId = Sight::create($postData)['_id'];
            return response()->json([
                '_id' => $addId
            ], 201);
        }
    }


    /**
     * 返回具体的景点信息.
     *
     * 返回的数据格式(200):
     *  {
     *    "_id": "主键id，唯一标识",
     *    "province": "省份",
     *    "city": "城市（地级市）",
     *    "loc": {"type": "Point", "coordinates": ["", ""]}, //地理坐标
     *    "name": "景点名称",
     *    "description": "景点描述", //可选
     *    "address": "景点的详细地址（文字描述）",
     *    "images": [] //注意是个数组，景点所关联的图片, 【可能不存在】
     *    "comments": [] //注意是个数组，景点所关联的用户评价与讨论 【可能不存在】
     *    "check_in_num": "此景点的用户签到数"  【可能不存在】
     * }
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $target = Sight::findOrFail($id);

        return response()->json($target);
    }

    /**
     * 更新景点的信息.
     *
     * 提交的数据格式：
     *  type: base, check_in
     * 返回的数据格式：
     *  成功(200)；
     *  失败:400
     *   {
     *     "error": "相关的错误信息"
     *     "data": 请求提交的更新数据
     *   }
     * 失败：403|404
     * {
     *     "error": "相关的错误信息"
     * }
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update($id)
    {
        $putData = \Input::all();
        $validate = $this->getUpdateValidator($putData);
        if ($validate->fails()) {
            return ResponseHelper::responseErrorMessageOnJson($putData, $validate->getMessageBag());
        }

        $resp = $this->updateSightByCond($id, $putData);
        if ( isset($resp['error']) ) {
            return response()->json($resp['error'], $resp['status']);
        } else {
            $sightData = Sight::findOrFail($id);
            return response()->json($sightData, 200);
        }
    }
}