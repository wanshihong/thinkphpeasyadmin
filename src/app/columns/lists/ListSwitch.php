<?php


namespace easyadmin\app\columns\lists;


class ListSwitch extends BaseList
{

    /**
     * 列的其他选项
     * @var array
     */
    protected $options = [
        'attr' => '',//属性
        'class' => '',//样式表
        'success' => 1, //正确的值
        'text' => 'ON|OFF',//开关文字
        'url' => 'enable',//请求URL
        'params' => [],//请求参数,
        'jsFiles' => ['/easy_admin_static/js/switch.js']
    ];

    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'list:field:switch';

    public function formatData($data)
    {
        $params = $this->getOption('params', []);
        $params['id'] = $data['row_id'];
        $params['field'] = $data['field'];
        $url = $this->getOption('url') . '?' . http_build_query($params);


        $onValue = $this->getOption('success', 1);
        $data['isChecked'] = $this->getValue() == $onValue ? 'checked' : '';
        $data['text'] = $this->getOption('text');
        $data['url'] = $url;

        return $data;
    }

}
