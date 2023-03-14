<?php


namespace easyadmin\app\columns;


use easyadmin\app\columns\form\BaseForm;
use easyadmin\app\libs\Lib;
use easyadmin\app\libs\ListTableRow;
use easyadmin\app\libs\Resource;
use JetBrains\PhpStorm\Pure;

class ColumnClass
{

    protected array $jsFiles = [];//默认的JS 资源文件
    protected array $cssFiles = [];//默认的 css 资源文件

    /**
     * 列的字段
     * @var string
     */
    protected string $field;

    /**
     * 列的别名
     * @var string
     */
    protected string $selectAlias;

    /**
     * 列的显示名称
     * @var string
     */
    protected string $label;

    /**
     * 一行的数据
     * @var ListTableRow
     */
    protected ListTableRow $row;

    /**
     * 过滤器
     * @var array
     */
    protected array $filters;

    /**
     * 列的其他选项
     * @var array
     */
    protected array $options = [
        'attr' => '',//属性
        'class' => '',//样式表
        'format' => null,//格式化输出,格式化给用户看  例如: Y-m-d H:i:s  |  匿名函数 ;
        'in_format' => null,//格式化输入,格式化后操作数据库或者存入数据库  strtotime | 匿名函数
        'highlight' => false, //包含搜索的值是否高亮 ; false 关闭高亮, 不写或者 true 开启高亮
        'default' => null,//默认值
        'filter_callback' => null,//过滤器回调 参数有2个  query查询对象  $alias别名
        'end_field' => null,//日期时间范围选择器, 第二个字段,
        'copy' => true,//是否启用复制， 启用后；点击列就复制了
    ];

    /**
     * 列所对应的值
     * @var mixed
     */
    protected mixed $value = '';

    /**
     * 字段的模板路径
     * @var string
     */
    protected string $templatePath;


    public function __construct($field, $label = '', $options = [])
    {
        $this->field = $field;
        $this->label = $label ?: $field;
        $this->options = array_merge($this->options, $options);

        $this->setSelectAlias($field);
        $this->addStatic();
        $this->checkOptions();
    }

    //参数校验
    public function checkOptions()
    {

    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
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
    public function getSelectField(string $alias = 't0', bool $isSelect = false, array &$fields = []): string
    {
        if (stripos($this->field, '.') === false) {
            $fieldName = $alias . '.' . $this->field;
        } else {
            $fieldName = $isSelect ? ($this->field . ' as ' . $this->getSelectAlias()) : $this->field;
        }
        array_push($fields, $fieldName);
        return $fieldName;
    }

    /**
     * 获取查询字段的别名
     * @return string
     */
    public function getSelectAlias(): string
    {
        return $this->selectAlias;
    }

    /**
     * 设置字段的别名
     * @param string $field
     * @return ColumnClass
     */
    public function setSelectAlias(string $field): ColumnClass
    {
        if (stripos($field, ' as ') !== false) {
            //如果用户指定了别名的,直接使用用户的别名
            $arr = explode(' as ', $field);
            $this->field = trim($arr[0]);
            $field = trim($arr[1]);
        } else {
            //如果用户没有指定别名, 默认采用 字段名称做为别名
            if (stripos($field, '.') !== false) {
                $field = str_replace('.', '_', $field);
            }
        }

        $this->selectAlias = $field;
        return $this;
    }


    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * 获取一个选项
     * @param string $name
     * @param mixed $default
     * @return false|mixed
     * @noinspection PhpMissingReturnTypeInspection
     */
    #[Pure] public function getOption(string $name = '', mixed $default = '')
    {
        $lib = new Lib();
        return $lib->getArrayValue($this->options,$name,$default);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }


    #[Pure] public function getValue()
    {
        if ($this->value === '') {
            $default = $this->getOption('default');
            if ($default !== '') {
                return $default;
            }
        }

        return $this->value;
    }

    /**
     * 设置当前字段的值
     *
     * 加入  当前列名称是 username
     * 两种用法:
     * 1.  直接传递一个具体的值, 赋值上去   例如   setValue("张山");
     * 2.  传递一个 包含列名称的  数组, 取数组中的值  例如:  setValue(["username"=>"张山"])
     *
     * @param $value
     * @return ColumnClass
     */
    public function setValue($value): ColumnClass
    {
        $fieldAlias = $this->getSelectAlias();
        if (is_array($value) && array_key_exists($fieldAlias, $value)) {
            $this->value = $value[$fieldAlias] ?:'' ;
        } else {
            $this->value = $value;
        }
        return $this;
    }


    /**
     * @return string
     */
    #[Pure] public function getTemplatePath(): string
    {
        return $this->getOption('template') ?: $this->templatePath;
    }

    /**
     * 格式化数据
     * 格式化给用户看的
     * @return mixed
     */
    public function formatValue(): mixed
    {
        $ret = $this->getValue();
        if ($ret === '') {
            return '';
        }

        $format = $this->getOption('format');
        if ($format === '') {
            return $ret;
        }

        if ($format && (is_callable($format) || function_exists($format))) {
            $ret = call_user_func($format, $ret,$this->row->getRow());
        }
        return $ret;
    }

    /**
     * 列表中, 包含过滤器的内容高亮
     * @param $value
     * @return string
     */
    public function filterHighlight($value): string
    {
        if (empty($this->filters)) return $value;

        //替换value 中 包含  filter 的内容高亮
        /** @var BaseForm $filter */
        foreach ($this->filters as $filter) {
            if (!is_string($filter->getValue())) continue;
            $highlight = $filter->getOption('highlight');
            if (!$highlight) continue;

            //限定本字段高亮, 还是所有字段高亮
            if ($filter->getOption('highlight_self')) {
                if ($filter->getSelectAlias() != $this->getSelectAlias()) continue;
            }

            if (is_callable($highlight)) {
                $hValue = call_user_func($highlight, $filter->getValue());
            } else if (is_string($highlight)) {
                //自定义颜色
                $hValue = '<em style="color: ' . $highlight . '">' . $filter->getValue() . '</em>';
            } else {
                // 默认, 黄色背景白色字体
                $hValue = '<em class="layui-bg-orange">' . $filter->getValue() . '</em>';
            }

            $value = str_replace($filter->getValue(), $hValue, ''.$value);

        }
        return $value;
    }


    /**
     * @param array $filters
     * @return ColumnClass
     */
    public function setFilters(array $filters): ColumnClass
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * @param ListTableRow $row
     * @return ColumnClass
     */
    public function setRow(ListTableRow $row): ColumnClass
    {
        $this->row = $row;
        return $this;
    }

    public function formatData($data)
    {
        return $data;
    }

    /**
     * 添加资源文件到到页面
     */
    public function addStatic()
    {
        $resource = Resource::getInstance();

        //默认的 js
        foreach ($this->jsFiles as $js) {
            $resource->appendJsFile($js);
        }

        //默认的 css
        foreach ($this->cssFiles as $css) {
            $resource->appendCssFile($css);
        }

        //用户添加的 js
        foreach ($this->getOption('jsFiles', []) as $js) {
            $resource->appendJsFile($js);
        }

        //用户添加的 css
        foreach ($this->getOption('cssFiles', []) as $css) {
            $resource->appendCssFile($css);
        }


    }

}
