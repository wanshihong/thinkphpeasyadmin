<?php


namespace easyadmin\app\columns\form;


class FormSwitch extends BaseForm
{
    /**
     * 列的其他选项
     * @var array
     */
    protected array $options = [
        'attr' => '',//属性
        'class' => '',//样式表
        'success' => 1, //正确或者打开 的值
        'error' => 0, //错误或者关闭 的值
        'text' => 'ON|OFF',//开关文字
    ];


    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath = 'form:field:switch';

    public function formatData($data)
    {
        $onValue = $this->getOption('success', 1);
        $data['isChecked'] = $this->getValue() == $onValue ? 'checked' : '';
        $data['text'] = $this->getOption('text');
        $data['success'] = $this->getOption('success');

        return $data;
    }
}
