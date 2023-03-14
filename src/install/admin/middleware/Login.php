<?php


namespace app\admin\middleware;


use easyadmin\app\libs\User;
use think\facade\Cache;

class Login
{

    /**
     * 获取当前路由需要什么权限
     * true:  跳过,可以进入
     * false: 拦截到登录页面
     * string: 需要的权限, 自行进一步判断
     * @return bool|string
     */
    public function getRole()
    {
        $rules = config('login.rules');
        if (empty($rules)) return true;

        $url = strtolower($_SERVER['REQUEST_URI']);

        $match = [];
        foreach ($rules as $reg => $role) {
            $reg = str_replace('/', '\/', $reg);

            if (preg_match("/$reg/", $url)) {
                $match[strlen($reg)] = $role;
            }
        }
        if (empty($match)) return false;
        krsort($match);
        $match = array_merge($match);
        return $match[0];
    }


    public function handle($request, \Closure $next)
    {
        // 没有开启登陆
        if (!config('login.enable_login')) {
            return $next($request);
        }

        $needRole = $this->getRole();
        if ($needRole === true) {
            return $next($request);
        }

        if ($needRole === false) {
            return redirect(config('login.login_url'));
        }


        switch ($needRole) {
            case 'anonymous': // 可以匿名访问
                return $next($request);
            case 'login': // 登录就可以访问
                if (User::getInstance()->getUserId()) {
                    return $next($request);
                } else {
                    Cache::set('redirect.login.login_url', $_SERVER['REQUEST_URI']);
                    return redirect(config('login.login_url'));
                }
            default:
                //判断是否有权限, 无权限调整到无权限页面
                if (User::getInstance()->hasRole($needRole)) {
                    return $next($request);
                } else {
                    return redirect(config('login.no_access_url'));
                }

        }


    }
}
