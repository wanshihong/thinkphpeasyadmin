<?php


namespace easyadmin\app\columns\form;


class FormUpload extends BaseForm
{

    public $options = [
        'highlight' => true, //包含搜索的值是否高亮
    ];


    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'form:field:text';


}
