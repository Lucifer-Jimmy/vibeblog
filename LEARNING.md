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

