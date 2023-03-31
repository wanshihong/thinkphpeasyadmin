<?php


namespace easyadmin\controller;


use easyadmin\app\libs\Breadcrumb;
use easyadmin\app\libs\Install;
use easyadmin\app\libs\Resource;
use easyadmin\app\libs\Template;
use easyadmin\app\libs\User;
use easyadmin\app\traits\CrudRewriteTrait;
use easyadmin\app\traits\CrudRoutersTrait;
use stdClass as stdClassAlias;
use think\Exception;
use think\response\Json;

class Admin
{
    use CrudRoutersTrait;
    use CrudRewriteTrait;

    //主键 字段名称
    protected string $pk = 'id';

    //配置是否使用软删除
    protected string $softDeleteField = '';//软删除字段
    protected int $softDeleteAfterVal = 1;//软删除后的值.  例如  is_delete=1 标识已经删除
    protected int $softDeleteBeforeVal = 0;//软删除前的值.  例如  is_delete=0 标识尚未删除


    protected string $pageName;//页面名称,默认使用表格名称; 页面标题,导航 会用到
    protected string $tableName;//数据库,数据表格名称
    protected string $siteName = '网站标题';
    protected array $jsFiles = [];
    protected array $cssFiles = [];


    /** @var array 赋值到页面的数据 */
    protected array $data = [];


    public function __construct()
    {
        new Install();
    }

    /**
     * 获取页面标题
     * @return string
     */
    protected function getPageName(): string
    {
        if (empty($this->pageName)) {
            $this->pageName = $this->getTableName();
        }
        return $this->pageName;
    }

    /**
     * 返回当前数据表格名称
     * @return string
     */
    protected function getTableName(): string
    {
        if (empty($this->tableName)) {
            $controller = request()->controller();
            $index = stripos($controller, '.');
            if ($index !== false) {
                $controller = substr($controller, $index+1);
            }
            $controller = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $controller));
            $this->tableName = $controller;
        }

        return strtolower($this->tableName);
    }

    /**
     * @param $data
     * @return Json
     */
    protected function success($data): Json
    {
        return json([
            'code' => 1,
            'msg' => 'ok',
            'data' => $data
        ]);
    }


    /**
     * @param $msg
     * @return Json
     */
    protected function error($msg): Json
    {
        return json([
            'code' => 0,
            'msg' => $msg,
            'data' => new stdClassAlias(),
        ]);
    }

    protected function assign($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * 页面的公共参数赋值
     * @param $data
     * @return mixed
     */
    private function _assignCommon($data): mixed
    {

        $data['__page_name__'] = $this->getPageName();

        //资源文件
        $resource = Resource::getInstance();
        $data['__css__'] = $resource->getCssFiles();
        $data['__js__'] = $resource->getJsFiles();

        //导航
        $data['__menu__'] = $this->configMenu();

        //面包屑
        $breadcrumb = Breadcrumb::getInstance();
        $data['__breadcrumb__'] = $breadcrumb;
        $data['__site_name__'] = $this->siteName;
        $data['static_root'] = $resource->getRoot();

        //用户信息
        $data['user'] = $this->getUser();
        return $data;
    }

    // 获取当前登陆的用户信息
    protected function getUser(): ?array
    {
        return User::getInstance()->getUser();
    }

    /**
     * 渲染模板
     * @param string $path
     * @param array $data
     * @return string
     * @throws Exception
     */
    protected function fetch(string $path = '', array $data = []): string
    {

        $data = $this->_assignCommon($data);

        //合并数组 输出到页面
        $data = array_merge($this->data, $data);

        $template = new Template();
        $template->fetch($path, $data);
        return '';
    }


}
