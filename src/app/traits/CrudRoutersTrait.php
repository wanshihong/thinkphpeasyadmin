<?php


namespace easyadmin\app\traits;


use easyadmin\app\columns\form\BaseForm;
use easyadmin\app\columns\form\FormAutocomplete;
use easyadmin\app\libs\ListQuery;
use easyadmin\app\libs\PageForm;
use easyadmin\app\libs\PageList;
use easyadmin\app\libs\PageShow;
use Exception as ExceptionAlias;
use think\db\exception\DataNotFoundException as DataNotFoundExceptionAlias;
use think\db\exception\DbException as DbExceptionAlias;
use think\db\exception\ModelNotFoundException as ModelNotFoundExceptionAlias;
use think\facade\Db;
use think\facade\Filesystem;
use think\response\Json as JsonAlias;
use stdClass as stdClassAlias;

/**
 * 路由类
 * Trait CrudRoutersTrait
 * @package easyadmin\app\traits
 */
trait CrudRoutersTrait
{

    /**
     * @return mixed
     * @throws DbExceptionAlias
     * @throws ExceptionAlias
     */
    public function lists()
    {
        //实例化查询
        $listQuery = new ListQuery();
        //实例化数据表格
        $page = new PageList($this->getTableName(), $this->pk);


        //配置字段
        $this->configList($page);
        $this->configListField($page->getField());
        $this->configListFilter($page->getFilter());

        //创建查询
        $listQuery->createQuery($page);

        //自定义查询相关
        $this->configListJoin($page, $listQuery->getQuery(), $listQuery->getAlias());
        $this->configListQuery($page, $listQuery->getQuery(), $listQuery->getAlias());
        $this->configListWhere($page, $listQuery->getQuery(), $listQuery->getAlias());

        //查询没有被删除的
        $listQuery->softDelete($this->softDeleteField, $this->softDeleteBeforeVal);

        //配置分页
        $this->configListPagination($page->getPage());

        //获取结果
        $listQuery->listResult($page);

        $this->configListBreadcrumb();

        return $this->fetch($page->getTemplate(), [
            'table' => $page,
            'addAction' => $page->getActionAdd(),
        ]);

    }


    /**
     * 启用与禁用
     * @return JsonAlias
     */
    public function enable(): JsonAlias
    {
        try {
            $id = request()->get('id');
            $field = request()->get('field');
            $value = request()->post('value', 0);

            if (empty($id) || empty($field)) throw new ExceptionAlias('缺少参数');

            $query = $this->getModel();
            $res = $query->where($this->pk, '=', $id)->save([$field => $value]);
            return $this->success([
                'res' => $res
            ]);
        } catch (ExceptionAlias $e) {
            return $this->error($e->getMessage());
        }

    }

    /**
     * 添加页面
     * @return mixed|string
     */
    public function add(): string
    {
        $page = new PageForm($this->getTableName(), $this->pk);

        $this->configFormField($page);
        $this->configFormAction($page->getAction());

        $this->configFormBreadcrumb();
        return $this->fetch($page->getTemplate(), [
            'forms' => $page->getFields(),
            'actions' => $page->getAction()
        ]);
    }

    /**
     * 修改页面
     * @return string
     * @throws DataNotFoundExceptionAlias
     * @throws DbExceptionAlias
     * @throws ModelNotFoundExceptionAlias
     * @throws ExceptionAlias
     */
    public function edit(): string
    {
        $id = request()->get($this->pk);
        if (empty($id)) throw new ExceptionAlias("缺少{$this->pk}参数");
        $page = new PageForm($this->getTableName(), $this->pk);
        $page->setId($id);

        $this->configFormField($page);
        $this->configFormAction($page->getAction());

        $page->createQuery();
        $this->formQuery($page->getQuery(), $page->getAlias());
        $data = $page->getResult();

        if (empty($data)) {
            throw new ExceptionAlias("参数{$id},查找不到内容");
        }

        $this->configFormBreadcrumb();
        return $this->fetch($page->getTemplate(), [
            'forms' => $page->getFields(),
            'actions' => $page->getAction()
        ]);
    }

    /**
     * 获取表单类型
     * 返回说明
     * add: 添加页面
     * edit: 编辑页面
     * @return string
     */
    protected function getFormType(): string
    {
        $id = request()->post($this->pk, request()->get($this->pk));
        return $id ? 'edit' : 'add';
    }

    /**
     * 保存记录
     * @return JsonAlias
     */
    public function form_save(): JsonAlias
    {
        try {
            $id = request()->post($this->pk, request()->get($this->pk));
            $page = new PageForm($this->getTableName(), $this->pk);
            $this->configFormField($page);

            $form = $this->formRequestParam($page->getFields());
            $data = $this->formSave($form, $id);

            return $this->success($data);
        } catch (ExceptionAlias $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 查看详细页面
     * @return mixed
     * @throws DataNotFoundExceptionAlias
     * @throws DbExceptionAlias
     * @throws ModelNotFoundExceptionAlias|ExceptionAlias
     */
    public function show()
    {
        $id = request()->get('id');
        if (empty($id)) {
            return redirect(url('lists'));
        }
        $show = new PageShow($this->getTableName(), $this->pk);
        $show->createQuery($id);
        $this->configShowQuery($show->getQuery(), $show->getAlias());
        $this->configShow($show);
        $this->configShowField($show->getField());
        $this->configShowAction($show->getAction());
        $row = $show->getResult();

        $this->configShowBreadcrumb();
        return $this->fetch($show->getTemplate(), [
            'result' => $row,
            'action' => $show->getAction(),
        ]);
    }


    /**
     * 删除一条数据
     * @return mixed
     */
    public function delete()
    {
        try {
            $id = request()->get('id');
            if (empty($id)) throw new ExceptionAlias('缺少参数');

            //创建查询
            $query = $this->getModel()->where($this->pk, $id);

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
     * @return mixed
     * @throws DbExceptionAlias
     * @throws DataNotFoundExceptionAlias
     * @throws ModelNotFoundExceptionAlias
     */
    public function autocomplete_select()
    {
        $pk = request()->get('pk');
        $table = request()->get('table');
        $property = request()->get('property');
        $search = request()->get('search');
        $default = request()->get('default');
        $fieldName = request()->get('field');

        if (empty($pk) || empty($table) || empty($property)) {
            return $this->error('缺少参数');
        }

        $page = new PageForm($this->getTableName(), $this->pk);
        $this->configFormField($page);


        // 获取自定义查询
        $query = false;
        /** @var BaseForm $field */
        foreach ($page->getFields() as $field) {
            if ($field->getField() != $fieldName) continue;
            $queryFun = $field->getOption('query');
            if (!is_callable($queryFun)) continue;
            if ($field instanceof FormAutocomplete) {
                $query = call_user_func($queryFun, $table, $pk, $property, $search, $default);
            }
        }

        $defArr = [];
        //默认的查询
        if (empty($query)) {
            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            $query = Db::name($table)->field("`{$pk}`,`{$property}`")->limit(30);

            //处理搜索
            if ($search) {
                $query->where($property, 'like', "%{$search}%");
            }

            //处理默认值
            if ($default) {
                $defArr = explode(",",$default);
                $query->where($pk,'in', $defArr);
            }

        }

        $res = $query->select();
        $result = [];
        foreach ($res as $item) {
            array_push($result, [
                'value' => $item[$pk],
                'name' => $item[$property],
                'selected'=>in_array($item[$pk],$defArr)
            ]);
        }

        return $this->success($result);

    }


    // 图片上传
    public function upload(): JsonAlias
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
        } catch (ExceptionAlias $e) {
            return json([
                'errno' => 0,
                'msg' => $e->getMessage(),
                'data' => new stdClassAlias()
            ]);
        }

    }

    public function export()
    {
        
    }


}
