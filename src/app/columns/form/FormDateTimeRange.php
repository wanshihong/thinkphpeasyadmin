<?php


namespace easyadmin\app\columns\form;

use easyadmin\app\columns\ColumnClass;
use easyadmin\app\libs\Lib;
use think\db\Query;

class FormDateTimeRange extends FormDateTime
{

    protected $jsFiles = ['/easy_admin_static/js/laydate.js'];

    protected $options = [
        'format' => 'Y-m-d H:i:s',
        'in_format' => '',//strtotime , 或者传入一个匿名函数
        'end_field' => '', // 第二字段
        'jsFiles' => [],
        // layui-date 文档 https://www.layui.com/doc/modules/laydate.html#use
        // options 使用官方参数
        'options' => [
            'range' => '|',
            'type' => 'date',
        ]
    ];


    /**
     * 获取分隔符
     * @return mixed|string
     */
    protected function getSeparator(): string
    {
        $lib = new Lib();
        $options = $this->getOption('options', []);
        return $lib->getArrayValue($options, 'range', '|');

    }

    public function getValue(): array
    {
        $value = parent::getValue();

        if ($value === null) {
            return [null, null];
        }

        if (is_array($value)) {
            return $value;
        }

        //分隔符
        $separator = $this->getSeparator();

        if (strpos($value, $separator) === false) {
            return [null, null];
        }

        $arr = explode(" $separator ", $value);
        return [trim($arr[0]), trim($arr[1])];
    }


    /**
     * 设置当前字段的值
     * @param $value
     * @return ColumnClass
     */
    public function setValue($value): ColumnClass
    {

        $endField = $this->getOption('end_field');
        if ($endField) {
            $val1 = array_key_exists($this->getSelectAlias(), $value) ? $value[$this->getSelectAlias()] : '';
            $val2 = array_key_exists($endField, $value) ? $value[$endField] : '';
            $arr = [$val1, $val2];
        } else {
            $arr = $value;
        }

        $separator = $this->getSeparator();
        $this->value = implode(" {$separator} ", $arr);

        return $this;
    }


    public function formatValue()
    {
        $arr = $this->getValue();
        $separator = $this->getSeparator();
        if ($arr) {
            $arr = array_map(function ($value) {
                if (empty($value)) return '';
                $format = $this->getOption('format');

                if ($format === null) {
                    return $value;
                }

                if (is_callable($format) || function_exists($format)) {
                    $value = call_user_func($format, $value);
                } elseif (is_string($format)) {
                    $value = date($format, $value);
                }

                return $value;
            }, $arr);

            $val = implode(" {$separator} ", $arr);

            if ($val === " {$separator} ") return '';
            return $val;
        }
        return '';
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

        if (empty($value)) {
            return null;
        }

        $separator = $this->getSeparator();
        $arr = explode(" {$separator} ", $value);

        $ret = array_map(function ($item) {
            return $this->inFormat($item);
        }, $arr);

        //如果第二字段参数接收
        $endField = $this->getOption('end_field');
        if ($endField) {
            $data[$fieldName] = $ret[0];
            $data[$endField] = $ret[1];
            $this->setValue([
                $fieldName => $ret[0],
                $endField => $ret[1]
            ]);
        } else {
            $this->setValue($ret);
        }
        return $data;
    }


    /**
     * 获取查询字段
     * @param string $alias
     *
     * 是否获取查询字段:
     * true: 在 field 查询中使用,会连接上 as  select {field} from table
     * false: 在 where 条件中使用,不需要 as   select field from table {where}
     * @param bool $isSelect 是否获取查询字段
     * @param array $fields 外部查询所有字段得接收
     * @return string
     */
    public function getSelectField($alias = 't0', $isSelect = false, &$fields = []): string
    {

        //第一字段
        if (stripos($this->field, '.') === false) {
            $fieldName = $alias . '.' . $this->field;
        } else {
            $fieldName = $isSelect ? ($this->field . ' as ' . $this->getSelectAlias()) : $this->field;
        }
        array_push($fields, $fieldName);


        //第二字段
        $endField = $this->getOption('end_field');
        if ($endField) {
            if (stripos($endField, '.') === false) {
                $endFieldName = $alias . '.' . $endField;
            } else {
                $endFieldName = $isSelect ? ($endField . ' as ' . $endField) : $endField;
            }
            array_push($fields, $endFieldName);
        }

        return $fieldName;
    }


    /**
     * @param $data
     * @return mixed
     */
    public function formatData($data)
    {
        $options = $this->getOption('options', []);

        $lib = new Lib();

        $options['range'] = $this->getSeparator();
        $options['type'] = $lib->getArrayValue($options, 'type', 'date');
        if ($options['type'] === 'date') {
            unset($options['type']);
        }

        $data['options'] = json_encode($options);
        $render_class = 'lay-date' . time() . mt_rand(10000, 99999);
        $data['class'] .= " lay-date {$render_class}";
        $data['render_class'] = $render_class;
        return $data;
    }


    /**
     * 过滤器使用什么条件查询数据
     * @param Query $query
     * @param $alias
     */
    public function filterQuery(Query $query, $alias)
    {
        $this->requestValue();
        $val = $this->getValue();
        if ($val === null || (!$val[0] && !$val[1])) {
            return;
        }

        $callback = $this->getOption('filter_callback');
        if ($callback) {
            call_user_func($callback, $query, $alias, $val);
        } else {
            if ($val || is_numeric($val)) {
                $endField = $this->getOption('end_field');
                if ($endField) {
                    $query->where($this->getSelectField($alias), '>', $val[0]);
                    $query->where($endField, '<=', $val[1]);
                } else {
                    $query->where($this->getSelectField($alias), 'between', $val);
                }
            }
        }
    }


}
