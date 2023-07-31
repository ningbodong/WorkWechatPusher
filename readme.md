### 主要用途

- 年纪大了健忘需要提醒？
- 工作太多事情需要提醒？
- 薅了太多羊毛，服务到期需要提醒？
- 定时追女友舔狗式发微信？
- 合同太多需要提前提醒？


# WorkWechatPusher



### 前言
本程序需要用到企业微信，借助于企业微信的API（又薅了一次企业微信的羊毛）以及cron计划任务。

本来想到网上找一个直接用的，找了几天没找到一个合适的，所以就借助先辈们的基础加上AI的技术，粗糙的写了一个。
因为写代码并非本人特长，所以写的比较简单和粗糙，本程序也可能存在漏洞。随缘更新。第一次上传至Github，本着分享的精神，任何人可无偿使用，请勿对源码进行二次售卖。

### 主要功能
定时推送，可提前N天（N天内每次计划任务促发均会推送）。
批量导入导出推送任务，有前台登入，后台管理，用户可单独自定义配置企业微信参数。

### 使用方法
1.用电脑打开企业微信官网，注册一个企业微信(免费)。

2.注册成功后，点「管理企业」进入管理界面，选择「应用管理」 → 「自建」 → 「创建应用」。

3.应用名称填入「WorkWechatPusher」或者你想要的名称都可以。

4.完成创建企业微信APP后，可以得到应用ID( agentid )，应用Secret( secret )。进入「我的企业」页面，拉到最下边，可以看到企业ID，这个参数后面要用到。

5.搭建系统，安装LNMP，最简单的pyp+mysql，测试是在7.3的php，其他应该也可以，自测，LNMP安装方法略。

6.修改db.php为自己的数据库账号密码。导入send.sql数据库，默认账号密码admin，密码：admin123456

7.创建完成后登入，id为1的是管理员，后面创建的是用户，可以自建用户。

8.进入系统后，配置参数，点击「配置企业微信参数」，就是你刚才第4步创建的应用ID( agentid )，应用Secret( secret )，企业ID。用户ID填 @all ，推送给全员，填写某个人则推送给某个人。

9.安装cron添加计划任务，定时执行目录下的php cron.php即可。设置几点执行就是几点推送。


### 演示
演示就略了，这个很简单，搞个服务器演示怕被DDCC。

### 打赏

> 如果您觉得对您有帮助，欢迎给我打赏。

<img src="wxpay.jpg" width="400" />
