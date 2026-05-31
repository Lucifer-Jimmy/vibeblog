# LEARNING.md · Laravel 项目学习指南

> 本文档以 myblog 项目为例，帮助你理解 Laravel 13（架构风格与 Laravel 11+ 一致）的目录结构和请求处理流程。

---

## 目录

1. [目录结构总览](#1-目录结构总览)
2. [各目录详细说明](#2-各目录详细说明)
3. [请求生命周期（从浏览器到响应）](#3-请求生命周期从浏览器到响应)
4. [框架启动加载流程](#4-框架启动加载流程)
5. [MVC 架构在本项目中的体现](#5-mvc-架构在本项目中的体现)
6. [数据库初始化机制](#6-数据库初始化机制)
7. [关键概念速查](#7-关键概念速查)

---

## 1. 目录结构总览

```
myblog/
├── app/                    ← 应用核心代码（你写的业务逻辑都在这里）
├── bootstrap/              ← 框架启动引导文件
├── config/                 ← 所有配置文件
├── database/               ← 数据库相关（迁移、填充、工厂）
├── public/                 ← Web 入口 + 静态资源（唯一对外暴露的目录）
├── resources/              ← 前端资源（视图模板、CSS、JS 源码）
├── routes/                 ← 路由定义（URL → Controller 的映射）
├── storage/                ← 框架生成的文件（日志、缓存、上传文件）
├── tests/                  ← 自动化测试
├── vendor/                 ← Composer 依赖（不要手动修改）
├── node_modules/           ← NPM 依赖（不要手动修改）
├── .env                    ← 环境变量（数据库密码等敏感信息）
├── composer.json           ← PHP 依赖声明
├── package.json            ← 前端依赖声明
├── vite.config.js          ← Vite 前端构建配置
└── artisan                 ← Laravel CLI 入口（php artisan xxx）
```

---

## 2. 各目录详细说明

### 2.1 `app/` — 应用核心代码

这是你日常开发中最常修改的目录。

```
app/
├── Http/
│   ├── Controllers/        ← 控制器：处理请求、调用模型、返回视图
│   │   ├── Admin/          ← 后台管理控制器
│   │   ├── Auth/           ← 认证相关控制器（登录、注册等，Breeze 生成）
│   │   ├── PostController.php      ← 前台文章展示
│   │   ├── CategoryController.php  ← 前台分类展示
│   │   ├── SearchController.php    ← 搜索功能
│   │   └── ...
│   ├── Middleware/          ← 中间件：请求的"过滤器"
│   │   └── AdminMiddleware.php     ← 检查用户是否是管理员
│   └── Requests/            ← 表单请求验证类
│       ├── StorePostRequest.php    ← 创建文章时的验证规则
│       └── UpdatePostRequest.php   ← 更新文章时的验证规则
├── Models/                  ← Eloquent 模型：对应数据库表
│   ├── User.php             ← 用户模型
│   ├── Post.php             ← 文章模型
│   ├── Category.php         ← 分类模型
│   ├── Tag.php              ← 标签模型
│   └── Comment.php          ← 评论模型
├── Providers/               ← 服务提供者：框架启动时注册服务
│   ├── AppServiceProvider.php      ← 应用级服务注册（目前为空）
│   └── ViewServiceProvider.php     ← 向视图注入全局数据（导航栏分类）
└── View/
    └── Components/          ← 视图组件类（对应 Blade 布局组件）
        ├── AppLayout.php    ← 前台布局
        ├── AdminLayout.php  ← 后台布局
        └── GuestLayout.php  ← 游客布局（登录/注册页）
```

**核心概念：**
- **Controller** = 接收请求 → 处理逻辑 → 返回响应
- **Model** = 一张数据库表的 PHP 对象表示，负责数据的增删改查
- **Middleware** = 请求到达 Controller 之前/之后的拦截器
- **Provider** = 框架启动时执行的初始化代码

