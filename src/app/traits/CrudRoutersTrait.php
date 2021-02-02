<?php


namespace easyadmin\app\traits;


use easyadmin\app\libs\ListQuery;
use easyadmin\app\libs\PageForm;
use easyadmin\app\libs\PageList;
use easyadmin\app\libs\PageShow;
use Exception as ExceptionAlias;
use think\db\exception\DataNotFoundException as DataNotFoundExceptionAlias;
use think\db\exception\DbException as DbExceptionAlias;
use think\db\exception\ModelNotFoundException as ModelNotFoundExceptionAlias;
use think\Exception;
use think\facade\Db;
use think\Request;
use think\Request as RequestAlias;
use think\response\Json as JsonAlias;

/**
 * 路由类
 * Trait CrudRoutersTrait
 * @package easyadmin\app\traits
 */
trait CrudRoutersTrait
{

    /**
     * @param RequestAlias $request
     * @return mixed
     * @throws DbExceptionAlias
     * @throws ExceptionAlias
     */
    public function lists(Request $request)
    {
        $this->request = $request;

        //实例化查询
        $listQuery = new ListQuery($this->request);
        //实例化数据表格
        $page = new PageList($this->getTableName(), $this->getPageName(), $this->pk);

        //配置字段
        $this->configList($page);
        $this->configListField($page->getField());
        $this->configListFilter($page->getFilter());

        //创建查询
        $listQuery->createQuery($page);

        //自定义查询相关
        $this->configListJoin($listQuery->getQuery(), $listQuery->getAlias());
        $this->configQuery($listQuery->getQuery(), $listQuery->getAlias());
        $this->configWhere($listQuery->getQuery(), $listQuery->getAlias());

        //查询没有被删除的
        $listQuery->softDelete($this->softDeleteField, $this->softDeleteBeforeVal);

        //配置分页
        $this->configPage($page->getPage());

        //获取结果
        $listQuery->listResult($page);

        return $page->fetch($this->getPageName(), [
            'table' => $page,
            'addAction' => $page->getActionAdd(),
        ]);

    }


    /**
     * 启用与禁用
     * @param RequestAlias $request
     * @return JsonAlias
     */
    public function enable(Request $request): JsonAlias
    {
        try {
            $this->request = $request;
            $id = $request->get('id');
            $field = $request->get('field');
            $value = $request->post('value', 0);

            if (empty($id) || empty($field)) throw new ExceptionAlias('缺少参数');

            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            $query = Db::table($this->getTableName());
            $res = $query->where($this->pk, $id)->save([$field => $value]);
            return $this->success([
                'res' => $res
            ]);
        } catch (ExceptionAlias $e) {
            return $this->error($e->getMessage());
        }

    }

    /**
     * 添加页面
     * @param RequestAlias $request
     * @return mixed|string
     * @throws Exception
     */
    public function add(Request $request): string
    {
        $this->request = $request;
        $page = new PageForm($this->getTableName(), $this->getPageName(), $this->pk);

        $this->configFormField($page);
        $this->configFormAction($page->getAction());

        return $page->fetch($this->getPageName(), [
            'forms' => $page->getFields(),
            'actions' => $page->getAction()
        ]);
    }

    /**
     * 修改页面
     * @param RequestAlias $request
     * @return mixed|string
     * @throws Exception
     */
    public function edit(Request $request): string
    {
        $id = $request->get($this->pk);
        if (empty($id)) throw new Exception("缺少{$this->pk}参数");
        $this->request = $request;
        $page = new PageForm($this->getTableName(), $this->getPageName(), $this->pk);
        $page->setId($id);

        $this->configFormField($page);
        $this->configFormAction($page->getAction());

        $page->createQuery();
        $this->formQuery($page->getQuery(), $page->getAlias());
        $data = $page->getResult();

        if (empty($data)) {
            throw new Exception("参数{$id},查找不到内容");
        }

        return $page->fetch($this->getPageName(), [
            'forms' => $page->getFields(),
            'actions' => $page->getAction()
        ]);
    }

    /**
     * 保存记录
     * @param RequestAlias $request
     * @return JsonAlias
     */
    public function form_save(Request $request): JsonAlias
    {
        try {
            $this->request = $request;
            $page = new PageForm($this->getTableName(), $this->getPageName(), $this->pk);
            $this->configFormField($page);
            $id = $request->post($this->pk);

            $form = $this->formRequestParam($page->getFields());

            $data = $this->formSave($form, $id);

            return $this->success($data);
        } catch (ExceptionAlias $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 查看详细页面
     * @param RequestAlias $request
     * @return mixed
     * @throws DataNotFoundExceptionAlias
     * @throws DbExceptionAlias
     * @throws ModelNotFoundExceptionAlias|Exception
     */
    public function show(Request $request)
    {
        $this->request = $request;
        $id = $request->get('id');
        if (empty($id)) {
            return redirect(url('lists'));
        }
        $show = new PageShow($this->getTableName(), $this->getPageName(), $this->pk);
        $this->configShow($show);
        $show->createQuery($id);
        $this->configShowQuery($show->getQuery(), $show->getAlias());
        $this->configShow($show);
        $this->configShowField($show->getField());
        $this->configShowAction($show->getAction());
        $row = $show->getResult();

        return $show->fetch($this->getPageName(), [
            'result' => $row,
            'action' => $show->getAction(),
        ]);
    }


    /**
     * 删除一条数据
     * @param RequestAlias $request
     * @return mixed
     */
    public function delete(Request $request)
    {
        try {
            $this->request = $request;
            $id = $request->get('id');
            if (empty($id)) throw new ExceptionAlias('缺少参数');

            //创建查询
            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            $query = Db::table($this->getTableName())->where($this->pk, $id);

            //删除
            if ($this->softDeleteField) {
                //软删除
                $query->update([$this->softDeleteField => $this->softDeleteAfterVal]);
            } else {
                //直接删除
                $query->delete();
            }
            return $this->success([
                'url' => url('lists')
            ]);
        } catch (ExceptionAlias $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 搜索下拉框, 搜索值
     * @param RequestAlias $request
     * @return mixed
     * @throws DbExceptionAlias
     * @throws DataNotFoundExceptionAlias
     * @throws ModelNotFoundExceptionAlias
     * @noinspection PhpUnused
     */
    public function autocomplete_select(Request $request)
    {
        $pk = $request->get('pk');
        $table = $request->get('table');
        $property = $request->get('property');
        $search = $request->get('search');
        $default = $request->get('default');

        if (empty($pk) || empty($table) || empty($property)) {
            return $this->error('缺少参数');
        }

        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $query = Db::table($table)->field("`{$pk}`,`{$property}`")->limit(30);
        if ($search) {
            $query->where($property, 'like', "%{$search}%");
        }
        if ($default) {
            $query->where($pk, $default);
        }

        $res = $query->select();
        $result = [];
        foreach ($res as $item) {
            array_push($result, [
                'value' => $item[$pk],
                'name' => $item[$property],
            ]);
        }

        return $this->success($result);

    }

}
