<?php


namespace easyadmin\app\columns\form;


class FormUpload extends BaseForm
{

    protected $jsFiles = ['cropperjs/cropper.js', 'js/upload.js'];
    protected $cssFiles = ['cropperjs/cropper.css'];

    public $options = [
        'highlight' => false, //包含搜索的值是否高亮
        'width' => 80, //宽
        'height' => 80,//高
        'cropper' => true,//裁剪
        'url' => ''
    ];


    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'form:field:upload';

    public function formatData($data)
    {
        $data['url'] = $this->getOption('url', url("upload"));
        $data['cropper'] = $this->getOption('cropper') ? 1 : 0;
        $data['width'] = $this->getOption('width');
        $data['height'] = $this->getOption('height');
        return $data;
    }


}
