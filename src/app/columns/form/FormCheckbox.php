<?php


namespace easyadmin\app\columns\form;



class FormCheckbox extends BaseForm
{

    protected array $options = [

        'title' => '',//空是传统的 checkbox 框框中间勾选,   带文字是 layui 美化的样式
        'success' => 1, //正确或者打开 的值
        'error' => 0, //错误或者关闭 的值

    ];
    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath = 'form:field:checkbox';


    /**
     * @param $data
     * @return mixed
     */
    public function formatData($data): mixed
    {
        $onValue = $this->getOption('success', 1);
        $data['isChecked'] = $this->getValue() == $onValue ? 'checked' : '';
        $data['title'] = $this->getOption('title', '');
        $data['success'] = $this->getOption('success', 1);

        return $data;
    }


    /**
     * 接收用户输入的值
     * @param array $data 外部接收数据得数组
     * @return array|null
     */
    public function requestValue(array &$data = []): ?array
    {
        $fieldName = $this->getSelectAlias();

        //接收用户参数
        $value = request()->post($fieldName, request()->get($fieldName, $this->getOption('default')));

        if ($value === null) {
            $value = $this->getOption('error', 0);
        }
        $value = $this->inFormat($value);
        $data[$fieldName] = $value;

        $this->setValue($value);
        return $data;
    }

}
