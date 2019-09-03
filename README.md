<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Advanced Project Template</h1>
    <br>
</p>

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template includes three tiers: front end, back end, and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Build Status](https://travis-ci.org/yiisoft/yii2-app-advanced.svg?branch=master)](https://travis-ci.org/yiisoft/yii2-app-advanced)

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
