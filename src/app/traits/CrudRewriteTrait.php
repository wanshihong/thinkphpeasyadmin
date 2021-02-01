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

    /**
     * 定义列表字段
     * @param PageList $page
     */
    protected function configList(PageList $page)
    {
        $page->addAction('查看', 'show', [
            'icon' => 'layui-icon-release',
            'class' => ['layui-btn-primary', 'layui-btn-xs'],
        ])->addAction('编辑', 'edit', [
            'icon' => 'layui-icon-edit',
            'class' => ['layui-btn-primary', 'layui-btn-xs']
        ])->addAction('删除', 'delete', [
            'icon' => 'layui-icon-delete',
            'class' => ['layui-btn-danger', 'layui-btn-xs'],
            'confirm' => '确定要删除数据吗?',
        ]);

        $addBtn = new Btn();
        $addBtn->setLabel('添加');
        $addBtn->setUrl('add');
        $addBtn->setIcon('layui-icon-add-1');
        $page->setActionAdd($addBtn);
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
     * @param Query $query
     * @param string $alias 主表的别名
     */
    protected function configListJoin(Query $query, string $alias)
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
     * @param Query $query
     * @param $alias
     */
    protected function configQuery(Query $query, $alias)
    {

    }

    /**
     * 设置查询条件
     * @param Query $query
     * @param $alias
     */
    protected function configWhere(Query $query, $alias)
    {

    }

    /**
     * 查看详情配置
     * @param PageShow $show
     */
    protected function configShow(PageShow $show)
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
        $id = $this->request->get('id');
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
        $id = $this->request->get($this->pk);
        if (empty($id)) return;

        $params = [
            $this->pk => $id
        ];

        $action->addAction('返回', 'window.history.back();', [
            'icon' => 'layui-icon-return',
            'class' => ['layui-btn-primary'],
            'params' => $params
        ])->addAction('编辑', 'edit', [
            'icon' => 'layui-icon-edit',
            'class' => [''],
            'params' => $params
        ])->addAction('删除', 'delete', [
            'icon' => 'layui-icon-delete',
            'class' => ['layui-btn-danger'],
            'confirm' => '确定要删除数据吗?',
            'params' => $params
        ]);
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
//            $label = $item['Comment'];
            $class = FormText::class;
            if ($field == $this->pk) {
                $class = FormHidden::class;
            }
            $page->addField($field, $field, $class);
        }

    }


    /**
     * 查看详情按钮配置
     * @param Actions $action
     * @throws ExceptionAlias
     */
    protected function configFormAction(Actions $action)
    {
        $id = $this->request->get($this->pk);

        $params = [];
        if ($id) {
            $params = [$this->pk => $id];
        }

        $action->addAction('取消', 'window.history.back();', [
            'icon' => 'layui-icon-return',
            'class' => ['layui-btn-primary'],
            'params' => $params
        ])->addAction($id ? '更新' : '添加', 'javascript:', [
            'icon' => 'layui-icon-ok',
            'params' => $params,
            'btn_type' => 'submit',
            'confirm' => '确认提交吗?'
        ]);

        if ($id) {
            $action->addAction('删除', 'delete', [
                'icon' => 'layui-icon-delete',
                'class' => ['layui-btn-danger'],
                'confirm' => '确定要删除数据吗?',
                'params' => $params
            ]);
        }

    }

    /**
     * form 表单接收参数
     * @param $fields
     * @return array
     */
    protected function formRequestParam($fields): array
    {
        $form = [];
        /** @var BaseForm $field */
        foreach ($fields as $field) {
            $field->requestValue($form);
        }
        return $this->requestAfter($form);
    }


    /**
     * 表单保存数据
     * @param $data
     * @param $id
     * @return int|mixed|string
     * @throws DbException
     * @noinspection PhpUndefinedClassInspection
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
     * @return int|string
     */
    protected function formInsert($data)
    {
        $data = $this->insertBefore($data);
        $data = $this->verifyData($data);
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $id = Db::name($this->getTableName())->insertGetId($data);
        $data[$this->pk] = $id;
        $data = $this->insertAfter($data);
        return $data;
    }

    /**
     * 表单新增
     * @param $data
     * @param $id
     * @return mixed
     * @throws DbException
     * @noinspection PhpDynamicAsStaticMethodCallInspection
     * @noinspection PhpUndefinedClassInspection
     */
    protected function formUpdate($data, $id)
    {
        $data = $this->updateBefore($data);
        $data = $this->verifyData($data);
        Db::name($this->getTableName())
            ->where($this->pk, $id)
            ->update($data);
        $data[$this->pk] = $id;
        $data = $this->updateAfter($data);
        return $data;
    }

    //接收参数之后执行
    protected function requestAfter($data)
    {
        return $data;
    }

    //写入之前执行
    protected function insertBefore($data)
    {
        return $data;
    }

    //写入之后执行
    protected function insertAfter($data)
    {
        return $data;
    }

    //更新之前执行
    protected function updateBefore($data)
    {
        return $data;
    }

    //更新之后执行
    protected function updateAfter($data)
    {
        return $data;
    }

    //添加和更新 数据验证
    protected function verifyData($data)
    {
        return $data;
    }

    //更新列表的查询语句
    protected function formQuery(Query $query, $alias)
    {

    }

}
