<?php

namespace easyadmin\app\libs;

use easyadmin\app\libs\Template as TemplateAlias;
use think\Exception;
use think\Exception as ExceptionAlias;

/**
 * 按钮类
 * Class Btn
 * @package easyadmin\app\libs
 */
class Btn
{
    /**
     * @var string 按钮名称
     */
    private $label;

    /**
     * @var string 按钮URL
     */
    private $url;

    /**
     * 按钮URL 参数
     * @var array
     */
    private $params = [];

    /**
     * @var array 按钮的样式列表
     */
    private $class = [];

    /**
     * @var string 按钮的图标
     */
    private $icon;

    /**
     * @var string 按钮的模板路径
     */
    private $template = 'btn';

    /**
     * 点击按钮是否需要确认
     * 文字为真, 需要确认,并且提示相关的文字
     * @var string
     */
    private $isConfirm = '';

    /**
     * 按钮类型
     * a:  a   素 a 标签
     * btn:  layui-button  a 标签 添加了 layui-button 样式
     * button:  button-button  button标签 type button
     * submit:  button-submit  button标签 type submit
     * @var string
     */
    private $btnType = 'btn';


    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function addParam($key, $val)
    {
        $this->params[$key] = $val;
    }

    /**
     * @return array
     */
    public function getClass(): array
    {
        return $this->class;
    }

    /**
     * @param array $class
     */
    public function setClass(array $class)
    {
        $this->class = $class;
    }

    public function addClass($className)
    {
        array_push($this->class, $className);
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;
    }


    /**
     * 渲染列表的新增按钮
     * @return string
     * @throws ExceptionAlias
     */
    public function __toString(): string
    {

        $url = $this->getUrl();

        $params = $this->getParams();

        $url = Lib::formatUrl($url, $params);

        $href = $url;
        $confirmText = $this->getIsConfirm();
        if ($confirmText) {
            $href = "javascript:";
        }

        //模板路径
        $path = $this->getTemplate();

        //渲染
        $template = new TemplateAlias();
        $template->fetch($path, [
            'url' => $url,
            'class' => implode(' ', $this->getClass()),
            'icon' => $this->getIcon(),
            'label' => $this->getLabel(),
            'confirmText' => $confirmText,
            'href' => $href,
            'btnType' => $this->getBtnType()
        ]);
        return '';
    }

    /**
     * @return string
     */
    public function getIsConfirm(): string
    {
        return $this->isConfirm;
    }

    /**
     * @param string $isConfirm
     */
    public function setIsConfirm(string $isConfirm)
    {
        $this->isConfirm = $isConfirm;
    }

    /**
     * @return string
     */
    public function getBtnType(): string
    {
        return $this->btnType;
    }

    /**
     * @param string $btnType
     * @return $this
     * @throws ExceptionAlias
     */
    public function setBtnType(string $btnType): Btn
    {
        $types = ['a', 'btn', 'submit','button'];
        if (!in_array($btnType, $types)) {
            throw new Exception('按钮类型错误');
        }
        $this->btnType = $btnType;
        return $this;
    }


}
