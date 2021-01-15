<?php


namespace easyadmin\app\columns\form;


use easyadmin\app\columns\ColumnClass;
use easyadmin\app\libs\ListFilter;
use easyadmin\app\libs\Template;
use think\db\Query;
use think\Exception as ExceptionAlias;
use think\Request;

class BaseForm extends ColumnClass
{
    /**
     * @return array
     */
    public function getData(): array
    {
        $elemId = 'filter_' . $this->getSelectAlias();
        return [
            'field' => $this->getField(),//列的字段
            'label' => $this->getLabel(),//列的名称
            'attr' => $this->getOption('attr', ''), //列的属性
            'class' => $this->getOption('class', ''), //列的样式
            //dom 元素 id
            'elem_id' => str_replace(':', '_', $elemId),
            'type' => $this->getOption('type', 'text')
        ];
    }

    /**
     * 渲染一个表单输入框
     * @throws ExceptionAlias
     */
    public function __toString(): string
    {
        $template = new Template();

        $data = $this->getData();
        $data['value'] = $this->formatValue();
        $data['icon'] = $this->getOption('icon');
        $data = $this->formatData($data);
        $template->fetch($this->getTemplatePath(), $data);
        return '';
    }

    /**
     * 是否是隐藏表单
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this instanceof FormHidden;
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
            return null;
        }
        $value = $this->inFormat($value);
        $data[$fieldName] = $value;
        $this->setValue($value);
        return $data;
    }

    /**
     * 格式化数据
     * 格式化后存入数据库,或者操作数据库
     * @param $val
     * @return mixed
     */
    public function inFormat($val)
    {
        $ret = $val;

        $format = $this->getOption('in_format');
        if ($format === null) {
            $ret = $val;
        }

        if (is_callable($format) || function_exists($format)) {
            $ret = call_user_func($format, $val);
        }

        return $ret;
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
        if ($val === null) {
            return;
        }

        $callback = $this->getOption('filter_callback');
        if ($callback) {
            call_user_func($callback, $query, $alias, $val);
        } else {
            if ($val || is_numeric($val)) {
                $query->where($this->getSelectField($alias), '=', $val);
            }
        }
    }


}