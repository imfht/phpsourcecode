<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted' => '必须接受 :attribute',
    'active_url' => ':attribute 不是有效的URL。',
    'after' => ':attribute 必须晚于 :date。',
    'after_or_equal' => ':attribute 必须晚于或者等于 :date。',
    'alpha' => ':attribute 可能只包含字母。',
    'alpha_dash' => ':attribute 可能只包含字母，数字，短划线和下划线。',
    'alpha_num' => ':attribute 可能只包含字母和数字。',
    'array' => ':attribute 必须是一个数组。',
    'before' => ':attribute 必须早于 :date。',
    'before_or_equal' => ':attribute 必须早于或者等于 :date。',
    'between' => [
        'numeric' => ':attribute 必须大于 :min，小于 :max 。',
        'file' => ':attribute 必须大于 :min KB，小于 :max KB。',
        'string' => ':attribute 必须大于 :min 个字符，小于 :max 个字符。',
        'array' => ':attribute 长度必须大于 :min，小于 :max 。',
    ],
    'boolean' => ':attribute 必须为 true 或者 false。',
    'confirmed' => ':attribute 不匹配。',
    'date' => ':attribute 不是有效日期。',
    'date_equals' => ':attribute 必须与 :date 相等',
    'date_format' => ':attribute 与 :format 的格式要求不匹配',
    'different' => ':attribute 和 :other 不能相同',
    'digits' => ':attribute 必须是 :digits 的数字。',
    'digits_between' => ':attribute 必须是大于 :min 并且小于 :max 的数字。',
    'dimensions' => ':attribute 图像尺寸无效。',
    'distinct' => ':attribute 字段有重复值。',
    'email' => ':attribute 必须是一个有效的E-mail地址。',
    'exists' => '选项 :attribute 无效。',
    'file' => ':attribute 必须是一个文件。',
    'filled' => ':attribute 必须有一个值。',
    'gt' => [
        'numeric' => ':attribute 必须大于 :value 。',
        'file' => ':attribute 必须大于 :value KB 。',
        'string' => ':attribute 必须大于 :value 个字符。',
        'array' => ':attribute 长度必须大于 :value 个。',
    ],
    'gte' => [
        'numeric' => ':attribute 必须大于或者等于 :value 。',
        'file' => ':attribute 必须大于或者等于 :value KB 。',
        'string' => ':attribute 必须大于或者等于 :value 个字符。',
        'array' => ':attribute 必须至少有 :value 个项。',
    ],
    'image' => ':attribute 必须是个图片',
    'in' => '选择的 :attribute 无效。',
    'in_array' => ':other 不存在属性 :attribute 。',
    'integer' => ':attribute 必须是整形。',
    'ip' => ':attribute 必须是一个有效的 IP 地址。',
    'ipv4' => ':attribute 必须是一个有效的 IPv4 地址。',
    'ipv6' => ':attribute 必须是一个有效的 IPv6 地址。',
    'json' => ':attribute 必须是一个有效的 JSON 字符串。',
    'lt' => [
        'numeric' => ':attribute 必须小于 :value 。',
        'file' => ':attribute 必须小于 :value KB。',
        'string' => ':attribute 必须小于 :value 个字符。',
        'array' => ':attribute 长度必须小于 :value 。',
    ],
    'lte' => [
        'numeric' => ':attribute 必须小于或者等于 :value 。',
        'file' => ':attribute 必须小于或者等于 :value KB。',
        'string' => ':attribute 必须小于或者等于 :value 个字符。',
        'array' => ':attribute 长度必须小于或者等于 :value 。',
    ],
    'max' => [
        'numeric' => ':attribute 可能不会大于 :max 。',
        'file' => ':attribute 可能不会大于 :max KB。',
        'string' => ':attribute 可能不会大于 :max 个字符。',
        'array' => ':attribute 长度可能不会大于 :max 。',
    ],
    'mimes' => ':attribute 必须是类型为 :values 的文件。',
    'mimetypes' => ':attribute 必须是类型为 :values 的文件。',
    'min' => [
        'numeric' => ':attribute 必须大于 :min 。',
        'file' => ':attribute 必须大于 :min KB 。',
        'string' => ':attribute 必须大于 :min 个字符。',
        'array' => ':attribute 长度必须大于 :min 。',
    ],
    'not_in' => '已选择的 :attribute 无效。',
    'not_regex' => ':attribute 格式不正确。',
    'numeric' => ':attribute 必须是一个数字。',
    'present' => ':attribute 必须存在。',
    'regex' => ':attribute 格式不正确。',
    'required' => ':attribute 是必填项。',
    'required_if' => '当 :other 是 :value 时，:attribute 是必须的。',
    'required_unless' => '当 :other 属于 :values 时，:attribute 是必须的。',
    'required_with' => '当 :values 存在时，:attribute 是必须的。',
    'required_with_all' => '当 :values 存在时，:attribute 是必须的。',
    'required_without' => '当 :values 不存在，:attribute 是必须的。',
    'required_without_all' => '当 :values 其中一个存在时，:attribute 是必须的。',
    'same' => ':attribute 和 :other 必须匹配。',
    'size' => [
        'numeric' => ':attribute 大小必须是 :size 。',
        'file' => ':attribute 大小必须是 :size KB 。',
        'string' => ':attribute 大小必须是 :size 个字符。',
        'array' => ':attribute 有且只能有 :size 项。',
    ],
    'starts_with' => ':attribute 必须从以下之一开始： :values',
    'string' => ':attribute 必须是一个字符串。',
    'timezone' => ':attribute 必须是有效的区域。',
    'unique' => ':attribute 已经存在。',
    'uploaded' => ':attribute 上传失败。',
    'url' => ':attribute 格式不正确。',
    'uuid' => ':attribute 必须是有效的 UUID。',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */
    'attributes' => [],
];
