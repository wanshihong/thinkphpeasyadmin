<?php


namespace easyadmin\app\libs;


class Breadcrumb
{
    /**
     * 面包屑列表
     * @var array
     */
    private $lists = [];

    private $template = 'breadcrumb';


    /**
     * @return array
     */
    public function get(): array
    {
        return $this->lists;
    }

    /**
     * @param array $lists
     * @return Breadcrumb
     */
    public function set(array $lists): Breadcrumb
    {
        $this->lists = $lists;
        return $this;
    }

    /**
     * 添加一个面包屑路径
     * @param $name
     * @param $url
     * @param string $icon
     * @return $this
     */
    public function add($name, $url, $icon = '')
    {
        array_push($this->lists, [
            'name' => $name,
            'url' => $url,
            'icon' => $icon
        ]);
        return $this;
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
     * @return Breadcrumb
     */
    public function setTemplate(string $template): Breadcrumb
    {
        $this->template = $template;
        return $this;
    }


    /**
     * 渲染页面
     * @return string
     * @throws \think\Exception
     */
    public function __toString(): string
    {
        //模板路径
        $path = $this->getTemplate();

        //渲染
        $template = new Template();
        $template->fetch($path, [
            'lists' => $this->get()
        ]);
        return '';
    }


}
