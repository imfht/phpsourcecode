<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-07-15 19:45
 */
namespace Notadd\Mall\Handlers\Administration\Store\Grade;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\StoreGrade;

/**
 * Class EditHandler.
 */
class EditHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'application_instruction' => Rule::required(),
            'can_claim'               => Rule::boolean(),
            'can_upload'              => Rule::boolean(),
            'level'                   => Rule::numeric(),
            'name'                    => Rule::required(),
            'price'                   => Rule::numeric(),
            'publish_limit'           => Rule::numeric(),
            'upload_limit'            => Rule::numeric(),
        ], [
            'application_instruction.required' => '申请说明必须填写',
            'can_claim.numeric'                => '可认领商品必须为布尔值',
            'can_upload.numeric'               => '可自主发布商品必须为布尔值',
            'level.numeric'                    => '店铺等级必须为数值',
            'name.required'                    => '等级名称必须填写',
            'price.numeric'                    => '收费标准必须为数值',
            'publish_limit.numeric'            => '可发布商品数必须为数值',
            'upload_limit.numeric'             => '可上传商品数必须为数值',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'application_instruction',
            'level',
            'name',
            'publish_limit',
            'upload_limit',
            'can_claim',
            'can_upload',
            'price',
        ]);
        $grade = StoreGrade::query()->find($this->request->input('id'));
        if ($grade instanceof StoreGrade && $grade->update($data)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('编辑店铺等级信息成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('编辑店铺等级信息失败！');
        }
    }
}
