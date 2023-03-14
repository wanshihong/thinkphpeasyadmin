<?php


namespace easyadmin\app\columns\form;


class FormUpload extends BaseForm
{

    protected array $jsFiles = ['cropperjs/cropper.js', 'js/upload.js'];
    protected array $cssFiles = ['cropperjs/cropper.css'];

    public array $options = [
        'highlight' => false, //包含搜索的值是否高亮
        'width' => 80, //宽
        'height' => 80,//高
        'cropper' => true,//裁剪
        'multiple' => false,//是否开启多图上传, 大于0的数字表示开启,具体的数字表示可以传多少张
        'url' => 'upload'
    ];


    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath = 'form:field:upload';

    public function formatData($data)
    {
        $data['url'] = $this->getOption('url', url("upload"));
        $data['cropper'] = $this->getOption('cropper') ? 1 : 0;
        $data['width'] = $this->getOption('width');
        $data['height'] = $this->getOption('height');
        $data['multiple'] = $this->getOption('multiple');
        return $data;
    }


}
