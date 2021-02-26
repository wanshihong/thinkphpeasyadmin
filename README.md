# thinkeasyadmin
>基于thinkphp的后台工具包

本项目是一个快速开发后台的工具库,完全基于面向对象思想; 

一切都是可以通过重写继承达到自定义的;

本项目无任何业务功能上的封装,业务上的一切都需要您自己开发,只是会让您开发得更流畅;

###安装

`composer require wansh/thinkeasyadmin`

### 使用
只需要继承`\easyadmin\controller\Admin`类即可
```
#project\app\admin\controller\Category.php

namespace app\admin\controller;


class Category extends \easyadmin\controller\Admin
{

}
```

打开浏览器 访问 [http://localhost/admin/Category/lists](http://localhost/admin/Category/lists)
如果您的数据库配置正确,并且存在数据表`category`,您将会看到如下界面
![首次安装预览图片](https://thinkeasyadmin.wansh.cc/doc_img/use_category.png)     

### 其他配置
#### 基础配置 - 表名称 - 主键 等

默认会取`当前控制器`的名称做为数据库的表名,`id`做为主键去查询数据;

如果需要自定义配置数据库表名和主键,可使用如下配置:

```
#project\app\admin\controller\Category.php

namespace app\admin\controller;

class Index extends \easyadmin\controller\Admin
{
    protected $pageName = '任务'; //页面显示名称
    protected $tableName = 'category';//数据库表名 默认取当前控制器名称
    protected $pk = 'id';//主键, 默认 id

}
```



更多内容,[查看详细文档](https://thinkeasyadmin.wansh.cc/)
