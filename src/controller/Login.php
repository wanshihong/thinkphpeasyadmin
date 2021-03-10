<?php


namespace easyadmin\controller;


use think\captcha\facade\Captcha;
use think\Exception;
use think\facade\Session;
use think\response\Json as JsonAlias;


class Login extends Admin
{

    protected $tableName = 'manage';//用户表名称
    protected $isRegister = true;//是否开启注册
    protected $isFindPwd = true;//是否开启注册
    protected $salt = 'EasyAdmin';//密码 加密的盐
    protected $homeUrl = '/admin/index/index'; //登录注册成功以后跳转到那个页面

    /**
     * 加密密码
     * @param $username
     * @param $password
     * @return string
     */
    protected function encrypt($username, $password)
    {
        $md5 = md5($username . $password . $this->salt);
        $num = preg_replace('/\D/s', '', $md5);
        return sha1($md5 . $num) . $num;
    }

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

            $user = $this->getModel()->where('username', $username)->find();
            if (empty($user)) return $this->error('用户名不存在');

            if ($this->encrypt($user['username'], $password) !== $user['password']) {
                return $this->error('密码输入错误');
            }

            unset($user['password']);
            Session::set('user', $user);
            Session::set('user_id', $user['id']);

            return $this->success(['url' => (string)url($this->homeUrl)]);
        } else {
            return $this->fetch('login:login', [
                'is_register' => $this->isRegister,
                'is_find_pwd' => $this->isFindPwd,
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
        if (!$this->isRegister) {
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

            $user = $this->getModel()->where('username', $username)->find();
            if ($user) return $this->error('用户已经存在,您可以直接登陆');

            $user = [
                'username' => $username,
                'password' => $this->encrypt($username, $password),
                'reg_time' => time(),
                'last_login_time' => time(),
            ];

            $userId = $this->getModel()->insertGetId($user);

            unset($user['password']);
            $user['id'] = $userId;
            Session::set('user', $user);
            Session::set('user_id', $userId);

            return $this->success(['url' => (string)url($this->homeUrl)]);
        } else {
            return $this->fetch('login:register');
        }
    }

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


            $userId = Session::get('user_id');
            $user = $this->getModel()->where('id', $userId)->find();
            if (empty($user)) {
                return redirect('login');
            }

            if ($this->encrypt($user['username'], $opassword) !== $user['password']) {
                return $this->error('旧密码输入错误');
            }

            $newPwd = $this->encrypt($user['username'], $password);
            if ($this->encrypt($user['username'], $opassword) === $newPwd) {
                return $this->error('新密码不能和旧密码相同');
            }


            $this->getModel()->where('id', $userId)->update([
                'password' => $newPwd
            ]);


            return $this->success(['url' => (string)url($this->homeUrl)]);
        } else {
            return $this->fetch('login:change_pwd');
        }
    }

    //退出登录
    public function logout()
    {
        Session::delete('user');
        Session::delete('user_id');
        return redirect('login');
    }

    //找回密码
    public function find_pwd()
    {
        return '找回密码需要根据自己业务实现';
    }

    // 显示验证码
    public function verify()
    {
        return Captcha::create();
    }


}
