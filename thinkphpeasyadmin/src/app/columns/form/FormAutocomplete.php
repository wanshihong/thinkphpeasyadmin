<?php


namespace easyadmin\app\columns\form;


use think\db\exception\DataNotFoundException as DataNotFoundExceptionAlias;
use think\db\exception\DbException as DbExceptionAlias;
use think\db\exception\ModelNotFoundException as ModelNotFoundExceptionAlias;
use think\Exception;
use think\facade\Db;

class FormAutocomplete extends BaseForm
{

    protected array $jsFiles = ['js/autocomplete.js'];

    protected array $options = [
        'table' => '',// 选择的查询表名
        'pk' => 'id',//使用查询,的主键
        'property' => 'text',//查询显示字段,
        //资源文件
        'jsFiles' => [],
    ];
    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath = 'form:field:autocomplete';


    public function formatData($data)
    {
        $data['pk'] = $this->getOption('pk');
        $data['table'] = $this->getOption('table');
        $data['property'] = $this->getOption('property');
        $data['url'] = $this->getOption('url',url("autocomplete_select"));
        return $data;
    }

}
