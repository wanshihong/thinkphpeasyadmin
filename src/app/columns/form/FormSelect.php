<?php


namespace easyadmin\app\columns\form;


use think\Collection;
use think\db\exception\DataNotFoundException as DataNotFoundExceptionAlias;
use think\db\exception\DbException as DbExceptionAlias;
use think\db\exception\ModelNotFoundException as ModelNotFoundExceptionAlias;
use think\db\Query;
use think\Exception;
use think\facade\Db;

class FormSelect extends BaseForm
{

    protected $options = [

        'options' => [],


        'query' => '',//使用自定义查询语句
        'table' => '',// 选择的查询表名
        'pk' => 'id',//使用查询,的主键
        'property' => 'text',//查询显示字段
    ];
    /**
     * 字段的模板路径
     * @var string
     */
    protected $templatePath = 'form:field:select';

    /**
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function formatData($data)
    {
        if (empty($data['options'])) {
            $data['options'] = [
                ['key' => '', 'text' => '无数据',]
            ];
        }

        //如果定义了数组, 直接使用,不做其他判断
        $options = $this->getOption('options');
        if ($options) {
            $data['options'] = $options;
            return $data;
        }

        $options = $this->parseQuery();
        if ($options) {
            $data['options'] = $options;
            return $data;
        }


        $options = $this->parseTable();
        if ($options) {
            $data['options'] = $options;
            return $data;
        }

        return $data;
    }

    protected function getQueryArr($query, $pk, $property)
    {
        $options = [];
        foreach ($query as $item) {
            if (!array_key_exists($pk, $item)) throw new Exception("{$pk} 不存在于查询字段给中");
            if (!array_key_exists($property, $item)) throw new Exception("{$property} 不存在于查询字段给中");

            array_push($options, [
                'key' => $item[$pk],
                'text' => $item[$property],
            ]);

        }
        return $options;
    }

    /**
     * 解析 query
     * @return array
     * @throws Exception
     */
    protected function parseQuery(): array
    {
        $query = $this->getOption('query');
        if (empty($query)) {
            return [];
        }
        $options = [];
        if (is_callable($query)) {
            $options = call_user_func($query);
            if ($options instanceof Collection) {
                return $options->toArray();
            }
            return $options;
        }


        $pk = $this->getOption('pk', 'id');
        $property = $this->getOption('property', 'text');


        if ($query instanceof Collection) {
            $options = $this->getQueryArr($query, $pk, $property);
        } elseif ($query instanceof Query) {
            $query = $query->field("{$pk},{$property},{$pk}")->select();
            $options = $this->getQueryArr($query, $pk, $property);
        }

        return $options;
    }

    /**
     * @return array
     * @throws DataNotFoundExceptionAlias
     * @throws DbExceptionAlias
     * @throws ModelNotFoundExceptionAlias
     */
    protected function parseTable(): array
    {
        $options = [];
        $table = $this->getOption('table');
        if (empty($table)) {
            return $options;
        }
        $pk = $this->getOption('pk', 'id');
        $property = $this->getOption('property', 'text');

        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $query = Db::name($table)->field("{$pk},{$property}")->select();
        $options = [];

        foreach ($query as $item) {
            array_push($options, [
                'key' => $item[$pk],
                'text' => $item[$property],
            ]);
        }

        return $options;
    }

}
