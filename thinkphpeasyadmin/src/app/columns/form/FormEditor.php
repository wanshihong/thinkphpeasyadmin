<?php


namespace easyadmin\app\columns\form;


class FormEditor extends BaseForm
{

    protected array $jsFiles = ['wangEditor4.6.2.min.js', 'js/editor.js'];

    protected array $options = [
        'jsFiles' => [],
        'url' => 'upload'
    ];


    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath = 'form:field:editor';


    public function formatData($data)
    {
        $data['url'] = url("upload");
        $data['editor_id'] = 'editor_id' . time() . mt_rand(1000, 9999);
        return $data;
    }

}
