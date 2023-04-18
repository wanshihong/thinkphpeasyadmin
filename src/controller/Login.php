<?php /** @noinspection PhpUnused */


namespace easyadmin\controller;


use easyadmin\app\libs\Menu;
use easyadmin\app\libs\User;
use think\captcha\facade\Captcha;
use think\Exception;
use think\facade\Cache;
use think\facade\Db;
use think\Response;
use think\response\Json as JsonAlias;
use think\response\Redirect;


class Login extends Admin
{


    /**
     * 登陆
     * @return string|JsonAlias
     * @throws Exception
     */
    public function login()
    {

        if (request()->isAjax()) {
            $username = request()->post('username');
            $password = request()->post('password');
            $code = request()->post('code');

            if (empty($username)) return $this->error('请输入账号');
            if (empty($password)) return $this->error('请输入密码');
            if (!captcha_check($code)) return $this->error('验证码错误');

            $user = Db::name(config('login.table_name'))->where('username', $username)->find();
            if (empty($user)) return $this->error('用户名不存在');

            if (User::getInstance()->encrypt($user['username'], $password) !== $user['password']) {
                return $this->error('密码输入错误');
            }

            User::getInstance()->save($user);

            Db::name(config('login.table_name'))->where('id', $user['id'])->update([
                'last_login_time' => time()
            ]);


            // 有来源地址,跳回来源地址
            $HTTP_REFERER = Cache::get('redirect.login.login_url');
            $retUrl = $HTTP_REFERER ?: (string)url(config('login.home_url'));

            return $this->success(['url' => $retUrl]);
        } else {
            $HTTP_REFERER = $_SERVER['HTTP_REFERER'] ?? '';
            if ($HTTP_REFERER) {
                Cache::set('login_referer', $HTTP_REFERER, 86400);
            }

            return $this->fetch('login:login', [
                'is_register' => config('login.register'),
                'is_find_pwd' => config('login.find_password'),
            ]);
        }

    }

    /**
     * 注册
     * @return string|JsonAlias
     * @throws Exception
     */
    public function register()
    {
        if (!config('login.register')) {
            return '为开通注册';
        }

        if (request()->isAjax()) {
            $username = request()->post('username');
            $password = request()->post('password');
            $rpassword = request()->post('rpassword');
            $code = request()->post('code');

            if (empty($username)) return $this->error('请输入账号');
            if (empty($password)) return $this->error('请输入密码');
            if ($password !== $rpassword) return $this->error('两次密码输入不一致');
            if (!captcha_check($code)) return $this->error('验证码错误');

            $user = Db::name(config('login.table_name'))->where('username', $username)->find();
            if ($user) return $this->error('用户已经存在,您可以直接登陆');

            $user = [
                'username' => $username,
                'password' => User::getInstance()->encrypt($username, $password),
                'reg_time' => time(),
                'last_login_time' => time(),
            ];

            $userId = Db::name(config('login.table_name'))->insertGetId($user);

            $user['id'] = $userId;
            User::getInstance()->save($user);


            return $this->success(['url' => (string)url(config('login.home_url'))]);
        } else {
            return $this->fetch('login:register');
        }
    }

    /**
     * 修改密码
     * @return string|JsonAlias|Redirect
     * @throws Exception
     */
    public function change_pwd()
    {
        if (request()->isAjax()) {
            $opassword = request()->post('opassword');
            $password = request()->post('password');
            $rpassword = request()->post('rpassword');
            $code = request()->post('code');

            if (empty($opassword)) return $this->error('请输入旧密码');
            if (empty($password)) return $this->error('请输入密码');
            if ($password !== $rpassword) return $this->error('两次密码输入不一致');
            if (!captcha_check($code)) return $this->error('验证码错误');


            $userId = User::getInstance()->getUserId();
            $user = Db::name(config('login.table_name'))->where('id', $userId)->find();
            if (empty($user)) {
                return redirect('login');
            }

            if (User::getInstance()->encrypt($user['username'], $opassword) !== $user['password']) {
                return $this->error('旧密码输入错误');
            }

            $newPwd = User::getInstance()->encrypt($user['username'], $password);
            if (User::getInstance()->encrypt($user['username'], $opassword) === $newPwd) {
                return $this->error('新密码不能和旧密码相同');
            }


            Db::name(config('login.table_name'))->where('id', $userId)->update([
                'password' => $newPwd
            ]);


            return $this->success(['url' => (string)url(config('login.home_url'))]);
        } else {
            return $this->fetch('login:change_pwd');
        }
    }

    //退出登录
    public function logout(): Redirect
    {
        User::getInstance()->clear();
        return redirect('login');
    }

    //找回密码
    public function find_pwd()
    {
        return '找回密码需要根据自己业务实现';
    }

    // 显示验证码
    public function verify(): Response
    {
        return Captcha::create();
    }

    //无权访问
    public function no_access()
    {
        return $this->fetch('login:no_access');
    }


}
