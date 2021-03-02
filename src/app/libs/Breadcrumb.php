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
     * @param int $index 追加还是插入
     * @param int $length 是否替换,0\不替换, 大于0 替换的长度
     * @return $this
     */
    public function add($name, $url, $icon = '', $index = 0,$length=0): Breadcrumb
    {
        $item = [
            'name' => $name,
            'url' => $url,
            'icon' => $icon
        ];
        if ($index > 0) {
            array_splice($this->lists, $index, $length, [$item]);
        } else {
            array_push($this->lists, $item);
        }

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

        //渲染
        $template = new Template();
        $template->fetch($path, [
            'lists' => $this->get()
        ]);
        return '';
    }


}
