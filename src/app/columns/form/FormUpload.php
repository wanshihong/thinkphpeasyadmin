<?php


namespace easyadmin\app\columns\form;


class FormUpload extends BaseForm
{

    protected $jsFiles = ['/easy_admin_static/cropperjs/cropper.js', '/easy_admin_static/js/upload.js'];
    protected $cssFiles = ['/easy_admin_static/cropperjs/cropper.css'];

    public $options = [
        'highlight' => false, //包含搜索的值是否高亮
        'width' => 80, //宽
        'height' => 80,//高
        'ratio' => 1.0,//比例
        'cropper' => true,//裁剪
    ];


    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'form:field:upload';

    public function formatData($data)
    {
        $data['url'] = url("upload");


        $ratio = $this->getOption('ratio');

        $data['cropper'] = $this->getOption('cropper') ? 1 : 0;
        $data['width'] = $this->getOption('width');
        $data['height'] = $this->getOption('height') * $ratio;
        $data['ratio'] = $ratio;
        return $data;
    }


}
