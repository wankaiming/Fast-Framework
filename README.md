# Fast-Framework

1>整个项目采用单一入口。

2>模板引擎采用smarty。

3>数据库类型是mysql。

4>url模式是: 域名/模块名/方法名/参数名/参数值，例如: http://demo.com/news/show/id/2。

5>为了支持上面的url模式，nginx需要做pathinfo模式和隐藏index.php的配置，配置示例文件在nginx配置.txt。如果是使用apache也需要做类似的配置。

6>网站配置文件为config.php，里面可以配置db，smarty等。

7>网站示例sql文件为demo.sql。

8>后台和前台都做了两个示例模块。后台登录地址为: http://demo.com/admin/login 示例的账号为: admin/admin。

9>不同的业务模块，分别在ctrl/model/templates下面建立它们的文件夹。

10>控制文件的命名规则 xxxCtrl.class.php，模型文件的命名规则 xxxModel.class.php。


