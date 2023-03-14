<?php


namespace easyadmin\app\columns\form;


use easyadmin\app\columns\ColumnClass;
use easyadmin\app\libs\Resource;
use easyadmin\app\libs\Template;
use easyadmin\app\libs\Verify;
use Exception as ExceptionAlias;
use think\db\Query;

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
            'type' => $this->getOption('type', 'text'),
            'placeholder' => $this->getOption('placeholder', ''),//输入提示
            'required' => $this->getOption('required', false), //是否必填
            'verify' => $this->getOption('verify', []), //验证器,
            'help' => $this->getOption('help', ''), //表单说明,
            'static_root' => Resource::getInstance()->getRoot()
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
     * @return array|null
     */
    public function requestValue(array &$data = []): ?array
    {
        $fieldName = $this->getSelectAlias();

        //接收用户参数
        $value = request()->post($fieldName, request()->get($fieldName, $this->getOption('default')));

        if ($value === null) {
            return null;
        }
        $value = $this->inFormat($value);
        $data[$fieldName] = $value;
        $this->setValue($value);
        return $data;
    }

    public function verify($value)
    {
        if (is_array($value)) {
            foreach ($value as $v) {
                $res = $this->verifyAction($v);
                if ($res !== true) {
                    return $res;
                }
            }
            return true;
        } else {
            return $this->verifyAction($value);
        }
    }

    public function verifyAction($value)
    {

        //必填验证
        if ($this->getOption('required') && empty($value)) {
            return ('请输入' . $this->getLabel());
        }

        // 不是必填, 0个长度表示没输入,不验证
        if (strlen($value) === 0) return true;

        //自定义验证
        $rule = $this->getOption('verify');

        //自定义函数验证
        if (is_callable($rule)) {
            $res = call_user_func($rule, $value);
            if ($res !== true) {
                return $res;
            }
        }

        if ($rule instanceof Verify) {
            $res = $rule->verify($value);
            if ($res !== true) {
                return $res;
            }
        }

        return true;

    }

    /**
     * 格式化数据
     * 格式化后存入数据库,或者操作数据库
     * @param $val
     * @return mixed
     */
    public function inFormat($val): mixed
    {
        $ret = $val;

        $format = $this->getOption('in_format');
        if ($format === null) {
            $ret = $val;
        }
    
        if ($format && (is_callable($format) || function_exists($format))) {
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
