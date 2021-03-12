<?php


namespace app\admin\controller;


use easyadmin\app\columns\form\FormText;
use easyadmin\app\columns\lists\ListDateTime;
use easyadmin\app\columns\lists\ListText;
use easyadmin\app\libs\Btn;
use easyadmin\app\libs\ListField;
use easyadmin\app\libs\PageForm;
use easyadmin\app\libs\PageList;
use easyadmin\app\libs\User;
use easyadmin\app\libs\Verify;

/**
 * 管理员列表相关路由控制器
 * 可直接在里面扩展自己的业务
 * Class Manage
 * @package app\admin\controller
 */
class Manage extends Admin
{

    protected $roles = [
        'role_super' => '超级管理',
        'role_agent' => '代理商',
        'role_store' => '商户',
    ];


    protected $pageName = '管理员';

    public function __construct()
    {
        $this->tableName = config('login.table_name');
        parent::__construct();
    }

    protected function configList(PageList $page)
    {
        // 添加按钮到页面
        $page->setTemplate('manage:lists');
        if (User::getInstance()->hasRole('role_super')) {
            $page->addAction('修改密码', 'edit', [
                'icon' => 'layui-icon layui-icon-edit',
                'class' => ['layui-btn-primary', 'layui-btn-xs']
            ]);
        }

        $page->addAction('权限', 'javascript:', [
            'icon' => 'layui-icon layui-icon-edit',
            'class' => 'layui-btn-primary layui-btn-xs set_role'
        ]);

        $page->addAction('删除', 'delete', [
            'icon' => 'layui-icon layui-icon-delete',
            'class' => ['layui-btn-danger', 'layui-btn-xs'],
            'confirm' => '确定要删除数据吗?',
        ]);

        $addBtn = new Btn();
        $addBtn->setLabel('添加');
        $addBtn->setUrl('add');
        $addBtn->setIcon('layui-icon layui-icon-add-1');
        $page->setActionAdd($addBtn);


        //赋值到页面
        //不是超级管理员,不显示超级管理员权限到前台
        $roles = $this->roles;
        if (!User::getInstance()->hasRole('role_super')) {
            unset($roles['role_super']);
        }
        $this->assign('roles', $roles);

    }


    protected function configListField(ListField $list)
    {
        $list
            ->addField('id', 'ID', ListText::class)
            ->addField('username', '登录账号', ListText::class)
            ->addField('user_role', '权限', ListText::class, [
                'default' => '-',
                'format' => function ($val) {
                    if ($val == '-' || empty($val)) return '-';
                    $arr = unserialize($val);
                    $ret = '';
                    foreach ($arr as $value) {
                        $ret .= '<span class="roles-span" data-role="' . $value . '" style="background-color: #e2e2e2;color: #666666;padding: 0 6px;border-radius: 4px;display: inline-block;margin-right: 5px;font-size: 10px;">' . $this->roles[$value] . '</span>';
                    }
                    return $ret;
                }
            ])
            ->addField('reg_time', '注册时间', ListDateTime::class)
            ->addField('last_login_time', '登录时间', ListDateTime::class);
    }


    protected function configFormField(PageForm $page)
    {

        if ($this->getFormType() === 'edit') {
            $page
                ->addField('password', '登录密码', FormText::class, [
                    'required' => true,
                    'type' => 'password',
                    'verify' => (new Verify())
                        ->addRule('maxlength', '登录密码最多20个长度', 20)
                        ->addRule('minlength', '登录密码不能少于6个字符', 6)
                ]);
        } else {
            $page
                ->addField('username', '登录账号', FormText::class, [
                    'required' => true,
                    'verify' => (new Verify())
                        ->addRule('username', '登录账号不要包含特殊字符')
                        ->addRule('maxlength', '登录账号最多20个长度', 20)
                        ->addRule('minlength', '登录账号不能少于6个字符', 6)
                ])
                ->addField('password', '登录密码', FormText::class, [
                    'required' => true,
                    'type' => 'password',
                    'verify' => (new Verify())
                        ->addRule('maxlength', '登录密码最多20个长度', 20)
                        ->addRule('minlength', '登录密码不能少于6个字符', 6)
                ]);
        }


    }

    protected function insertBefore($data): array
    {
        $data['password'] = User::getInstance()->encrypt($data['username'], $data['password']);
        $data['reg_time'] = time();
        return $data;
    }

    protected function updateBefore($data): array
    {
        $id = request()->post($this->pk);
        $user = $this->getModel()->where($this->pk, $id)->find();
        $data['password'] = User::getInstance()->encrypt($user['username'], $data['password']);
        return $data;
    }

    /**
     * 设置用户的权限
     * @return \think\response\Json
     */
    public function set_role()
    {
        $id = request()->post('id');
        $roles = request()->post('roles');
        if (empty($id)) $this->error('缺少参数');

        if (empty($roles)) {
            $roles = [];
        } else {
            $roles = explode(',', $roles);
            $roles = array_unique(array_filter($roles));
        }

        //给自己设置的权限,更新到缓存中
        if ($id == User::getInstance()->getUserId()) {
            User::getInstance()->setRoles($roles);
        } else {
            $this->getModel()->where($this->pk, $id)->update([
                'user_role' => serialize($roles)
            ]);
        }

        return $this->success([]);
    }


}
