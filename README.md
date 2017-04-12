## 整合UCenter与ThinkPhp通信
本次接口采用UCenter1.6.0和Thinkphp3.2.3整合双向通信

初发点就是项目中考虑到站群式会员共用体系，为了使用一套会员体系，所以学习了一下UCenter运行原理，并进行简单的封装，简化代码逻辑，配置简单，双向通信。

####感谢提供建议/意见的小伙伴们(排名不分先后):
<pre>
小9
</pre>

首先，参考文档地址如下:

社区动力DZ资料库地址 http://faq.comsenz.com/library/index.htm

UCenter相关地址 http://faq.comsenz.com/library/UCenter/introduction/introduction_brief.htm

Thinkphp3.2.3相关手册地址 http://document.thinkphp.cn/manual_3_2.html

其次，参照了一些网上的整合方法并改进了一些BUG并完善而来，大家可以自行查找

最后，采用模块方式整合，不影响其他模块，而且可以在其他模块中调用，统一整合方式，便于PC端和手机端同步会员账户通信体系打通。

## 结构目录如下

<pre>
Application
├─Common                                //Common 模块
│  ├─Common                             //Common 公用目录
│  └─Conf                               //Common 配置目录
│     └─config.php                      //Common 配置文件(这里可以配置cookie前缀,每个站点一个,防止冲突)
├─Home                                  //Home 模块 (这里只做测试用,便于你自己理解)
│  ├─Common                             //Home 公用目录
│  ├─Conf                               //Home 配置目录
│  ├─Cotroller                          //Home 控制器目录
│  │  ├─BaseController.class.php        //基类控制器
│  │  ├─EmptyController.class.php       //空控制器
│  │  ├─IndexController.class.php       //首页类控制器
│  │  ├─LoginController.class.php       //登录类控制器(登录逻辑在这个里面处理)
│  │  └─UcController.class.php          //UC类控制器(这里只写一些基础示例,个人可以自行完善)
│  ├─Model                              //Home 模型目录
│  └─View                               //Home 视图目录
│     ├─Index                           //Index 控制器 视图目录
│     ├─Login                           //Login 控制器 视图目录
│     │  ├─index.html                   //登录首页获取cookie测试
│     │  └─sign_on.html                 //登录表单页(只是测试,可自己完善,如果注册需自己完善,可以借鉴这个)
│     └─Public                          //公用视图目录
└─UCenter                               //UCenter 模块
   ├─Client                             //UC 客户端目录
   │  ├─uc_client                       //UC 客户端文件
   │  └─UcApi.class.php                 //UC 接口处理类
   ├─Common                             //UCenter 公用目录
   │  └─function.php                    //公用函数
   ├─Conf                               //UCenter 配置目录
   │  └─config.php                      //UC 配置信息文件
   └─Controller                         //UCenter 控制器目录
      └─ApiController.class.php         //UC 通信处理类,这个是核心类,在UCenter后台配置(可以自行写你的逻辑)
</pre>

## 使用方法如下

1.UCenter安装与使用
<pre>
UCenter下载安装与使用，这里不讲了，自行查阅官方文档，上面已经给出地址。
</pre>
2.UCenter后台配置
<pre>
1.进入UCenter用户中心后台
2.找到左侧菜单[应用管理]
3.点击[添加新应用]按钮
4.进入新应用界面[应用类型]选择[其他]
5.[应用名称]这里可以取个好记的名字
6.[应用的主URL]这里填写[http://域名|IP:PORT/index.php/UCenter/Api]
7.[通信密钥]可以填写自己的密钥，不填写保存后会自动生成
8.[是否开启同步登录/是否接受通知]选择[是]单选按钮
9.保存成功后，返回[应用列表]后[通信情况]是红色的[通信失败]
10.进入编辑，把UCenter后台生成的配置文件拷贝到UCenter模块的Conf目录中的config.php文件中保存即可(可参考我写的文件)
11.再次保存后，返回应用列表会变成绿色的[通信成功]字样
</pre>
3.Common模块配置cookie前缀
<pre>
'AuthPre'=>'ucenter_' //UC登录cookie前缀，一个站点一个前缀名不要重复
</pre>

## 开源协议

可以在任何项目项目(个人或商业)中使用，如有不清楚的地方可以联系我。

欢迎提建议或给意见 QQ:17624522/Email:d8q8#163.com(#替换成@即可)。