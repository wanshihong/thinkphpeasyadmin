<?php

namespace app\admin\controller;

use easyadmin\app\libs\Menu;
use easyadmin\app\libs\MenuItem;
use easyadmin\controller\Admin as easyAdmin;

class Admin extends easyAdmin
{


    protected $siteName = '网站标题';

    /**
     * 设置系统导航
     * @return Menu
     */
    public function configMenu(): Menu
    {
        return Menu::getInstance()->setItems([

            MenuItem::addItem('首页', 'index/index', ['icon' => 'layui-icon layui-icon-home']),


            MenuItem::addItem('系统', 'javascript:')->setChildren([
                MenuItem::addItem('管理员', 'manage/lists'),
            ]),

        ]);


    }


}
