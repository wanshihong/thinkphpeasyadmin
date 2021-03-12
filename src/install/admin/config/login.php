<?php

// +----------------------------------------------------------------------
// | 登录注册相关配置
// +----------------------------------------------------------------------

return [
    // 数据库表名
    'table_name' => env('manage_table_name', 'manage'),

    // 是否开放注册
    'register' => env('manage_register', true),

    // 是否启用密码找回
    'find_password' => env('manage_find_password', true),

    // 密码加密的盐
    'crypt_salt' => env('manage_crypt_salt', 'easy_admin'),

    // 后台首页管理地址
    'home_url' => env('manage_home_url', '/admin/index/index'),

    // 后台登录地址
    'login_url' => env('manage_login_url', '/admin/login/login'),

    // 无权访问拦截地址
    'no_access_url' => env('manage_login_url', '/admin/login/no_access'),

    // 路由访问限制
    // 如果配置一个 空数组, 表示不验证权限,直接访问
    //
    // anonymous 表示可用匿名访问, 不登录
    // login     表示登录后可用访问
    // other     表示需要指定的权限可以访问 (直接写自己定义的权限名称)
    //
    'rules' => [
        '^/admin/*' => 'login',
        '^/admin/login/.*' => 'anonymous',

        // 为了方便演示,首页不用登录,其他页面都需要登录
        '^/admin/index/' => 'anonymous',

        // ... 其他路由规则
    ]

];
