<div align="center">
    <h1>shopFX</h1>
    <P>一个简单又完整的商城</P>
    <br>
</div>

## 项目介绍 ##
    这个一个用yii2 + vue 做的一个前后端分离的商城，包含了商城的完整的功能。权限管理、商品的增删改查、商品的规格管理、
    订单管理、简单的物流、会员管理、文章管理、轮播图、商城积分管理。。。
    还有就是后端的vue路由不需要在前端的router中添加，只需要在菜单操作处，将路由信息添加到数据库即可。登录之后会进行路由的动态挂载。每次添加新菜单或是路由，需要重新登录才会生效。

## 项目配置 ##
    一、前期准备
        1、下载项目
            git clone https://github.com/FX336494/shopFX.git      // 把模板下载到本地
        1、进入到项目 目录 shopFX下 
            cd shopFX
        2、安装依赖、推荐使用composer安装依赖 (没有安装composer的自行安装)
            composer install

    二、后台接口配置
        1、将设置好的域名(admin.shopfx.com)指向 /shopFX/apiadmin/web/   如："H:/wamp/www/shopFx/apiadmin/web"
        2、在/shopFX/common/config/ 下配置数据库
        3、将 /shopFX/shopfx_db.sql 导入数据库
        4、接口的访问 如 http://admin.shopfx.com/v1/member/test
    三、后台管理前端配置
        1、cd vueadmin
        2、安装依赖
            npm install
        3、在 ./src/components/js/request.js里配置接口域名
        4、本地运行
            npm run dev
        5、执行构建命令，生成的dist文件夹放在服务器下即可访问
            npm run build
    四、商城接口配置
        1、将设置好的域名(api.shopfx.com)指向 /shopFX/api/web/   如："H:/wamp/www/shopFx/api/web"
        2、接口的访问 如 http://api.shopfx.com/v1/connect/test  
    五、商城前端配置
        1、cd vueshop
        2、安装依赖
            npm install
        3、在 ./src/components/js/common.js里配置接口域名
        4、本地运行
            npm run dev
        5、执行构建命令，生成的dist文件夹放在服务器下即可访问
            npm run build    

## 效果图 后台管理 ##
![Image text](https://raw.githubusercontent.com/FX336494/shopFX/master/api/web/assets/1.png)
![Image text](https://raw.githubusercontent.com/FX336494/shopFX/master/api/web/assets/2.png)
![Image text](https://raw.githubusercontent.com/FX336494/shopFX/master/api/web/assets/3.png)
![Image text](https://raw.githubusercontent.com/FX336494/shopFX/master/api/web/assets/4.png)
![Image text](https://raw.githubusercontent.com/FX336494/shopFX/master/api/web/assets/5.png)
![Image text](https://raw.githubusercontent.com/FX336494/shopFX/master/api/web/assets/6.png)
![Image text](https://raw.githubusercontent.com/FX336494/shopFX/master/api/web/assets/7.png)

目录结构
-------------------

```
api                     前端商城的api接口
    config/             接口的配置文件
    modules/
        models/         api数据模型
        v1/             api控制器
    runtime/            
    web/                api入口
apiadmin                后台管理的api接口
    config/             接口的配置文件
    modules/
        models/         后台api数据模型
        v1/             后台api控制器
    runtime/            
    web/                api入口
vueadmin                后台管理vue项目代码
vueshop                 前端vue项目代码
common
    config/             包含共享配置
    models/             共用的数据模型
    utils               包含一些共用的类
console                 用于执行角本的，一些定时处理任务
vendor/                 包含一些第三方的依赖包

```

## 一些说明
    1、商品规格
        这个商城虽然简单，但是对规格这块做的还是比较完善的，对规格也是做了库存的控制。这里主要是想说一下规格的
        设置，这里是将不同的规格做了一个分组的处理，以适应不同的分类。比如，设置了颜色、尺寸、内存等等规格，
        那么我们可以将这三个规格，分为一个组 叫电子产品。当我们添加手机这个分类的时候，就可以将他的规格组选为
        电子产品，当我们在手机分类下，添加产品的时候 ，这三个规格就会显示出来。
    2、图片上传
        图片上传有两种方式，一种是本地上传，一种是七牛上传（七牛是需要申请的）
    3、模型的设计
        我把大部分的共用模型都放在了common模块中，api、和 apiadmin 里对应的模型去继承common的模型，
        这样写虽然有点麻烦，但扩展性会要好点。 
    4、最后两句
        我觉得用一个项目做二开的时候，一定要理清项目的思路，这样写的时候就不会太乱。当别人接手你的项目的
        时候，也不会觉得杂乱。我现在就是这样，接手了一个三、四开的项目，里面五花八门，你想整理难度太大，所以只能按自己的思路写，这样就更乱，然后某天当你不得去看以前别人写的东西的时候 ，你会发现，这个功能其实已经写过类似的了，你只要稍加修改就已经可以用了。
    5、其它有想到的再说吧


## 赞赏
如果你觉得帮助到了你，可以请作者喝杯咖啡

![微信扫一扫](https://raw.githubusercontent.com/FX336494/admin_v1/master/apiadmin/web/data/6.png)
=======
