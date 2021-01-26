<?php


namespace easyadmin\controller;


use easyadmin\app\libs\Menu;
use easyadmin\app\traits\CrudRoutersTrait;
use Exception;
use stdClass as stdClassAlias;
use think\facade\Filesystem;
use think\Request as RequestAlias;
use think\response\Json;

class Admin
{
    use CrudRoutersTrait;


    //主键 字段名称
    protected $pk = 'id';

    //配置是否使用软删除
    protected $softDeleteField;//软删除字段
    protected $softDeleteAfterVal = 1;//软删除后的值.  例如  is_delete=1 标识已经删除
    protected $softDeleteBeforeVal = 0;//软删除前的值.  例如  is_delete=0 标识尚未删除


    protected $pageName;//页面名称,默认使用表格名称; 页面标题,导航 会用到
    protected $tableName;//数据库,数据表格名称

    /** @var Menu */
    protected $menu;
    /**
     * Request实例
     * @var RequestAlias
     */
    protected $request;


    public function __construct()
    {
        $this->menu = Menu::getInstance();
        $this->configMenu($this->menu);
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
            $this->tableName = $this->request->controller();
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
            'data' => new stdClassAlias()
        ]);
    }


    protected function configMenu(Menu $menu)
    {

    }

    // 图片上传
    public function upload()
    {
        try {
            // 获取表单上传文件 例如上传了001.jpg
            $files = request()->file();

            $paths = [];
            foreach ($files as $file) {
                // 上传到本地服务器
                $path = Filesystem::disk('public')->putFile('easy_admin', $file);
                array_push($paths, request()->domain() . '/storage/' . $path);
            }

            return json([
                'errno' => 0,
                'msg' => 'ok',
                'data' => $paths
            ]);
        } catch (Exception $e) {
            return json([
                'errno' => 0,
                'msg' => $e->getMessage(),
                'data' => new stdClassAlias()
            ]);
        }

    }


}
