<?php


namespace easyadmin\app\libs;


use think\Exception;

/**
 * 模板类
 * 继承 thinkTemplate 类
 * thinkTemplate 是 thinkphp 的 Template 类
 * 原来的 private 部分修改成  protected 方便继承后重写
 * @package src\app\libs
 */
class Template extends ThinkTemplate
{

    protected $dirs = [];

    /**
     * 获取 easyadmin 默认模板路径
     * @return string
     */
    protected function getEasyAdminViewPath(): string
    {
        return dirname(dirname(__FILE__)) . '/views/';
    }

    public function __construct(array $config = [])
    {
        if (!array_key_exists('cache_path', $config)) {
            $config['cache_path'] = runtime_path() . 'easy_admin/';
        }

        //模板查找路径
        array_push($this->dirs, app_path() . 'views/');//自定义模板
        array_push($this->dirs, $this->getEasyAdminViewPath());//easyadmin 默认模板
        parent::__construct($config);
    }

    /**
     * 取得文件得全名称
     * @param $template
     * @return string
     */
    protected function getFullName($template): string
    {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            return $template . '.' . ltrim($this->config['view_suffix'], '.');
        } else {
            return $template;
        }
    }

    /**
     * 查找模板文件
     * @param $template
     * @return string
     * @throws Exception
     */
    public function findTemplate($template): string
    {
        //本来就写的全路径
        if (is_file($template)) {
            return $template;
        }

        if (0 !== strpos($template, '/')) {
            $template = str_replace(['/', ':'], $this->config['view_depr'], $template);
        } else {
            $template = '/' . str_replace(['/', ':'], $this->config['view_depr'], substr($template, 1));
        }

        //可能没有后缀,加上后缀
        $template = $this->getFullName($template);
        if (is_file($template)) {
            return $template;
        }

        foreach ($this->dirs as $dir) {
            $path = $this->getFullName($dir . $template);

            if (is_file($path)) {
                return $path;
            }
        }

        $dirStr = implode(',', $this->dirs);
        throw new Exception("目录{$dirStr}中都不存在{$template}");
    }


    /**
     * 解析模板文件名
     * @access private
     * @param string $template 文件名
     * @return string
     * @throws Exception
     */
    protected function parseTemplateFile(string $template): string
    {
        // @ 符号替换城模板路径
        if (substr($template, 0, 1) === '@') {
            $template = str_replace('@', $this->getEasyAdminViewPath(), $template);
        }

        //取得文件后缀,如果为空
        $template = $this->findTemplate($template);

        if (is_file($template)) {
            // 记录模板文件的更新时间
            $this->includeFile[$template] = filemtime($template);

            return $template;
        }

        throw new Exception('template not exists:' . $template);
    }

    /**
     * 渲染模板文件
     * @param string $template
     * @param array $vars
     * @throws Exception
     */
    public function fetch(string $template, array $vars = []): void
    {
        $template = $this->findTemplate($template);
        parent::fetch($template, $vars);
    }


}
