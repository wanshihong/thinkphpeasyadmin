<?php


namespace easyadmin\app\columns\lists;


class ListSwitch extends BaseList
{

    protected array $jsFiles = ['js/switch.js'];

    /**
     * 列的其他选项
     * @var array
     */
    protected array $options = [
        'attr' => '',//属性
        'class' => '',//样式表
        'success' => 1, //正确,开启的值
        'error' => 0, //错误,关闭的值
        'text' => 'YES|NO',//开关文字
        'url' => 'enable',//请求URL
        'params' => [],//请求参数,
        'jsFiles' => []
    ];

    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath = 'list:field:switch';

    public function formatData($data)
    {
        $params = $this->getOption('params', []);
        $params['id'] = $data['row_id'];
        $params['field'] = $data['field'];
        $url = url($this->getOption('url'),$params);


        $onValue = $this->getOption('success', 1);
        $offValue = $this->getOption('error', 0);
        $data['isChecked'] = $this->getValue() == $onValue ? 'checked' : '';
        $data['text'] = $this->getOption('text');
        $data['url'] = $url;
        $data['on'] = $onValue;
        $data['off'] = $offValue;

        return $data;
    }

}
