<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-4-22
 * Time: 下午9:37
 */
namespace App\Http\Controllers;

use App\Services\DataPurifier;
use App\User;
use Auth;
use Helper\ResponseHelper;
use Illuminate\Http\Request;
use Input;
use Validator;
use App\Collection;

class CollectionController extends Controller
{
    public function __construct(Request $request)
    {
        DataPurifier::purifyForRest($request, $this->purifyField);
    }

    /**
     * 返回当前用户所收藏的地点信息，所返回的格式如下：
     * [
     *  {
     *   "province": "省份",
     *   "city": "城市",
     *   "zone": "区/县",
     *   "longitude": "维度",
     *   "latitude": "经度",
     *   "address": "地址描述",
     *   "label": "地点标签",
     *  },
     *  //注意是个数组..
     * ]
     *
     * 若新建成功，则返回：
     * {
     *   "_id": "新建资源的id"
     * ｝
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $resp = Collection::changeDataToResp(Auth::user()->collection );
        return response()->json($resp);
    }

    /**
     * 用于处理用户新建并所保存的地点。
     *
     * 提交的数据格式如下：
     * 　province: 省份，可选
     *   city: 城市，可选
     *   zone: 区/县, 可选
     *   longitude: 经度，必填
     *   latitude: 维度，必填
     *   address：　地址描述，必填
     * 　label: 地点标签，可选
     *   name:  地点名字，必填
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store()
    {
        $postData = Input::all();

        $validator = $this->getStoreValidator($postData);
        if ( $validator->fails() ) {
            return ResponseHelper::responseErrorMessageOnJson($postData, $validator->getMessageBag());
        }

        $placeData = $this->makeStoreData($postData);
        $new = Collection::create($placeData);

        return response()->json([
            '_id' => $new['_id']
        ], 201);
    }

    /**
     * 内部辅助类，生成新建地点的相关数据
     *
     * @param $postData array 用户提交过来的数据
     * @return mixed
     */
    protected function makeStoreData($postData)
    {
        $postData['loc'] = [
            'type' => 'Point',
            'coordinates' => [ $postData['longitude'], $postData['latitude'] ]
        ];

        $postData['creator_id'] = Auth::user()['_id'];

        if( ! isset($postData['label']) ){
            $postData['label'] = '';
        }

        return array_only($postData, [
            'uid', 'address', 'name', 'loc', 'creator_id', 'label'
        ]);
    }

    /**
     * 生成校验器.
     *
     * @param $placeData array 用户提交过来的数据
     * @return \Illuminate\Validation\Validator
     */
    protected function getStoreValidator($placeData)
    {
        $validator = Validator::make($placeData, [
            'name' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'address' => 'required',
        ]);

        return $validator;
    }

    /**
     * 展现具体的收藏地点信息.
     *
     * 具体的格式如下：
     *  {
     *   "province": "省份",
     *   "city": "城市",
     *   "zone": "区/县",
     *   "longitude": "维度",
     *   "latitude": "经度",
     *   "address": "地址描述",
     *   "label": "地点标签",
     *  }
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $collection = Collection::where('creator_id', Auth::user()['_id'])
            ->where('_id', $id)->first();

        if ( is_null($collection) ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        } else {
            return response()->json(
                Collection::changeDataToResp($collection), 200
            );
        }
    }

    /**
     * 删除用户指定的收藏地点
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy($id)
    {
        $placeData = Collection::where('creator_id', Auth::user()['_id'])
            ->where('_id', $id)->first();

        if ( is_null($placeData) ) {
            return response()->json(['error' => '找不到相应的数据'], 404);
        } else {
            $placeData->delete();
            return response(null, 200);
        }

    }

    private $purifyField = [
        'POST' => ['province','city','zone','address','label','name']
    ];
}