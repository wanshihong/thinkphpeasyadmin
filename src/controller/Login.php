<?php


namespace easyadmin\controller;


use think\captcha\facade\Captcha;


class Login extends Admin
{



    public function login()
    {
        return $this->fetch('login:login');
    }


    public function register()
    {
        return 'register';
    }

    public function logout()
    {
        return 'logout';
    }


    public function verify()
    {
        return Captcha::create();
    }


}
