<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-03 16:23
 */
namespace Notadd\Mall\Handlers\Administration\Store;

use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Store;

/**
 * Class CreateHandler.
 */
class CreateHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $this->validate($this->request, [
            'category_id'              => Rule::numeric(),
            'company'                  => Rule::required(),
            'company_address'          => Rule::required(),
            'company_capital'          => Rule::required(),
            'company_contacts'         => Rule::required(),
            'company_email'            => Rule::required(),
            'company_employees'        => Rule::required(),
            'company_licence_location' => Rule::required(),
            'company_licence_image'    => Rule::required(),
            'company_licence_number'   => Rule::required(),
            'company_licence_sphere'   => Rule::required(),
            'company_licence_validity' => Rule::required(),
            'company_location'         => Rule::required(),
            'company_telephone'        => Rule::required(),
            'name'                     => Rule::required(),
            'status'                   => Rule::in([
                'review',
                'opening',
                'closed',
                'banned',
            ]),
            'user_id'                  => Rule::numeric(),
        ], [
            'category_id.numeric'               => '所属分类 ID 必须为数值',
            'company.required'                  => '公司名称必须填写',
            'company_address.required'          => '公司详细地址必须填写',
            'company_capital.required'          => '公司注册资金必须填写',
            'company_contacts.required'         => '公司联系电话必须填写',
            'company_email.required'            => '公司电子邮箱必须填写',
            'company_employees.required'        => '公司员工总数必须填写',
            'company_licence_location.required' => '公司营业执照所在地必须填写',
            'company_licence_image.required'    => '公司营业执照电子版必须填写',
            'company_licence_number.required'   => '公司营业执照号必须填写',
            'company_licence_sphere.required'   => '公司法定经营范围必须填写',
            'company_licence_validity.required' => '公司营业执照有效期必须填写',
            'company_location.required'         => '公司所在地必须填写',
            'company_telephone.required'        => '公司电话必须填写',
            'name.required'                     => '店铺名称必须填写',
            'user_id.numeric'                   => '店铺所有者 ID 必须为数值',
            'status.in'                         => '店铺状态不在允许的范围内',
        ]);
        $this->beginTransaction();
        $company = [
            'address'          => $this->request->input('company_address'),
            'capital'          => $this->request->input('company_capital'),
            'company'          => $this->request->input('company'),
            'contacts'         => $this->request->input('company_contacts'),
            'email'            => $this->request->input('company_email'),
            'employees'        => $this->request->input('company_employees'),
            'licence_image'    => $this->request->input('company_licence_image'),
            'licence_location' => $this->request->input('company_licence_location'),
            'licence_number'   => $this->request->input('company_licence_number'),
            'licence_validity' => $this->request->input('company_licence_validity'),
            'licence_sphere'   => $this->request->input('company_licence_sphere'),
            'location'         => $this->request->input('company_location'),
            'telephone'        => $this->request->input('company_telephone'),
        ];
        $data = $this->request->only([
            'address',
            'category_id',
            'company',
            'end_at',
            'flow_marketing',
            'level',
            'location',
            'name',
            'open_at',
            'status',
            'user_id',
        ]);
        if (Store::query()->create($data)->getRelation('information')->create($company)) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('创建店铺成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('创建店铺失败！');
        }
    }
}
