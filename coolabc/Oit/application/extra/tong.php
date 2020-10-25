<?php
// 物流公司提供api,对接获取数据,生成系统中的单据
// 1 注意客户的编码位数
return [
    'vr_type' => 'BA',                  // oit单据类型
    'create_user' => 'admin',            // 默认新增客户
    'res_id' => '01001',                // 产品 - 托运费
    'default_unit_type_id' => 'A02',    // 虚拟的单位
    'temp_service_id' => '0000',        // 临时区域
    'temp_eba_state' => 'A',        // 临时区域
    'emp_id' => '0000',            // 默认单据员工
    'sup_service_id' => 'B0000',    // 默认的供应商临时区域
    'sup_state' => 'A',

];
