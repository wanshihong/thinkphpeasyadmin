<?php


namespace easyadmin\app\libs;


use think\facade\Db;

class Install
{

    private $files = [];

    public function __construct()
    {
        $this->install();
        $this->installStatic();
    }

    public function install(): bool
    {
        //应用根目录  project/app/admin/
        $app_path = app_path();

        $lockPath = $app_path . 'is_install.lock';
        if (is_file($lockPath)) {
            return true;
        }

        if (!is_writable($app_path)) {
            exit("请给与 {$app_path} 目录可写权限");
        }


        //源文件
        $sourceDir = dirname(dirname(dirname(__FILE__))) . '/install/admin/';

        //复制目录到 web 根目录
        $this->files = [];
        $this->copyDir($sourceDir, $app_path);



        //创建管理员表
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `manage` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(55) NOT NULL COMMENT '账号',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `reg_time` int(10) UNSIGNED DEFAULT NULL COMMENT '注册时间',
  `last_login_time` int(10) UNSIGNED DEFAULT NULL COMMENT '最后登录时间',
  `user_role` text COMMENT '用户角色 数组序列化',
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
SQL;
        Db::query($sql);


        $handle = fopen($lockPath, "w");
        fwrite($handle, '应用安装锁文件,安装过之后不在安装;' . PHP_EOL . '请不要删除,否则会覆盖下列相关的文件' . PHP_EOL);
        foreach ($this->files as $file) {
            fwrite($handle, $file . PHP_EOL);
        }

        fclose($handle);

        exit('<h1>安装成功,请刷新页面</h1>');
    }


    /**
     * 安装资源文件
     */
    public function installStatic(): bool
    {

        //web根目录
        $public_path = public_path();

        //目标文件
        $toDir = $public_path . Resource::getInstance()->getRoot();


        //安装过了 不再安装
        $lockPath = $toDir . 'is_install.lock';
        if (is_file($lockPath)) {
            return true;
        }

        if (!is_writable($public_path)) {
            exit("请给与 {$public_path} 目录可写权限");
        }

        //源文件
        $sourceDir = dirname(dirname(dirname(__FILE__))) . '/install/public/';

        //复制目录到 web 根目录
        $this->files = [];
        $this->copyDir($sourceDir, $toDir);

        $handle = fopen($lockPath, "w");
        fwrite($handle, '资源安装锁文件,安装过之后不在安装;' . PHP_EOL . '更改资源文件后,删除本文件后从新运行即可从新安装' . PHP_EOL . '请不要删除,否则会覆盖上列相关的文件' . PHP_EOL);

        foreach ($this->files as $file) {
            fwrite($handle, $file . PHP_EOL);
        }
        fclose($handle);
        return true;
    }

    /**
     * @param $formDir
     * @param $toDir
     */
    public function copyDir($formDir, $toDir)
    {
        if (!file_exists($toDir)) {
            mkdir($toDir);
        }
        $handle = opendir($formDir);
        while (($file = readdir($handle)) !== false) {
            if ($file == '.' || $file == '..') continue;
            $_source = $formDir . '/' . $file;
            $_dest = $toDir . '/' . $file;
            if (is_file($_source)) {
                copy($_source, $_dest);
                array_push($this->files, $_dest);
            }
            if (is_dir($_source)) $this->copyDir($_source, $_dest);
        }
        closedir($handle);
    }


}
