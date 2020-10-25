<?php
namespace app\shop\service;
/**
 * 商城操作
 */
class ShopService extends \app\base\service\BaseService {

    /**
     * 添加问答
     * @param $app
     * @param $hasId
     * @param $userId
     * @param $content
     * @return bool
     */
    public function addFaq($app, $hasId, $userId, $content) {
        if(empty($app) || empty($hasId) ||empty($userId) || empty($content)) {
            return $this->error('参数不完整！');
        }

        $content = html_clear(trim($content));
        if(empty($content)) {
            return $this->error('您咨询的问题不能为空！');
        }
        if(mb_strlen($content) > 100) {
            return $this->error('请保持问题在100个文字内！');
        }

        $prevInfo = target('shop/ShopFaq')->loadList([
            'A.user_id' => $userId,
            'A.has_id' => $hasId,
            'A.app' => $app,
        ],1,'time desc');
        $prevInfo = $prevInfo[0];

        if(!empty($prevInfo)) {
            if(empty($prevInfo['replay_time'])) {
                return $this->error('您的上个问题暂无回复，无法咨询新的问题！');
            }
        }
        $status = target('shop/ShopFaq')->add([
            'user_id' => $userId,
            'has_id' => $hasId,
            'app' => $app,
            'time' => time(),
            'content' => $content
        ]);
        if(!$status){
            return $this->error('咨询问题失败！');
        }
        return $this->success('问题发布成功，请等待回复！');
    }

    /**
     * 添加收藏
     * @param $app
     * @param $hasId
     * @param $userId
     * @param $title
     * @param $image
     * @param $price
     * @return bool
     */
    public function addFollow($app, $hasId, $userId, $title, $image, $price) {

        $followInfo = target('shop/ShopFollow')->getWhereInfo(['app' => $app, 'has_id' => $hasId, 'user_id' => $userId]);
        if(!empty($followInfo)) {
            if(!target('shop/ShopFollow')->where(['follow_id' => $followInfo['follow_id']])->delete()) {
                return $this->error('取消收藏失败！');
            }
        }else {
            $data = [];
            $data['user_id'] = $userId;
            $data['app'] = $app;
            $data['has_id'] = $hasId;
            $data['time'] = time();
            $data['title'] = $title;
            $data['image'] = $image;
            $data['price'] = $price;
            if(!target('shop/ShopFollow')->add($data)) {
                return $this->error('收藏商品失败！');
            }
        }

        return $this->success($followInfo ? 'dec' : 'inc');
    }

    /**
     * 获取评论统计
     * @param $app
     * @param $hasId
     * @return array
     */
    public function getCommentStatis($app, $hasId) {

        $countCommentPositive = target('order/OrderComment')->countList([
            'A.app' => $app,
            'A.has_id' => $hasId,
            '_sql' => 'A.level = 4 OR A.level = 5'
        ]);
        $countCommentNeutral = target('order/OrderComment')->countList([
            'A.app' => $app,
            'A.has_id' => $hasId,
            '_sql' => 'A.level = 3 OR A.level = 4'
        ]);
        $countCommentNegative = target('order/OrderComment')->countList([
            'A.app' => $app,
            'A.has_id' => $hasId,
            '_sql' => 'A.level = 0 OR A.level = 1'
        ]);

        $sumComment = $countCommentPositive + $countCommentNeutral + $countCommentNegative;

        $commentPositiveRate = $sumComment ? round(($countCommentPositive / $sumComment) * 100) : 0;
        $commentNeutralRate = $sumComment ? round(($countCommentNeutral / $sumComment) * 100) : 0;
        $commentNegativeRate = $sumComment ? round(($countCommentNegative / $sumComment) * 100) : 0;

        return [
            'commentRate' => [
                'positive' => $commentPositiveRate,
                'neutral' => $commentNeutralRate,
                'negative' => $commentNegativeRate,
            ],
            'commentCount' => [
                'positive' => $countCommentPositive,
                'neutral' => $countCommentNeutral,
                'negative' => $countCommentNegative,
            ]
        ];
    }
}

