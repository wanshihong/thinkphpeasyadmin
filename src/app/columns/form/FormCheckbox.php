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
     * 格式化数据
     * 格式化后存入数据库,或者操作数据库
     * @param $val
     * @return mixed
     */
    public function inFormat($val)
    {
        $ret = $val == 'on' ? $this->getOption('success', 1) : $this->getOption('error', 0);
        return parent::inFormat($ret);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function formatData($data)
    {
        $onValue = $this->getOption('success', 1);
        $data['isChecked'] = $this->getValue() == $onValue ? 'checked' : '';
        $data['text'] = $this->getOption('text');

        return $data;
    }
}
