### 简介
基于thinkphp6.0 开发的后台快速开发包

本项目是一个快速开发后台的工具库,完全基于面向对象思想; 一切都是可以通过重写继承达到自定义的;

本项目无任何业务功能上的封装,业务上的一切都需要您自己开发,只是会让您开发得更流畅;

gitee地址:  https://gitee.com/wsh255/thinkeasyadmin

### 添加列表

```php
#project\app\admin\controller\Category.php
 
namespace app\admin\controller;
 
use easyadmin\app\columns\lists\ListDateTime;
use easyadmin\app\columns\lists\ListImage;
use easyadmin\app\columns\lists\ListText;
use easyadmin\app\libs\ListField;
 
class Category extends \easyadmin\controller\Admin
{
    protected $pageName = '分类';
 
    protected function configListField(ListField $list)
    {
        $list
            ->addField('id', 'ID', ListText::class)
            ->addField('name', '分类名称', ListText::class)
            ->addField('icon', '图标', ListImage::class)
            ->addField('time', '创建时间', ListDateTime::class);
    }
 
}
```
### 添加表单

```php
#project\app\admin\controller\Category.php
 
namespace app\admin\controller;
 
use easyadmin\app\libs\PageForm;
use easyadmin\app\columns\form\FormSelect;
use easyadmin\app\columns\form\FormSwitch;
use easyadmin\app\columns\form\FormText;
use easyadmin\app\columns\form\FormTextarea;
use easyadmin\app\columns\form\FormUpload;
 
class Category extends \easyadmin\controller\Admin
{
 
    /**
     * 配置表单
     * @param PageForm $page
     */
    protected function configFormField(PageForm $page)
    {
        $page
            ->addField('parent_id', '上级分类', FormSelect::class, [
                'table' => 'category',
                'pk' => 'id',//使用查询,的主键
                'property' => 'name',//查询显示字段
            ])
            ->addField('name', '分类名称', FormText::class)
            ->addField('icon', '分类图标', FormUpload::class)
            ->addField('intro', '分类简介', FormTextarea::class)
            ->addField('is_del', '是否删除', FormSwitch::class);
 
        if ($this->formType == 'edit') {
            //编辑页面特有的字段
            $page->addField('edit_field', '名称', FormText::class);
        }else{
            //添加页面特有的字段
            $page->addField('add_field', '名称', FormText::class);
        }
 
    }
}
```
### 表单验证

```php
#project\app\admin\controller\Category.php
 
namespace app\admin\controller;
 
use easyadmin\app\libs\PageForm;
use easyadmin\app\columns\form\FormText;
 
 
class Category extends \easyadmin\controller\Admin
{
 
    /**
     * 配置表单
     * @param PageForm $page
     */
    protected function configFormField(PageForm $page)
    {
        $page
            ->addField('name', '分类名称', FormText::class,[
 
                'required' => true,  //是否必填
 
 
                //定义验证规则, 用户输入的值长度大于0 个字符才会进入验证
                // 1. 使用 Verify 类,然后配置规则
                'verify' => (new Verify())
                    ->addRule('chinese', '名称请输入中文')
                    ->addRule('maxlength', '名称不能超过5个字符', 5)
                    ->addRule('minlength', '名称不能少于过2个字符', 2)
                    ->addRule('reg', '请输入 重庆', '/^重庆$/') // 使用正则自定义验证规则
 
                // 2. 声明匿名函数验证
                // 验证正确 返回 true  错误返回错误提示  注: 正必须是 返回 true
                'verify' => function ($val) {
                    if ($val != '583161908') {
                        return 'QQ 群是 583161908, 请输入: 583161908';
                    }
                    return true;
                }
            ]);
 
    }
}
```
### 路由权限控制配置

```php
#project\app\admin\config\login.php
 
 
 
// +----------------------------------------------------------------------
// | 登录注册相关配置
// +----------------------------------------------------------------------
 
return [
 
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
 
        // ... 其他路由规则,权限配置,在这里增加就好了
    ]
 
];
```
### 业务中权限控制

```php
if( User::getInstance()->hasRole('role_super') ){
    // 超级管理员才显示菜单
    // 超级管理员才显示字段
    // 超级管理员才执行某段逻辑
    // ...
    // ...
    // 根据自己逻辑干就完了
}
```

更多内容:  https://thinkeasyadmin.wansh.cc/
