<?php


namespace easyadmin\app\libs;


class MenuItem
{
    /**
     * 导航名称
     * @var string
     */
    private $name;

    /**
     * 导航URL
     * @var string
     */
    private $url;

    /**
     * 导航的选项
     * @var string[]
     */
    private $options = [
        'class' => '',// 自定义样式名称
        'icon' => '',//图标
        'attr' => '',//属性
        'params' => [],//url 参数
    ];

    /** @var array */
    private $children = [];

    /** @var MenuItem */
    private $parent;

    /**
     * 添加
     * @param $name
     * @param $url
     * @param array $options
     * @return MenuItem
     */
    public static function addItem($name, $url, $options = []): MenuItem
    {
        $self = new self();

        $params = array_key_exists('params', $options) ? $options['params'] : [];
        $url = Lib::formatUrl($url, $params);

        $self->setName($name);
        $self->setUrl((string)$url);
        $self->setOptions($options);

        return $self;
    }


    /**
     * 验证是否是当前访问的 url
     * @return bool
     */
    public function checkNowUrl(): bool
    {
        return stripos((string)$this->url,request()->pathinfo())!==false;
    }

    /**
     * 检查并且 激活当前按钮
     */
    public function checkActive()
    {
        if ($this->checkNowUrl()) {
            //如果是当前访问的URL 添加激活样式
            $this->addClass('layui-this');

            //如果有父菜单,添加展开样式
            if ($this->getParent()) {
                $this->getParent()->addClass('layui-nav-itemed');
            }
        }
    }


    public function addClass($className)
    {
        $class = $this->getOption('class');
        if (empty($class)) {
            $class = [];
        } else {
            $class = explode(' ', $class);
        }
        array_push($class, $className);
        $this->options['class'] = implode(' ', array_unique($class));
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return MenuItem
     */
    public function setName(string $name): MenuItem
    {
        $this->name = $name;
        return $this;
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
     * @return MenuItem
     */
    public function setUrl(string $url): MenuItem
    {
        $this->url = $url;
        return $this;
    }


    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }


    /**
     * @param string[] $options
     * @return MenuItem
     */
    public function setOptions(array $options): MenuItem
    {
        $this->options = $options;
        return $this;
    }

    /**
     * 获取一个选项
     * @param string $name
     * @param false $default
     * @return false|mixed
     */
    public function getOption($name = '', $default = false)
    {
        if ($this->options && is_array($this->options) && array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }
        return $default;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array $childrens
     * @return MenuItem
     */
    public function setChildren(array $childrens): MenuItem
    {
        /** @var MenuItem $children */
        foreach ($childrens as $children) {
            $children->setParent($this);
        }
        $this->children = $childrens;
        return $this;
    }

    /**
     * @return MenuItem
     */
    public function getParent(): ?MenuItem
    {
        return $this->parent;
    }

    /**
     * @param MenuItem $parent
     * @return MenuItem
     */
    public function setParent(MenuItem $parent): MenuItem
    {
        $this->parent = $parent;
        return $this;
    }


}
