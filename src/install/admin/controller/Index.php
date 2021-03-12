<?php

namespace app\admin\controller;


use think\db\exception\PDOException;
use think\facade\Db;

class Index extends Admin
{
    public function index()
    {
        $this->assign('name',request()->get('name','ZhangSan'));
        return $this->fetch('index:index',[
            'time'=>date('Y-m-d H:i:s')
        ]);
    }


}
