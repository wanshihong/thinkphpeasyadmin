<?php


namespace easyadmin\app\columns\form;


use think\db\exception\DataNotFoundException as DataNotFoundExceptionAlias;
use think\db\exception\DbException as DbExceptionAlias;
use think\db\exception\ModelNotFoundException as ModelNotFoundExceptionAlias;
use think\Exception;
use think\facade\Db;

class FormCheckbox extends BaseForm
{

    protected $options = [

        'title' => '',//空是传统的 checkbox 框框中间勾选,   带文字是 layui 美化的样式
        'success' => 1, //正确或者打开 的值
        'error' => 0, //错误或者关闭 的值

    ];
    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'form:field:checkbox';


    /**
     * @param $data
     * @return mixed
     */
    public function formatData($data)
    {
        $onValue = $this->getOption('success', 1);
        $data['isChecked'] = $this->getValue() == $onValue ? 'checked' : '';
        $data['title'] = $this->getOption('title','');
        $data['success'] = $this->getOption('success',1);

        return $data;
    }


    /**
     * 接收用户输入的值
     * @param array $data 外部接收数据得数组
     * @return array|false|int|mixed
     */
    public function requestValue(&$data = [])
    {
        $request = request();
        $fieldName = $this->getSelectAlias();

        //接收用户参数
        $value = $request->post($fieldName, $request->get($fieldName, $this->getOption('default')));

        if ($value === null) {
            $value = $this->getOption('error',0);
        }
        $value = $this->inFormat($value);
        $data[$fieldName] = $value;

        $this->setValue($value);
        return $data;
    }

}
