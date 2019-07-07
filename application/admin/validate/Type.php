<?php
namespace app\admin\validate;
use think\Validate;
class Type extends Validate{
    // 验证规则
    protected $rule=[
        'type_name|类型名称'=>'require|unique:type'
    ];
}