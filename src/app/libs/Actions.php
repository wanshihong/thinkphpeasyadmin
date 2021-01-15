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
    private $template = 'actions';

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
        $class = array_key_exists('class', $options) ? $options['class'] : [];
        $icon = array_key_exists('icon', $options) ? $options['icon'] : '';
        $params = array_key_exists('params', $options) ? $options['params'] : [];
        $template = array_key_exists('template', $options) ? $options['template'] : 'btn';
        $confirmText = array_key_exists('confirm', $options) ? $options['confirm'] : '';
        $btnType = array_key_exists('btn_type', $options) ? $options['btn_type'] : 'btn';

        $btn = new Btn();
        $btn->setClass($class);
        $btn->setIcon($icon);
        $btn->setLabel($label);
        $btn->setParams($params);
        $btn->setTemplate($template);
        $btn->setIsConfirm($confirmText);
        $btn->setUrl($url);
        $btn->setBtnType($btnType);

        array_push($this->actions, $btn);
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
}
