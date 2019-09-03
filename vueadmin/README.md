## 后台管理配置 ##

    一、后台接口配置
        1、将设置好的域名(admin.shopfx.com)指向 /shopFX/apiadmin/web/   如："H:/wamp/www/shopFx/apiadmin/web"
        2、在/shopFX/common/config/ 下配置数据库
        3、将 /shopFX/shopfx_db.sql 导入数据库
        4、接口的访问 如 http://admin.shopfx.com/v1/member/test
    二、后台管理前端配置
        1、cd vueadmin
        2、安装依赖
            npm install
        3、在 ./src/components/js/request.js里配置接口域名
        4、本地运行
            npm run dev
        5、执行构建命令，生成的dist文件夹放在服务器下即可访问
            npm run build