<?php


namespace easyadmin\app\libs;


use easyadmin\app\libs\Template as TemplateAlias;
use think\Exception as ExceptionAlias;

class Menu
{
    /**
     * 模板路径
     * @var string
     */
    private $template = 'public:menu';

    /**
     * 导航存放数组
     * @var array
     */
    private $items = [];


    /**
     * 私有属性，用于保存实例
     * @var Menu
     */
    private static $instance;

    //构造方法私有化，防止外部创建实例
    private function __construct()
    {
    }

    //克隆方法私有化，防止复制实例
    private function __clone()
    {
    }

    /**
     * 公有方法，用于获取实例
     * @return Menu
     */
    public static function getInstance(): Menu
    {
        //判断实例有无创建，没有的话创建实例并返回，有的话直接返回
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
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
     * @return Menu
     */
    public function setTemplate(string $template): Menu
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->setItemActive();
    }

    /**
     * @param array $items
     * @return Menu
     */
    public function setItems(array $items): Menu
    {
        $this->items = $items;
        return $this;
    }

    /**
     * 验证当前的菜单选项
     * @return array
     */
    public function setItemActive(): array
    {
        $items = [];
        /** @var MenuItem $item */
        foreach ($this->items as $item) {

            $item->checkActive();

            $childless = [];
            /** @var MenuItem $child */
            foreach ($item->getChildren() as $child) {
                $child->checkActive();
                array_push($childless, $child);
            }
            $item->setChildren($childless);
            array_push($items, $item);
        }
        $this->saveMenuRuntime($items);
        return $items;
    }

    public static function getMenuConfPath()
    {
        $userId = User::getInstance()->getUserId();
        return runtime_path() . 'menu' . $userId . '.json';
    }

    protected function formatUrl($url): string
    {
        $arr = explode('/', $url);
        array_pop($arr);
        return implode('/', $arr) . '/*';
    }

    /**
     * 把菜单写入到 runtime 目录
     * 如果配置了菜单,访问菜单可访问以外的地址时,拒绝
     * @param $menu
     */
    protected function saveMenuRuntime($menu)
    {


        $ret = [];
        foreach ($menu as $m) {
            if ($m->getUrl() != 'javascript:') {
                $url = $this->formatUrl($m->getUrl());
                $ret[$url] = User::getInstance()->getRoles();
            }

            if (empty($m->getChildren())) {
                continue;
            }
            foreach ($m->getChildren() as $c) {
                if ($c->getUrl() == 'javascript:') {
                    continue;
                }
                $url = $this->formatUrl($c->getUrl());
                $ret[$url] = User::getInstance()->getRoles();
            }
        }
        file_put_contents(
            self::getMenuConfPath(),
            json_encode($ret)
        );
    }

    /**
     * 渲染列表的新增按钮
     * @return string
     * @throws ExceptionAlias
     */
    public function __toString(): string
    {

        //模板路径
        $path = $this->getTemplate();

        //渲染
        $template = new TemplateAlias();
        $template->fetch($path, [
            'items' => $this->getItems(),
        ]);
        return '';
    }

}
