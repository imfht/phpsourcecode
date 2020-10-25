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

    'accepted'             => 'The :attribute must be accepted.',
    'active_url'           => ':attribute 不是一个URL地址。',
    'after'                => ':attribute 填写的时间必须在 :date 之后。',
    'after_or_equal'       => ':attribute 必须是大于或者等于 :date.',
    'alpha'                => ':attribute 只能包含字母。',
    'alpha_dash'           => ':attribute 仅包含字母、数字、破折号（ - ）以及下划线（ _ ）。',
    'alpha_num'            => ':attribute 仅包含字母、数字。',
    'array'                => ':attribute 必须是数组。',
    'before'               => ':attribute 必须在 :date 之前',
    'before_or_equal'      => ':attribute 必须小于或者等于 :date.',
    'between'              => [
        'numeric' => ':attribute 必须在 :min 和 :max 之间。',
        'file'    => ':attribute 大小必须在 :min KB 和 :max KB 之间。',
        'string'  => ':attribute 长度必须在 :min 和 :max 字符之间。',
        'array'   => ':attribute 元素数目必须在 :min 和 :max 之间。',
    ],
    'boolean'              => ':attribute 值必须是 true 或者 false 。',
    'confirmed'            => ':attribute 的确认信息与原始值不匹配。',
    'date'                 => ':attribute 不是一个日期。',
    'date_format'          => ':attribute 与定义的日期格式 :format 不一致。',
    'different'            => ':attribute 与 :other 不许不相同。',
    'digits'               => ':attribute must be :digits digits.',
    'digits_between'       => ':attribute 必须是 :min 和 :max 之间的数字。',
    'dimensions'           => ':attribute has invalid image dimensions.',
    'distinct'             => ':attribute 文件必须是图片并且图片比例必须符合规则。',
    'email'                => ':attribute 必须是电子邮件地址。',
    'exists'               => ':attribute 的值不存在。',
    'file'                 => ':attribute 必须是一个文件。',
    'filled'               => ':attribute field is required.',
    'image'                => ':attribute 必须是图片。',
    'in'                   => ':attribute 在指定的数据列中不存在。',
    'in_array'             => ':attribute 不在 :other 中。',
    'integer'              => ':attribute 必须是数字。',
    'ip'                   => ':attribute 不是IP地址。',
    'json'                 => ':attribute 不是有效的 JSON 字符串。',
    'max'                  => [
        'numeric' => ':attribute 不能大于 :max.',
        'file'    => ':attribute 不能大于 :max KB 。',
        'string'  => ':attribute 长度不能大于 :max 字符。',
        'array'   => ':attribute 元素数量不能大于:max 个。',
    ],
    'mimes'                => ':attribute 必须是以下文件类型: :values.',
    'mimetypes'            => ':attribute 必须是以下文件类型: :values.',
    'min'                  => [
        'numeric' => ':attribute 不能小于 :min.',
        'file'    => ':attribute 不能小于 :min KB 。',
        'string'  => ':attribute 字符长度不能小于 :min 。',
        'array'   => ':attribute 元素数量不能小于 :min 个。',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => ':attribute 必须是数字。',
    'present'              => ':attribute field must be present.',
    'regex'                => ':attribute 格式不符合要求。',
    'required'             => ':attribute 是必填项。',
    'required_if'          => '当 :other 的值是 :value 时 :attribute 不能为空。',
    'required_unless'      => ':attribute 是必填项除非 :other 的值是 :values 。',
    'required_with'        => ':attribute field is required when :values is present.',
    'required_with_all'    => ':attribute field is required when :values is present.',
    'required_without'     => ':attribute field is required when :values is not present.',
    'required_without_all' => ':attribute field is required when none of :values are present.',
    'same'                 => ':attribute 和 :other 的值必须相匹配。',
    'size'                 => [
        'numeric' => ':attribute must be :size.',
        'file'    => ':attribute must be :size kilobytes.',
        'string'  => ':attribute must be :size characters.',
        'array'   => ':attribute must contain :size items.',
    ],
    'string'               => ':attribute 必须是字符串。',
    'timezone'             => ':attribute must be a valid zone.',
    'unique'               => ':attribute 的值已经存在。',
    'uploaded'             => ':attribute 没有上传成功。',
    'url'                  => ':attribute format is invalid.',

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
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
