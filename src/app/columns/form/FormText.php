<?php


namespace easyadmin\app\columns\form;


class FormText extends BaseForm
{
    /**
     * 列的其他选项
     * @var array
     */
    protected array $options = [
        'highlight' => true, //包含搜索的值是否高亮 ; false 关闭高亮, 不写或者 true 开启高亮
    ];

    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath = 'form:field:text';


}
