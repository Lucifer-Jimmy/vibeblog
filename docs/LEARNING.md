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
8. [前端实现详解：为什么 Laravel 项目需要 NPM](#8-前端实现详解为什么-laravel-项目需要-npm)
9. [单元测试与功能测试详解](#9-单元测试与功能测试详解)

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

### 2.2 `bootstrap/` — 框架引导

```
bootstrap/
├── app.php                 ← 应用配置入口（路由、中间件、异常处理）
├── providers.php           ← 注册服务提供者列表
└── cache/                  ← 框架生成的缓存文件（路由缓存、配置缓存等）
```

`bootstrap/app.php` 是 Laravel 11+ 的核心配置文件，取代了旧版的 `app/Http/Kernel.php`。本项目中它做了三件事：
1. 注册路由文件（`routes/web.php`、`routes/console.php`）
2. 注册中间件别名（`admin` → `AdminMiddleware`）
3. 配置异常处理（API 路由返回 JSON 错误）

### 2.3 `config/` — 配置文件

```
config/
├── app.php                 ← 应用基础配置（名称、时区、语言）
├── auth.php                ← 认证配置（守卫、用户提供者）
├── cache.php               ← 缓存驱动配置
├── database.php            ← 数据库连接配置
├── filesystems.php         ← 文件存储配置（本地、S3 等）
├── logging.php             ← 日志配置
├── mail.php                ← 邮件配置
├── permission.php          ← spatie/permission 角色权限配置
├── queue.php               ← 队列配置
├── services.php            ← 第三方服务配置
└── session.php             ← Session 配置
```

**重要：** 配置文件中的值大多通过 `env('KEY', 'default')` 从 `.env` 文件读取。修改配置时，优先改 `.env` 而不是直接改 `config/` 文件。

### 2.4 `database/` — 数据库相关

```
database/
├── migrations/             ← 迁移文件：定义表结构（版本化的 DDL）
├── seeders/                ← 填充文件：插入初始/测试数据
└── factories/              ← 工厂文件：定义假数据生成规则
```

详见 [第 6 节：数据库初始化机制](#6-数据库初始化机制)。

### 2.5 `public/` — Web 服务器根目录

```
public/
├── index.php               ← 所有请求的入口（唯一的 PHP 入口文件）
├── build/                  ← Vite 构建产物（CSS、JS）
├── favicon.ico             ← 网站图标
└── robots.txt              ← 搜索引擎爬虫规则
```

**关键点：** Web 服务器（Nginx/Apache）的 document root 指向 `public/`，这样 `app/`、`config/` 等目录不会被直接访问，保证安全。

### 2.6 `resources/` — 前端资源

```
resources/
├── css/
│   └── app.css             ← 全局样式（引入 Tailwind CSS）
├── js/
│   └── app.js              ← 前端 JS 入口
└── views/                  ← Blade 模板文件
    ├── layouts/            ← 页面布局骨架
    │   ├── app.blade.php           ← 前台主布局
    │   ├── guest.blade.php         ← 游客布局
    │   └── navigation.blade.php    ← 导航栏组件
    ├── components/         ← 可复用的 UI 组件
    │   ├── post-card.blade.php     ← 文章卡片
    │   ├── dropdown.blade.php      ← 下拉菜单
    │   └── ...
    ├── posts/              ← 文章相关页面
    │   ├── index.blade.php         ← 文章列表（首页）
    │   └── show.blade.php          ← 文章详情
    ├── categories/         ← 分类页面
    ├── admin/              ← 后台管理页面
    ├── auth/               ← 认证页面（登录、注册等）
    ├── errors/             ← 错误页面（403、404、500）
    └── ...
```

**Blade 模板引擎：** Laravel 的模板语法，用 `{{ $variable }}` 输出变量，`@if`/`@foreach` 做逻辑控制，`@extends`/`@section` 实现布局继承。

### 2.7 `routes/` — 路由定义

```
routes/
├── web.php                 ← Web 路由（浏览器访问的页面）
├── auth.php                ← 认证路由（登录、注册、密码重置）
└── console.php             ← Artisan 命令路由
```

路由文件定义了 **URL → Controller 方法** 的映射关系。本项目的路由分三组：

| 路由组 | 中间件 | 示例 URL | 说明 |
|--------|--------|----------|------|
| 公开路由 | 无 | `/`、`/posts/{slug}`、`/categories` | 任何人都能访问 |
| 登录用户 | `auth` | `/my/posts`、`/comments` | 需要登录 |
| 管理员 | `auth` + `admin` | `/admin/posts`、`/admin/categories` | 需要管理员权限 |

### 2.8 `storage/` — 运行时生成的文件

```
storage/
├── app/
│   └── public/             ← 用户上传的文件（通过软链接对外暴露）
├── framework/
│   ├── cache/              ← 文件缓存
│   ├── sessions/           ← Session 文件
│   └── views/              ← 编译后的 Blade 模板
├── logs/
│   └── laravel.log         ← 应用日志
└── debugbar/               ← Debugbar 调试数据
```

### 2.9 `tests/` — 自动化测试

```
tests/
├── Feature/                ← 功能测试（模拟 HTTP 请求，测试完整流程）
└── Unit/                   ← 单元测试（测试单个类/方法）
```

---

## 3. 请求生命周期（从浏览器到响应）

当用户在浏览器输入 `http://myblog.test/posts/hello-world` 时，发生了什么？

```
浏览器发起请求
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│ Web 服务器（Nginx/Apache）                                        │
│ 所有请求转发到 public/index.php                                    │
└─────────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│ public/index.php                                                 │
│ 1. 检查维护模式                                                    │
│ 2. 加载 Composer 自动加载器（vendor/autoload.php）                   │
│ 3. 创建应用实例（bootstrap/app.php）                                │
│ 4. 处理请求 → $app->handleRequest(Request::capture())             │
└─────────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│ 框架启动（详见第 4 节）                                             │
│ 加载配置 → 注册服务提供者 → 启动服务提供者                             │
└─────────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│ 路由匹配                                                          │
│ URL: /posts/hello-world                                          │
│ 匹配规则: Route::get('/posts/{post:slug}', [PostController, 'show'])│
└─────────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│ 中间件管道                                                        │
│ 全局中间件 → 路由组中间件 → 路由中间件                                │
│ （本路由无特殊中间件，走默认 web 中间件组）                             │
└─────────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│ Controller 处理                                                   │
│ PostController::show($post)                                      │
│ - $post 通过路由模型绑定自动从数据库查询（WHERE slug = 'hello-world'） │
│ - 加载关联数据（作者、分类、标签、评论）                                │
│ - 返回视图 return view('posts.show', compact('post'))              │
└─────────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│ 视图渲染                                                          │
│ Blade 引擎编译 resources/views/posts/show.blade.php                │
│ - 继承布局 layouts/app.blade.php                                   │
│ - 填充数据到 HTML                                                  │
│ - 输出最终 HTML                                                    │
└─────────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│ HTTP 响应返回浏览器                                                 │
│ Status: 200 OK                                                   │
│ Content-Type: text/html                                          │
└─────────────────────────────────────────────────────────────────┘
```

---

## 4. 框架启动加载流程

Laravel 的启动过程可以分为 5 个阶段：

### 阶段 1：入口（public/index.php）

```php
// 1. 维护模式检查
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;  // 显示维护页面并退出
}

// 2. 自动加载
require __DIR__.'/../vendor/autoload.php';  // Composer PSR-4 自动加载

// 3. 创建应用 + 处理请求
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Request::capture());
```

### 阶段 2：应用配置（bootstrap/app.php）

本项目的 `bootstrap/app.php`：

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',       // Web 路由文件
        commands: __DIR__.'/../routes/console.php', // Artisan 命令
        health: '/up',                            // 健康检查端点
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
```

这里做了三件事：
1. **路由注册** — 告诉框架去哪里找路由定义
2. **中间件配置** — 注册 `admin` 中间件别名
3. **异常处理** — API 路由返回 JSON 格式的错误

### 阶段 3：服务提供者注册（bootstrap/providers.php）

```php
return [
    App\Providers\AppServiceProvider::class,   // 应用服务（目前为空）
    App\Providers\ViewServiceProvider::class,  // 视图数据共享
];
```

每个 Provider 有两个生命周期方法：
- `register()` — 注册绑定到服务容器（此时其他服务可能还没准备好）
- `boot()` — 所有服务注册完毕后执行（可以安全使用其他服务）

本项目中 `ViewServiceProvider::boot()` 的作用：

```php
// 每次渲染 layouts.navigation 视图时，自动注入 $navCategories 变量
View::composer('layouts.navigation', function ($view) {
    $view->with('navCategories', Category::orderBy('name')->get());
});
```

### 阶段 4：中间件管道

请求通过一系列中间件，像洋葱一样层层包裹：

```
请求 → 全局中间件 → 路由中间件 → Controller → 路由中间件 → 全局中间件 → 响应
```

Laravel 默认的 `web` 中间件组包含：
- 加密 Cookie
- 启动 Session
- CSRF 令牌验证
- 共享认证用户到视图

本项目额外注册的中间件：
- `admin` — 检查 `$user->hasRole('admin')`，非管理员返回 403

### 阶段 5：路由解析 → Controller → 响应

1. **路由匹配**：根据 HTTP 方法 + URL 找到对应的 Controller 方法
2. **依赖注入**：Laravel 自动解析 Controller 方法的参数（模型绑定、Request 对象等）
3. **执行 Controller**：运行业务逻辑
4. **返回响应**：视图、JSON、重定向等

---

## 5. MVC 架构在本项目中的体现

```
┌─────────────────────────────────────────────────────────────┐
│                        用户请求                               │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│  Route（路由）                                                │
│  routes/web.php 定义 URL → Controller 的映射                  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│  Controller（控制器）                                         │
│  app/Http/Controllers/                                       │
│  接收请求 → 调用 Model → 传数据给 View                         │
└─────────────────────────────────────────────────────────────┘
              │                              │
              ▼                              ▼
┌──────────────────────────┐  ┌──────────────────────────────┐
│  Model（模型）            │  │  View（视图）                  │
│  app/Models/              │  │  resources/views/             │
│  与数据库交互              │  │  渲染 HTML 页面               │
│  定义关联关系              │  │  使用 Blade 模板语法           │
└──────────────────────────┘  └──────────────────────────────┘
```

### 实际例子：访问文章列表首页

**Route（`routes/web.php`）：**
```php
Route::get('/', [PostController::class, 'index'])->name('home');
```

**Controller（`app/Http/Controllers/PostController.php`）：**
```php
public function index()
{
    $posts = Post::where('status', 'published')
                 ->with(['author', 'category', 'tags'])
                 ->latest('published_at')
                 ->paginate(10);

    return view('posts.index', compact('posts'));
}
```

**Model（`app/Models/Post.php`）：**
```php
class Post extends Model
{
    // 定义关联：一篇文章属于一个作者
    public function author() { return $this->belongsTo(User::class, 'user_id'); }
    // 定义关联：一篇文章属于一个分类
    public function category() { return $this->belongsTo(Category::class); }
    // 定义关联：一篇文章有多个标签（多对多）
    public function tags() { return $this->belongsToMany(Tag::class); }
}
```

**View（`resources/views/posts/index.blade.php`）：**
```blade
@foreach ($posts as $post)
    <x-post-card :post="$post" />
@endforeach

{{ $posts->links() }}  {{-- 分页链接 --}}
```

---

## 6. 数据库初始化机制

### 首次部署需要执行的命令

```bash
# 第一步：创建数据库（MySQL 层面）
mysql -uroot -p -e "CREATE DATABASE myblog DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 第二步：运行迁移 + 填充（开发环境）
php artisan migrate --seed

# 或者分步执行：
php artisan migrate          # 只创建表结构
php artisan db:seed          # 只填充数据
```

> 生产环境只执行 `php artisan migrate --force`（不带 `--seed`），不需要假数据。

### 迁移文件（表结构定义）

位置：`database/migrations/`

| 文件 | 作用 |
|------|------|
| `0001_01_01_000000_create_users_table.php` | users、password_reset_tokens、sessions 表 |
| `0001_01_01_000001_create_cache_table.php` | cache、cache_locks 表 |
| `0001_01_01_000002_create_jobs_table.php` | jobs、job_batches、failed_jobs 表 |
| `2026_05_31_042701_create_categories_table.php` | categories 表 |
| `2026_05_31_042702_create_tags_table.php` | tags 表 |
| `2026_05_31_042703_create_posts_table.php` | posts 表 |
| `2026_05_31_042704_create_comments_table.php` | comments 表 |
| `2026_05_31_042705_create_post_tag_table.php` | post_tag 多对多中间表 |
| `2026_05_31_045529_add_fulltext_index_to_posts_table.php` | posts 表全文索引 |
| `2026_05_31_045900_create_permission_tables.php` | spatie/permission 角色权限表 |

迁移按文件名中的时间戳排序执行，保证外键依赖正确。

### 数据填充文件

位置：`database/seeders/`

| 文件 | 作用 |
|------|------|
| `DatabaseSeeder.php` | **入口文件**，创建角色（admin/author/user）、管理员账号、普通用户，然后调用 PostSeeder |
| `PostSeeder.php` | 创建 6 个分类、10 个标签、35 篇文章，并为已发布文章生成评论 |

### 工厂文件（假数据生成规则）

位置：`database/factories/`

| 文件 | 作用 |
|------|------|
| `UserFactory.php` | 定义用户假数据的生成规则 |
| `CategoryFactory.php` | 定义分类名称池（技术、生活、读书等） |
| `TagFactory.php` | 定义标签名称池（Laravel、PHP、Vue.js 等） |
| `PostFactory.php` | 定义文章假数据（标题、slug、Markdown 内容、状态等） |
| `CommentFactory.php` | 定义评论假数据 |

### 执行流程图

```
php artisan migrate --seed
│
├── 1. 读取 database/migrations/ 下所有迁移文件
│   ├── 按时间戳排序
│   ├── 逐个执行 up() 方法
│   └── 在 migrations 表中记录已执行的迁移
│
└── 2. 执行 database/seeders/DatabaseSeeder.php 的 run()
    ├── 创建 3 个角色 (admin, author, user)
    ├── 创建管理员 admin@myblog.test (密码: password)
    ├── 创建普通用户 user@myblog.test (密码: password)
    ├── 创建 3 个额外测试用户
    └── 调用 PostSeeder::run()
        ├── 用 CategoryFactory 创建 6 个分类
        ├── 用 TagFactory 创建 10 个标签
        ├── 用 PostFactory 创建 35 篇文章
        ├── 每篇文章随机关联 1~4 个标签
        └── 已发布文章随机生成 1~3 条评论
```

### 开发环境 vs 生产环境

| 场景 | 命令 | 说明 |
|------|------|------|
| 首次开发 | `php artisan migrate --seed` | 建表 + 填充假数据 |
| 开发中重置 | `php artisan migrate:fresh --seed` | 删除所有表重建 + 重新填充 |
| 生产首次部署 | `php artisan migrate --force` | 只建表，不填充假数据 |
| 生产后续更新 | `php artisan migrate --force` | 只执行新增的迁移 |

---

## 7. 关键概念速查

### 服务容器（Service Container）

Laravel 的核心是一个 IoC（控制反转）容器。你不需要手动 `new` 对象，框架会自动解析依赖：

```php
// 你不需要这样写：
$controller = new PostController(new PostRepository(new Database()));

// Laravel 自动解析依赖，你只需要在方法参数中声明类型：
public function show(Post $post)  // 框架自动注入 Post 模型实例
```

### Eloquent ORM

Laravel 的数据库抽象层，每个 Model 对应一张表：

```php
// 查询
$posts = Post::where('status', 'published')->get();

// 关联查询（避免 N+1 问题）
$posts = Post::with(['author', 'tags'])->paginate(10);

// 创建
Post::create(['title' => '...', 'content' => '...']);

// 更新
$post->update(['title' => '新标题']);

// 删除
$post->delete();
```

### Blade 模板引擎

```blade
{{-- 输出变量（自动转义 HTML） --}}
{{ $post->title }}

{{-- 原始 HTML 输出（危险，慎用） --}}
{!! $post->content !!}

{{-- 条件判断 --}}
@if ($post->isPublished())
    <span class="badge">已发布</span>
@endif

{{-- 循环 --}}
@foreach ($posts as $post)
    <li>{{ $post->title }}</li>
@endforeach

{{-- 布局继承 --}}
@extends('layouts.app')
@section('content')
    <h1>页面内容</h1>
@endsection

{{-- 组件 --}}
<x-post-card :post="$post" />
```

### Artisan 常用命令

```bash
# 开发服务器
php artisan serve                    # 启动开发服务器 http://localhost:8000

# 路由
php artisan route:list               # 查看所有注册的路由

# 数据库
php artisan migrate                  # 执行迁移
php artisan migrate:fresh --seed     # 重建数据库
php artisan make:migration create_xxx_table  # 创建迁移文件

# 代码生成
php artisan make:controller XxxController    # 创建控制器
php artisan make:model Xxx -mfc             # 创建模型 + 迁移 + 工厂 + 控制器

# 缓存
php artisan config:cache             # 缓存配置（生产环境用）
php artisan route:cache              # 缓存路由（生产环境用）
php artisan cache:clear              # 清除应用缓存
php artisan view:clear               # 清除编译的视图

# 调试
php artisan tinker                   # 交互式 REPL（可以直接执行 PHP/Eloquent）
```

### Migration 是幂等的

Laravel 在 `migrations` 表中记录已执行的迁移，不会重复执行。

### Seeder 不是幂等的

重复执行会插入重复数据，所以开发中用 `migrate:fresh --seed` 重置。

### Factory 是 Seeder 的工具

Factory 定义"怎么生成一条假数据"，Seeder 决定"生成多少条、怎么关联"。

---

## 8. 前端实现详解：为什么 Laravel 项目需要 NPM

### 8.1 先回答核心问题：为什么需要 NPM？

Laravel 是 PHP 后端框架，但现代 Web 开发中，前端也有自己的生态系统。NPM（Node Package Manager）是 JavaScript/CSS 的包管理器，类似于 PHP 的 Composer。

**本项目用 NPM 管理的东西：**

| 工具/库 | 作用 | 为什么不能直接写 |
|---------|------|-----------------|
| Tailwind CSS | 原子化 CSS 框架 | 需要编译：扫描 HTML 中用到的类名，只打包用到的样式 |
| Alpine.js | 轻量级 JS 框架 | 需要打包：模块化导入，压缩体积 |
| Vite | 构建工具 | 开发时提供热更新，生产时打包压缩 |
| PostCSS + Autoprefixer | CSS 后处理器 | 自动添加浏览器前缀（`-webkit-` 等） |

**简单来说：** 你写的是"源码"（`resources/css/app.css`、`resources/js/app.js`），浏览器不能直接运行它们。需要一个构建步骤把源码编译、打包、压缩成浏览器能理解的文件（`public/build/`）。

### 8.2 两套包管理器的分工

```
┌─────────────────────────────────────────────────────────────────┐
│  Composer（PHP 包管理器）                                         │
│  管理后端依赖：Laravel 框架、数据库 ORM、权限系统等                    │
│  配置文件：composer.json                                          │
│  安装目录：vendor/                                                │
│  命令：composer install                                           │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│  NPM（Node.js 包管理器）                                          │
│  管理前端依赖：CSS 框架、JS 库、构建工具等                            │
│  配置文件：package.json                                           │
│  安装目录：node_modules/                                          │
│  命令：npm install                                                │
└─────────────────────────────────────────────────────────────────┘
```

两者互不干扰，各管各的。

### 8.3 本项目的前端技术栈

```
┌─────────────────────────────────────────────────────────────────┐
│                    前端技术栈全景                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  构建工具：Vite 8 + laravel-vite-plugin                           │
│  ├── 开发模式：npm run dev（热更新，改代码浏览器自动刷新）              │
│  └── 生产构建：npm run build（压缩打包到 public/build/）             │
│                                                                  │
│  CSS 方案：Tailwind CSS v3                                        │
│  ├── 原子化类名：class="text-lg font-bold text-primary"            │
│  ├── 插件：@tailwindcss/forms（美化表单）                           │
│  ├── 插件：@tailwindcss/typography（美化文章排版）                   │
│  └── PostCSS + Autoprefixer（自动浏览器兼容）                       │
│                                                                  │
│  JS 方案：Alpine.js v3                                            │
│  ├── 轻量级响应式框架（类似 Vue，但更简单）                           │
│  ├── 直接在 HTML 属性中写逻辑：x-data、x-show、@click              │
│  └── 本项目用途：暗色模式切换、打字机动画、下拉菜单                     │
│                                                                  │
│  字体：Inter（通过 Bunny Fonts CDN 加载，无需 NPM）                 │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### 8.4 构建流程详解

#### 源码文件（你编辑的）

**`resources/css/app.css`** — CSS 入口：
```css
@tailwind base;          /* Tailwind 的基础样式重置 */
@tailwind components;    /* Tailwind 的组件类 */
@tailwind utilities;     /* Tailwind 的工具类（text-lg、flex 等） */

/* 自定义样式 */
@layer utilities {
    .animate-blink {
        animation: blink 1s step-end infinite;
    }
}
```

**`resources/js/app.js`** — JS 入口：
```javascript
import Alpine from 'alpinejs';    // 从 node_modules 导入 Alpine.js

window.Alpine = Alpine;

// 注册一个 Alpine 组件：打字机效果
Alpine.data('typewriter', () => ({
    text: '分享技术、记录生活、探索世界',
    display: '',
    charIndex: 0,
    start() { this.tick(); },
    tick() {
        if (this.charIndex < this.text.length) {
            this.display = this.text.substring(0, this.charIndex + 1);
            this.charIndex++;
            setTimeout(() => this.tick(), 80);
        }
    },
}));

Alpine.start();    // 启动 Alpine，扫描页面中的 x-data 属性
```

#### 构建产物（浏览器加载的）

```
public/build/
├── assets/
│   ├── app-AwZHghkb.css    ← 编译后的 CSS（Tailwind 只保留用到的类）
│   └── app-DjMPn38w.js     ← 打包后的 JS（Alpine + 你的代码，压缩混淆）
└── manifest.json            ← 文件名映射（告诉 Laravel 加载哪个哈希文件）
```

文件名中的哈希值（`AwZHghkb`）是内容哈希——文件内容变了，哈希就变，浏览器就会重新下载。这解决了缓存问题。

#### 构建过程可视化

```
npm run build
    │
    ├── Vite 读取 vite.config.js 配置
    │
    ├── 处理 CSS 入口 (resources/css/app.css)
    │   ├── PostCSS 处理 @tailwind 指令
    │   ├── Tailwind 扫描所有 .blade.php 文件
    │   ├── 只生成页面中实际用到的 CSS 类
    │   ├── Autoprefixer 添加浏览器前缀
    │   └── 压缩 → public/build/assets/app-[hash].css
    │
    ├── 处理 JS 入口 (resources/js/app.js)
    │   ├── 解析 import 语句
    │   ├── 从 node_modules/ 中打包 Alpine.js 源码
    │   ├── 合并你的代码 + 依赖库代码
    │   ├── Tree-shaking（删除未使用的代码）
    │   └── 压缩混淆 → public/build/assets/app-[hash].js
    │
    └── 生成 manifest.json（源文件 → 产物的映射表）
```

### 8.5 Laravel 如何加载前端资源

在 Blade 模板中，用 `@vite` 指令引入前端资源：

```html
<!-- resources/views/layouts/app.blade.php -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

这个指令会根据环境自动生成不同的 HTML：

**开发环境**（`npm run dev` 运行中）：
```html
<!-- 直接连接 Vite 开发服务器，支持热更新 -->
<script type="module" src="http://localhost:5173/@vite/client"></script>
<link rel="stylesheet" href="http://localhost:5173/resources/css/app.css">
<script type="module" src="http://localhost:5173/resources/js/app.js"></script>
```

**生产环境**（已执行 `npm run build`）：
```html
<!-- 加载构建产物，带内容哈希用于缓存 -->
<link rel="stylesheet" href="/build/assets/app-AwZHghkb.css">
<script type="module" src="/build/assets/app-DjMPn38w.js"></script>
```

### 8.6 Tailwind CSS 工作原理

传统 CSS 框架（如 Bootstrap）提供预定义的组件类（`.btn-primary`、`.card`）。Tailwind 不同，它提供原子化的工具类，你直接在 HTML 中组合：

```html
<!-- 传统方式：写 CSS 类 -->
<style>
.post-title { font-size: 1.5rem; font-weight: bold; color: #333; }
</style>
<h1 class="post-title">标题</h1>

<!-- Tailwind 方式：直接用工具类 -->
<h1 class="text-2xl font-bold text-charcoal dark:text-white">标题</h1>
```

**为什么 Tailwind 需要构建步骤？**

Tailwind 有上万个工具类（`text-sm`、`text-base`、`text-lg`……每种颜色、间距、尺寸的排列组合）。如果全部加载，CSS 文件会有几 MB。所以 Tailwind 在构建时：

1. 扫描所有 `.blade.php` 文件中出现的类名
2. 只生成你实际用到的 CSS
3. 最终产物通常只有 10-30 KB

本项目的 `tailwind.config.js` 定义了扫描范围：
```javascript
content: [
    './resources/views/**/*.blade.php',   // 所有 Blade 模板
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
],
```

以及自定义的设计系统（颜色、字体、圆角等）：
```javascript
theme: {
    extend: {
        colors: {
            primary: { DEFAULT: '#5645d4', pressed: '#4534b3' },
            // ...
        },
        borderRadius: { btn: '8px', card: '12px' },
    },
},
```

### 8.7 Alpine.js 工作原理

Alpine.js 是一个轻量级 JS 框架，直接在 HTML 属性中声明交互逻辑，不需要写单独的组件文件：

```html
<!-- 暗色模式切换（本项目实际代码） -->
<html x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">

<!-- 切换按钮 -->
<button @click="darkMode = !darkMode">
    <span x-show="!darkMode">🌙</span>
    <span x-show="darkMode">☀️</span>
</button>
```

**Alpine 核心指令：**

| 指令 | 作用 | 示例 |
|------|------|------|
| `x-data` | 声明组件的响应式数据 | `x-data="{ open: false }"` |
| `x-show` | 条件显示/隐藏 | `x-show="open"` |
| `x-if` | 条件渲染（DOM 级别） | `x-if="loggedIn"` |
| `@click` | 点击事件 | `@click="open = !open"` |
| `:class` | 动态绑定 class | `:class="{ 'dark': darkMode }"` |
| `x-text` | 动态文本内容 | `x-text="display"` |
| `x-init` | 组件初始化时执行 | `x-init="start()"` |

### 8.8 暗色模式实现原理

本项目的暗色模式是一个完整的前端交互案例：

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. 页面加载（防闪烁）                                              │
│                                                                  │
│ <script>                                                         │
│   // 在 Alpine 加载前，立即读取 localStorage 设置 dark class         │
│   // 这样页面不会先显示亮色再切换到暗色（FOUC 问题）                    │
│   if (localStorage.getItem('darkMode') === 'true') {             │
│       document.documentElement.classList.add('dark');             │
│   }                                                              │
│ </script>                                                        │
└─────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. Alpine 初始化                                                  │
│                                                                  │
│ <html x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"│
│       x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"│
│       :class="{ 'dark': darkMode }">                             │
│                                                                  │
│ - x-data：声明 darkMode 状态，从 localStorage 读取初始值            │
│ - x-init：监听 darkMode 变化，自动保存到 localStorage              │
│ - :class：darkMode 为 true 时，给 <html> 加上 dark 类              │
└─────────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. Tailwind 响应 dark 类                                          │
│                                                                  │
│ tailwind.config.js: darkMode: 'class'                            │
│                                                                  │
│ 当 <html> 有 dark 类时，所有 dark: 前缀的样式生效：                  │
│ class="bg-white dark:bg-gray-900 text-black dark:text-white"     │
│         ↑ 亮色模式用这个    ↑ 暗色模式用这个                        │
└─────────────────────────────────────────────────────────────────┘
```

### 8.9 开发工作流

```bash
# 首次克隆项目后
npm install              # 安装前端依赖到 node_modules/

# 日常开发（两个终端窗口）
php artisan serve        # 终端 1：启动 PHP 开发服务器
npm run dev              # 终端 2：启动 Vite 开发服务器（热更新）

# 部署前
npm run build            # 构建生产版本到 public/build/
```

**开发模式（`npm run dev`）的好处：**
- 修改 CSS/JS 后浏览器自动刷新，不需要手动 F5
- 修改 Blade 模板后也会自动刷新（`refresh: true` 配置）
- 不压缩代码，方便调试
- 错误信息直接显示在浏览器上

### 8.10 文件关系总结

```
你编辑的源码                    构建工具                    浏览器加载的产物
─────────────                  ────────                    ──────────────

resources/css/app.css ──┐                          ┌── public/build/assets/app-[hash].css
                        ├── Vite + Tailwind + PostCSS ──┤
resources/js/app.js  ───┘                          └── public/build/assets/app-[hash].js
                                    ▲
                                    │
                        tailwind.config.js（设计系统配置）
                        vite.config.js（构建配置）
                        postcss.config.js（CSS 处理插件）
                        package.json（依赖声明）
```

**记住：** 你永远不需要手动编辑 `public/build/` 下的文件。修改 `resources/` 下的源码，然后让 Vite 自动构建。

---

## 9. 单元测试与功能测试详解

### 9.1 为什么要写测试？

写测试的核心目标只有一个：**让你敢改代码。**

没有测试时，你改了一个函数，不确定会不会影响其他地方，只能手动打开浏览器一个个页面点。有了测试，改完代码跑一遍 `php artisan test`，几秒钟就知道有没有搞坏东西。

**测试的三个核心价值：**

| 价值 | 说明 |
|------|------|
| 防止回归 | 修 Bug A 时不会引入 Bug B，因为 B 的测试会立刻报错 |
| 文档作用 | 测试代码就是"活的文档"，描述了系统应该怎么工作 |
| 设计驱动 | 写测试时会逼你思考接口设计，难测试的代码通常也是设计不好的代码 |

### 9.2 单元测试 vs 功能测试

Laravel 把测试分为两类，放在不同目录：

```
tests/
├── Unit/       ← 单元测试：测试单个类/方法，不启动框架
└── Feature/    ← 功能测试：模拟 HTTP 请求，测试完整流程
```

| 维度 | 单元测试（Unit） | 功能测试（Feature） |
|------|-----------------|-------------------|
| 测试范围 | 一个方法/一个类 | 一个完整的用户操作流程 |
| 是否启动 Laravel | ❌ 不启动 | ✅ 启动完整框架 |
| 是否访问数据库 | ❌ 通常不访问 | ✅ 使用测试数据库 |
| 运行速度 | 极快（毫秒级） | 较慢（需要启动框架+数据库） |
| 继承的基类 | `PHPUnit\Framework\TestCase` | `Tests\TestCase`（Laravel 的） |
| 适合测试 | 工具函数、计算逻辑、数据转换 | 路由、权限、表单提交、业务流程 |

**本项目的实际情况：** 主要写功能测试（Feature），因为博客系统的核心逻辑就是"用户通过 HTTP 请求操作数据"。

### 9.3 测试的核心逻辑：Arrange → Act → Assert

每个测试方法都遵循三步模式（也叫 AAA 模式）：

```php
public function test_user_can_create_post(): void
{
    // 1. Arrange（准备）— 构造测试所需的前置条件
    $user = User::factory()->create();
    $category = Category::factory()->create();

    // 2. Act（执行）— 执行你要测试的操作
    $response = $this->actingAs($user)->post('/my/posts', [
        'title' => '测试文章标题',
        'content' => '这是测试文章内容',
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    // 3. Assert（断言）— 验证结果是否符合预期
    $response->assertRedirect(route('my.posts.index'));
    $this->assertDatabaseHas('posts', [
        'title' => '测试文章标题',
        'user_id' => $user->id,
    ]);
}
```

**用大白话说：**
1. **准备** — 创建假用户、假数据，搭好测试场景
2. **执行** — 模拟用户的操作（发请求、调方法）
3. **断言** — 检查结果对不对（页面状态码、数据库有没有写入、有没有报错）

### 9.4 本项目的测试文件解析

#### 文件结构

```
tests/
├── TestCase.php                          ← 基类（所有 Feature 测试继承它）
├── Unit/
│   └── ExampleTest.php                   ← 示例单元测试
└── Feature/
    ├── ExampleTest.php                   ← 示例功能测试（首页返回 200）
    ├── PostTest.php                      ← 文章相关测试（10 个测试）
    ├── ProfileTest.php                   ← 个人资料测试（5 个测试）
    └── Auth/
        ├── AuthenticationTest.php        ← 登录/登出测试
        ├── RegistrationTest.php          ← 注册测试
        ├── PasswordConfirmationTest.php  ← 密码确认测试
        ├── PasswordUpdateTest.php        ← 密码修改测试
        ├── PasswordResetTest.php         ← 密码重置测试
        └── EmailVerificationTest.php     ← 邮箱验证测试
```

#### PostTest.php 逐个解读

这是本项目最有代表性的测试文件，覆盖了博客的核心业务逻辑：

```php
// 测试 1：游客能看到首页
public function test_guest_can_view_homepage(): void
{
    Post::factory(3)->published()->create();  // 准备：创建 3 篇已发布文章
    $response = $this->get('/');              // 执行：访问首页
    $response->assertStatus(200);            // 断言：返回 200
    $response->assertViewHas('posts');       // 断言：视图中有 $posts 变量
}

// 测试 2：游客能看到已发布的文章
public function test_guest_can_view_published_post(): void
{
    $post = Post::factory()->published()->create();
    $response = $this->get("/posts/{$post->slug}");
    $response->assertStatus(200);
    $response->assertSee($post->title);      // 断言：页面中包含文章标题
}

// 测试 3：游客看不到草稿文章（返回 404）
public function test_guest_cannot_view_draft_post(): void
{
    $post = Post::factory()->draft()->create();
    $response = $this->get("/posts/{$post->slug}");
    $response->assertStatus(404);            // 断言：草稿返回 404
}

// 测试 4：游客不能访问创建文章页面（被重定向到登录）
public function test_guest_cannot_access_create_post_page(): void
{
    $response = $this->get('/my/posts/create');
    $response->assertRedirect('/login');      // 断言：重定向到登录页
}

// 测试 5：登录用户可以创建文章
public function test_user_can_create_post(): void
{
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $response = $this->actingAs($user)->post('/my/posts', [...]);
    $response->assertRedirect(route('my.posts.index'));
    $this->assertDatabaseHas('posts', ['title' => '...']);
}

// 测试 6：用户只能编辑自己的文章
public function test_user_can_only_edit_own_post(): void
{
    $author = User::factory()->create();
    $other = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);

    $this->actingAs($author)->get("/my/posts/{$post->id}/edit")
         ->assertStatus(200);                // 作者可以编辑
    $this->actingAs($other)->get("/my/posts/{$post->id}/edit")
         ->assertStatus(403);                // 其他人返回 403 禁止
}

// 测试 7-8：评论权限
// 游客不能评论（重定向登录），登录用户可以评论

// 测试 9-10：分类页和标签页能正常显示文章
```

**这组测试覆盖了什么？**
- ✅ 页面可访问性（200/404）
- ✅ 认证保护（未登录重定向到 /login）
- ✅ 授权保护（非作者返回 403）
- ✅ 数据写入正确性（assertDatabaseHas）
- ✅ 业务规则（草稿不可见、只能编辑自己的文章）

### 9.5 关键测试工具和方法

#### RefreshDatabase trait

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;  // 每个测试方法执行前，自动重置数据库
}
```

这个 trait 保证每个测试方法都在"干净"的数据库上运行，测试之间互不影响。

#### Factory（工厂）— 快速创建测试数据

```php
// 创建一个用户（保存到数据库）
$user = User::factory()->create();

// 创建 3 篇已发布文章
$posts = Post::factory(3)->published()->create();

// 创建一篇草稿
$draft = Post::factory()->draft()->create();

// 创建文章并指定作者
$post = Post::factory()->create(['user_id' => $author->id]);
```

`published()` 和 `draft()` 是 Factory 中定义的"状态"（state），用来快速切换数据的不同形态。

#### actingAs — 模拟登录用户

```php
// 以 $user 身份发起请求（不需要真的走登录流程）
$this->actingAs($user)->get('/my/posts');
$this->actingAs($user)->post('/my/posts', [...]);
```

#### 常用断言方法

```php
// HTTP 状态码
$response->assertStatus(200);          // 成功
$response->assertStatus(403);          // 禁止访问
$response->assertStatus(404);          // 未找到

// 重定向
$response->assertRedirect('/login');   // 断言重定向到指定 URL
$response->assertRedirect(route('my.posts.index'));

// 页面内容
$response->assertSee('文章标题');       // 页面中包含指定文本
$response->assertDontSee('草稿内容');   // 页面中不包含指定文本

// 视图数据
$response->assertViewHas('posts');     // 视图中有 $posts 变量
$response->assertViewHas('posts', function ($posts) {
    return $posts->count() === 3;      // 还可以验证变量的值
});

// 数据库
$this->assertDatabaseHas('posts', [    // 数据库中存在匹配的记录
    'title' => '测试文章',
    'user_id' => 1,
]);
$this->assertDatabaseMissing('posts', [...]); // 数据库中不存在
$this->assertDatabaseCount('posts', 5);       // 表中有 5 条记录

// Session
$response->assertSessionHasNoErrors();         // 没有验证错误
$response->assertSessionHasErrors(['title']);   // 有指定字段的错误

// 认证状态
$this->assertGuest();                  // 当前是未登录状态
$this->assertAuthenticated();          // 当前是已登录状态
```

### 9.6 测试的运行方式

```bash
# 运行所有测试
php artisan test
# 或
composer test

# 只运行某个文件
php artisan test tests/Feature/PostTest.php

# 只运行某个方法
php artisan test --filter=test_user_can_create_post

# 只运行 Unit 测试套件
php artisan test --testsuite=Unit

# 只运行 Feature 测试套件
php artisan test --testsuite=Feature

# 并行运行（加速）
php artisan test --parallel

# 遇到第一个失败就停止
php artisan test --stop-on-failure
```

### 9.7 测试配置（phpunit.xml）

```xml
<php>
    <env name="APP_ENV" value="testing"/>           <!-- 使用 testing 环境 -->
    <env name="DB_DATABASE" value="myblog_test"/>   <!-- 使用独立的测试数据库 -->
    <env name="BCRYPT_ROUNDS" value="4"/>           <!-- 降低加密轮次，加速测试 -->
    <env name="CACHE_STORE" value="array"/>         <!-- 内存缓存，不写磁盘 -->
    <env name="SESSION_DRIVER" value="array"/>      <!-- 内存 Session -->
    <env name="MAIL_MAILER" value="array"/>         <!-- 不真的发邮件 -->
    <env name="QUEUE_CONNECTION" value="sync"/>     <!-- 队列同步执行，不入队 -->
</php>
```

**关键设计：**
- 使用独立的 `myblog_test` 数据库，不会污染开发数据
- 所有外部服务（缓存、Session、邮件、队列）都用内存驱动，测试不依赖外部基础设施
- `BCRYPT_ROUNDS=4` 降低密码哈希轮次，因为测试中频繁创建用户，不需要生产级安全性

### 9.8 如何写一个新测试（实战模板）

假设你要测试"管理员可以删除任何文章"：

```php
<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPostTest extends TestCase
{
    use RefreshDatabase;

    // 测试前的准备工作（每个测试方法执行前都会调用）
    protected function setUp(): void
    {
        parent::setUp();
        // 创建角色（因为 RefreshDatabase 每次都清空数据库）
        Role::create(['name' => 'admin']);
    }

    public function test_admin_can_delete_any_post(): void
    {
        // Arrange
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $post = Post::factory()->published()->create();

        // Act
        $response = $this->actingAs($admin)->delete("/admin/posts/{$post->id}");

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_non_admin_cannot_delete_others_post(): void
    {
        // Arrange
        $user = User::factory()->create();
        $post = Post::factory()->create(); // 别人的文章

        // Act
        $response = $this->actingAs($user)->delete("/admin/posts/{$post->id}");

        // Assert
        $response->assertStatus(403);
        $this->assertDatabaseHas('posts', ['id' => $post->id]); // 文章还在
    }
}
```

### 9.9 测试命名规范

Laravel 社区的命名惯例：

```php
// 格式：test_[谁]_[能/不能]_[做什么]
public function test_guest_can_view_homepage(): void
public function test_guest_cannot_view_draft_post(): void
public function test_user_can_create_post(): void
public function test_user_can_only_edit_own_post(): void
public function test_admin_can_delete_any_post(): void
```

好的测试名读起来就像一句话，描述了一条业务规则。

### 9.10 测试思维：应该测什么？

**优先测试的（高价值）：**
- 权限控制 — 谁能访问什么（最容易出安全漏洞的地方）
- 核心业务流程 — 创建文章、发表评论、用户注册
- 边界条件 — 草稿不可见、只能编辑自己的文章
- 数据完整性 — 创建后数据库中确实有正确的记录

**不需要测试的：**
- 框架本身的功能（Laravel 自己有测试）
- 简单的 getter/setter
- 纯 UI 展示（用浏览器测试工具更合适）

**测试金字塔：**

```
        /\
       /  \        E2E 测试（浏览器自动化，最慢，写少量）
      /    \
     /──────\
    /        \     功能测试（HTTP 请求级别，本项目主力）
   /          \
  /────────────\
 /              \  单元测试（纯逻辑，最快，有复杂计算时写）
/________________\
```

本项目以功能测试为主，因为博客系统的核心就是"HTTP 请求 → 数据库操作 → 页面响应"，功能测试能最直接地验证这个链路。

