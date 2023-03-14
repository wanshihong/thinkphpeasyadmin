<?php


namespace easyadmin\app\columns\form;


use think\db\exception\DataNotFoundException as DataNotFoundExceptionAlias;
use think\db\exception\DbException as DbExceptionAlias;
use think\db\exception\ModelNotFoundException as ModelNotFoundExceptionAlias;
use think\Exception;
use think\facade\Db;

class FormRadio extends BaseForm
{

    protected array $options = [

        'options' => [],


    ];
    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath = 'form:field:radio';

    /**
     * @param $data
     * @return mixed
     */
    public function formatData($data)
    {
        $data['options'] = $this->getOption('options', []);

        return $data;
    }
}
