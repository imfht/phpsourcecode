<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-08-01 01:06
 */

namespace Notadd\Mall\Handlers\Store;

use Notadd\Foundation\Member\Member;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Validation\Rule;
use Notadd\Mall\Models\Store;
use Notadd\Mall\Models\StoreInformation;

/**
 * Class ApplyHandler.
 */
class ApplyHandler extends Handler
{
    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    protected function execute()
    {
        $this->validate($this->request, [
            'business_scope' => Rule::required(),
            'category_id' => Rule::required(),
            'company_address' => Rule::required(),
            'company_capital' => Rule::required(),
            'company_employees' => Rule::required(),
            'company_location' => Rule::required(),
            'company_name' => Rule::required(),
            'company_telephone' => Rule::required(),
            'contact_email' => Rule::required(),
            'contact_name' => Rule::required(),
            'contact_telephone' => Rule::required(),
            'license_address' => Rule::required(),
            'license_begins' => Rule::required(),
            'license_deadline' => Rule::required(),
            'license_number' => Rule::required(),
            'store_account' => Rule::required(),
            'store_name' => Rule::required(),
            'type' => Rule::required(),
        ], [
            'business_no.required' => '营业执照号必须填写',
            'business_scope.required' => '法定经营范围必须填写',
            'category_id.required' => '所属分类必须填写',
            'company_address.required' => '店铺地址必须填写',
            'company_capital.required' => '注册资金必须填写',
            'company_employees.required' => '员工总数必须填写',
            'company_location.required' => '所在地区必须填写',
            'company_name.required' => '公司名称必须填写',
            'company_telephone.required' => '公司电话必须填写',
            'contact_email.required' => '电子邮箱必须填写',
            'contact_name.required' => '联系人必须填写',
            'contact_telephone.required' => '联系电话必须填写',
            'license_address.required' => '营业执照所在地必须填写',
            'license_begins.required' => '必须填写',
            'license_deadline.required' => '必须填写',
            'license_number.required' => '营业执照号必须填写',
            'store_account.required' => '店铺所有者必须填写',
            'store_name.required' => '店铺名称必须填写',
            'type.required' => '店铺类型必须填写',
        ]);
        $this->beginTransaction();
        $data = $this->request->only([
            'business_scope',
            'category_id',
            'company_address',
            'company_capital',
            'company_employees',
            'company_location',
            'company_name',
            'company_telephone',
            'contact_email',
            'contact_name',
            'contact_telephone',
            'license_address',
            'license_begins',
            'license_deadline',
            'license_number',
            'store_account',
            'store_name',
            'type',
        ]);
        $user = Member::query()->where('name', $data['store_account'])->first();
        $store = Store::query()->create([
            'category_id' => $data['category_id'],
            'name' => $data['store_name'],
            'user_id' => $user->getAttribute('id'),
            'company' => $data['company_name'],
            'location' => $data['company_location'],
            'address' => $data['company_address'],
        ]);
        $information = StoreInformation::query()->create([
            'store_id' => $store->getAttribute('id'),
            'company' => $data['company_name'],
            'location' => $data['company_location'],
            'address' => $data['company_address'],
            'telephone' => $data['company_telephone'],
            'employees' => $data['company_employees'],
            'capital' => $data['company_capital'],
            'contacts' => $data['contact_name'],
            'email' => $data['contact_email'],
            'licence_number' => $data['license_number'],
            'licence_location' => $data['license_address'],
//            'licence_validity' => $data[''],
            'licence_sphere' => $data['business_scope'],
//            'licence_image' => $data[''],
        ]);
        if ($store instanceof Store && $information instanceof StoreInformation) {
            $this->commitTransaction();
            $this->withCode(200)->withMessage('申请店铺入驻成功！');
        } else {
            $this->rollBackTransaction();
            $this->withCode(500)->withError('申请店铺入驻失败！');
        }
    }
}
