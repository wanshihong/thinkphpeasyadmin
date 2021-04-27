<?php


namespace easyadmin\app\libs;


use think\Exception as ExceptionAlias;
use think\Template;

/**
 * 列表操作按钮类
 * Class Actions
 * @package easyadmin\app\libs
 */
class Actions
{

    private $actions = [];


    /**
     * 列表模板路径
     * @var string
     */
    private $template = 'public:actions';


    private $options = [];

    /**
     * 设置模板路径
     * 设置列表的模板路径
     * @param string $template
     * @return Actions
     */
    public function setTemplate(string $template): Actions
    {
        $this->template = $template;
        return $this;
    }

    /**
     * 获取模板路径
     * @return Template
     */
    public function getTemplate(): string
    {
        return $this->template;
    }


    /**
     * 添加一个操作按钮
     * @param string $label 按钮显示名称,如果不传递,显示 key
     * @param string $url 按钮的URl, 如果没有指定URL 试图用key 去控制器找方法
     * @param array $options 按钮的其他属性, params, class ,icon , attr ,template
     * @return Actions
     * @throws ExceptionAlias
     */
    public function addAction($label = '', $url = '', $options = []): Actions
    {
        // 装进去方便后续取值
        $this->setOptions($options);

        $class = $this->getOption('class', []);
        if (!is_array($class)) {
            $class = explode(' ', $class);
        }

        $btn = new Btn();
        $btn->setLabel($label);
        $btn->setUrl($url);
        $btn->setClass($class);
        $btn->setIcon($this->getOption('icon', ''));
        $btn->setParams($this->getOption('params', []));
        $btn->setTemplate($this->getOption('template', 'public:btn'));
        $btn->setIsConfirm($this->getOption('confirm', ''));
        $btn->setBtnType($this->getOption('btn_type', 'btn'));
        $btn->setReferer($this->getOption('referer', false));
        $btn->setDataId($this->getOption('dataId', ''));
        $btn->setCondition($this->getOption('condition', null));

        array_push($this->actions, $btn);


        $resource = Resource::getInstance();
        foreach ($this->getOption('jsFiles', []) as $js) {
            $resource->appendJsFile($js);
        }
        foreach ($this->getOption('cssFiles', []) as $css) {
            $resource->appendCssFile($css);
        }


        return $this;
    }

    /**
     * 获取全部的按钮
     * @return array[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }


    /**
     * 渲染操作按钮
     * @return string
     * @throws ExceptionAlias
     */
    public function __toString(): string
    {
        $template = new \easyadmin\app\libs\Template();

        $template->fetch($this->getTemplate(), [
            'actions' => $this->getActions(),
        ]);
        return '';
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * 获取一个选项
     * @param string $name
     * @param false $default
     * @return false|mixed
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getOption($name = '', $default = null)
    {
        if ($this->options && is_array($this->options) && array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return $default;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @param array $actions
     * @return Actions
     */
    public function setActions(array $actions): Actions
    {
        $this->actions = $actions;
        return $this;
    }
}
