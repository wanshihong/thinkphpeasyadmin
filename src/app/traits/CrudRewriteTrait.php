<?php


namespace easyadmin\app\traits;

use easyadmin\app\columns\form\BaseForm;
use easyadmin\app\columns\form\FormHidden;
use easyadmin\app\columns\form\FormText;
use easyadmin\app\columns\lists\ListText;
use easyadmin\app\libs\Btn;
use easyadmin\app\libs\Actions;
use easyadmin\app\libs\ListField;
use easyadmin\app\libs\ListFilter;
use easyadmin\app\libs\Page;
use easyadmin\app\libs\PageForm;
use easyadmin\app\libs\PageList;
use easyadmin\app\libs\Pagination;
use easyadmin\app\libs\PageShow;
use think\db\exception\DataNotFoundException as DataNotFoundExceptionAlias;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException as ModelNotFoundExceptionAlias;
use think\db\Query;
use think\Exception as ExceptionAlias;
use think\facade\Db;

/**
 * CRUD 开放出去的重写接口
 * Trait CrudRewriteTrait
 * @package easyadmin\app\traits
 */
trait CrudRewriteTrait
{
    protected $disabledAction = [];

    /**
     * 定义列表的默认按钮
     * @param PageList $page
     */
    protected function configList(PageList $page)
    {
        if (!in_array('show', $this->disabledAction)) {
            $page->addAction('查看', 'show', [
                'icon' => 'layui-icon layui-icon-release',
                'class' => ['layui-btn-primary', 'layui-btn-xs'],
            ]);
        }
        if (!in_array('edit', $this->disabledAction)) {
            $page->addAction('编辑', 'edit', [
                'icon' => 'layui-icon layui-icon-edit',
                'class' => ['layui-btn-primary', 'layui-btn-xs']
            ]);
        }
        if (!in_array('delete', $this->disabledAction)) {
            $page->addAction('删除', 'delete', [
                'icon' => 'layui-icon layui-icon-delete',
                'class' => ['layui-btn-danger', 'layui-btn-xs'],
                'confirm' => '确定要删除数据吗?',
            ]);
        }

        if (!in_array('new', $this->disabledAction)) {
            $addBtn = new Btn();
            $addBtn->setLabel('添加');
            $addBtn->setUrl('add');
            $addBtn->setIcon('layui-icon layui-icon-add-1');
            $page->setActionAdd($addBtn);
        }
    }

    /**
     * 定义列表字段
     * @param ListField $list
     */
    protected function configListField(ListField $list)
    {
        //默认查询数据库的字段添加上去
        /** @noinspection PhpUndefinedMethodInspection */
        $fields = Db::query("show full columns from {$this->getTableName()}");

        foreach ($fields as $field) {
            //设置主键
            if ($field['Key'] === 'PRI') {
                $this->pk = $field['Field'];
            }
            //添加字段
            $list->addField($field['Field'], $field['Field'], ListText::class);
        }

    }

    /**
     * 设置分页相关
     * @param Pagination $page
     */
    protected function configPage(Pagination $page)
    {

    }

    /**
     * 列表查询设置 join
     * @param Page $page
     * @param Query $query
     * @param string $alias 主表的别名
     */
    protected function configListJoin(Page $page, Query $query, string $alias)
    {

    }

    /**
     * 定义列表过滤器
     * @param ListFilter $filter
     */
    protected function configListFilter(ListFilter $filter)
    {
        $filter->addFilter('_query_', ' ', FormText::class);
    }

    /**
     * 自定义查询
     * @param Page $page
     * @param Query $query
     * @param $alias
     */
    protected function configListQuery(Page $page, Query $query, $alias)
    {

    }

    /**
     * 设置查询条件
     * @param Page $page
     * @param Query $query
     * @param $alias
     */
    protected function configListWhere(Page $page, Query $query, $alias)
    {

    }

    /**
     * 查看详情配置
     * @param PageShow $page
     */
    protected function configShow(PageShow $page)
    {
    }

    /**
     * 查看字段配置
     * @param ListField $field
     * @throws DataNotFoundExceptionAlias
     * @throws DbException
     * @throws ModelNotFoundExceptionAlias
     * @noinspection PhpUndefinedClassInspection
     */
    protected function configShowField(ListField $field)
    {
        $id = request()->get('id');
        if (empty($id)) return;
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $res = Db::table($this->getTableName())->where($this->pk, $id)->find();
        if (empty($res)) return;

        foreach ($res as $key => $val) {
            $field->addField($key, $key, ListText::class);
        }

    }


    /**
     * 查看详情按钮配置
     * @param Actions $action
     * @throws ExceptionAlias
     */
    protected function configShowAction(Actions $action)
    {
        $id = request()->get($this->pk);
        if (empty($id)) return;

        $params = [
            $this->pk => $id
        ];

        $action->addAction('返回', 'javascript:', [
            'icon' => 'layui-icon layui-icon-return',
            'class' => ['layui-btn-primary'],
            'params' => $params,
            'referer' => true,
        ]);

        if (!in_array('edit', $this->disabledAction)) {
            $action->addAction('编辑', 'edit', [
                'icon' => 'layui-icon layui-icon-edit',
                'class' => [''],
                'params' => $params
            ]);
        }
        if (!in_array('delete', $this->disabledAction)) {
            $action->addAction('删除', 'delete', [
                'icon' => 'layui-icon layui-icon-delete',
                'class' => ['layui-btn-danger'],
                'confirm' => '确定要删除数据吗?',
                'params' => $params
            ]);
        }

    }


    /**
     * 详情 查询语句
     * @param Query $query
     * @param $alias
     */
    protected function configShowQuery(Query $query, $alias)
    {

    }

    /**
     * 设置表单字段
     * @param PageForm $page
     */
    protected function configFormField(PageForm $page)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $res = Db::query("show full fields from {$this->getTableName()}");
        if (empty($res)) return;

        foreach ($res as $item) {
            $field = $item['Field'];
            $class = FormText::class;
            if ($field == $this->pk) {
                $class = FormHidden::class;
            }
            $page->addField($field, $field, $class);
        }

    }


    /**
     * 添加和更新页面的操作按钮
     * @param Actions $action
     * @throws ExceptionAlias
     */
    protected function configFormAction(Actions $action)
    {
        $id = request()->get($this->pk);

        $params = [];
        if ($id) {
            $params[$this->pk] = $id;
        }

        $action->addAction('取消', 'window.history.back();', [
            'icon' => 'layui-icon layui-icon-return',
            'class' => 'layui-btn-primary',
            'params' => $params,
        ]);

        $action->addAction($id ? '更新' : '添加', 'javascript:', [
            'icon' => 'layui-icon layui-icon-ok',
            'params' => $params,
            'btn_type' => 'submit',
            'referer' => true
        ]);

        if ($id && !in_array('delete', $this->disabledAction)) {
            $action->addAction('删除', 'delete', [
                'icon' => 'layui-icon layui-icon-delete',
                'class' => ['layui-btn-danger'],
                'confirm' => '确定要删除数据吗?',
                'params' => $params,
                'referer' => true
            ]);
        }

    }

    /**
     * form 表单接收参数
     * @param $fields
     * @return array
     * @throws ExceptionAlias
     */
    protected function formRequestParam($fields): array
    {
        $form = [];
        /** @var BaseForm $field */
        foreach ($fields as $field) {
            //接收表单参数
            $field->requestValue($form);

            //处理表单验证
            $check = $field->verify($field->getValue());
            if ($check !== true) {
                throw new ExceptionAlias($check);
            }
        }
        return $this->requestAfter($form);
    }


    /**
     * 表单保存数据
     * @param $data
     * @param $id
     * @return int|mixed|string
     * @throws DbException
     */
    protected function formSave($data, $id)
    {
        if ($id) {
            return $this->formUpdate($data, $id);
        } else {
            return $this->formInsert($data);
        }
    }

    /**
     * 表单写入
     * @param $data
     * @return array
     */
    protected function formInsert($data): array
    {
        // 数据验证
        $data = $this->verifyData($data);

        //写入之前数据处理
        $data = $this->insertBefore($data);

        //软删除字段 添加的时候,如果没有设置值就 默认值赋值
        if ($this->softDeleteField && !array_key_exists($this->softDeleteField, $data)) {
            $data[$this->softDeleteField] = $this->softDeleteBeforeVal;
        }

        $id = $this->getModel()->save($data);
        $data[$this->pk] = $id;

        //写入之后数据处理
        $data = $this->insertAfter($data);
        return $data;
    }

    /**
     * 表单新增
     * @param $data
     * @param $id
     * @return mixed
     * @throws DbException
     */
    protected function formUpdate($data, $id)
    {
        $data = $this->verifyData($data);
        $data = $this->updateBefore($data);
        $this->getModel()
            ->where($this->pk, $id)
            ->save($data);
        $data[$this->pk] = $id;
        $data = $this->updateAfter($data);
        return $data;
    }

    //接收参数之后执行, post 接收到参数以后就调用
    protected function requestAfter($data): array
    {
        return $data;
    }

    //写入之前执行, 调用 insert 写入方法之前调用
    protected function insertBefore($data): array
    {
        return $data;
    }

    //写入之后执行,调用 insert 写入方法之后调用
    protected function insertAfter($data): array
    {
        return $data;
    }

    //更新之前执行, 调用 update 更新方法之前调用
    protected function updateBefore($data): array
    {
        return $data;
    }

    //更新之后执行, 调用 update 更新方法之后调用
    protected function updateAfter($data): array
    {
        return $data;
    }

    //添加和更新 数据验证
    // 在 requestAfter 之后 insertBefore updateBefore 之前执行
    protected function verifyData($data): array
    {
        return $data;
    }

    //更新页面的查询语句
    protected function formQuery(Query $query, $alias)
    {

    }

    /**
     * 获取模型,如果模型定义就返回模型,
     * 否则返回查询 DB类
     * @return mixed
     */
    protected function getModel()
    {
        $tableName = $this->getTableName();
        $modelName = "app\model\\" . ucfirst($tableName);
        if (class_exists($modelName)) {
            return new $modelName();
        } else {
            return Db::name($this->getTableName());
        }

    }
}
