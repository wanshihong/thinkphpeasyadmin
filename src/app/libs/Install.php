<?php


namespace easyadmin\app\libs;


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

        //源文件
        $sourceDir = dirname(dirname(dirname(__FILE__))) . '/install/admin/';

        //复制目录到 web 根目录
        $this->files = [];
        $this->copyDir($sourceDir, $app_path);

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
