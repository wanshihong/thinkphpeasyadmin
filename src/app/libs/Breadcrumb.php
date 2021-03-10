<?php


namespace easyadmin\app\libs;


use think\Exception;

class Breadcrumb
{
    /**
     * 面包屑列表
     * @var array
     */
    private $lists = [];

    private $template = 'public:breadcrumb';

    /**
     * 私有属性，用于保存实例
     * @var Breadcrumb
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
     * @return Breadcrumb
     */
    public static function getInstance(): Breadcrumb
    {
        //判断实例有无创建，没有的话创建实例并返回，有的话直接返回
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


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
     *
     * 索引说明
     * 首页是 0
     * 列表是 10
     * 添加编辑是 20
     * 查看是 30
     * 如果需要在中间穿插其他的面包屑 按照顺序插入即可
     *
     * @param int $index 插入索引, 输出的时候会按照这个索引有小到大排序,
     * @return $this
     */
    public function add($name, $url, $icon = '', $index = 0): Breadcrumb
    {
        $item = [
            'name' => $name,
            'url' => $url,
            'icon' => $icon
        ];

        if (array_key_exists($index, $this->lists)) {
            $index += 1;
        }
        $this->lists[$index] = $item;
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
     * @throws Exception
     */
    public function __toString(): string
    {
        //模板路径
        $path = $this->getTemplate();
        $lists = $this->get();
        ksort($lists);
        //渲染
        $template = new Template();
        $template->fetch($path, [
            'lists' => $lists
        ]);
        return '';
    }


}
