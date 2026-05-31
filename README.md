# MyBlog · Laravel 博客系统开发文档

> 本文档既是项目说明，也是开发过程中的活文档。每完成一个里程碑就同步更新对应章节，作为大作业答辩材料的一部分。

---

## 目录

- [1. 项目概述](#1-项目概述)
- [2. 技术栈](#2-技术栈)
- [3. 环境要求](#3-环境要求)
- [4. 快速开始](#4-快速开始)
- [5. 功能规划](#5-功能规划)
- [6. 数据库设计](#6-数据库设计)
- [7. 目录结构](#7-目录结构)
- [8. 路由与页面规划](#8-路由与页面规划)
- [9. 开发规范](#9-开发规范)
- [10. 开发里程碑](#10-开发里程碑)
- [11. 测试策略](#11-测试策略)
- [12. 部署说明](#12-部署说明)
- [13. 常用命令速查](#13-常用命令速查)
- [14. 变更日志](#14-变更日志)
- [15. 参考资料](#15-参考资料)

---

## 1. 项目概述

**项目名称**：MyBlog

**项目定位**：一个使用 Laravel + MySQL 实现的个人博客系统，作为课程大作业提交。先完成核心功能（用户、文章、分类、标签、评论），后续按里程碑迭代增加搜索、后台管理、权限等进阶功能。

**课程约束**：

| 项目 | 内容 |
|---|---|
| 截止/答辩日期 | （待补充） |
| 技术栈限制 | （待补充：老师是否指定 PHP/框架版本） |
| 评分重点 | （待补充：功能完整性？代码规范？文档？演示效果？） |
| 提交形式 | （待补充：源码包 / Git 仓库 / 部署到指定服务器 / 演示视频） |

> ⚠️ 请尽快补充以上信息，它们直接决定里程碑的优先级和取舍。

**目标用户**：
- 访客：浏览文章、分类、标签、评论
- 注册用户：评论、收藏（后续扩展）
- 管理员：发布与管理文章、审核评论

**设计原则**：
- 优先实现核心功能，保证全流程跑通后再做美化与扩展
- 每个功能配套迁移、模型、控制器、视图、测试，模块完整闭环
- 使用 Laravel 官方推荐方式，避免过度设计

---

## 2. 技术栈

### 后端

| 类别 | 选型 | 版本 | 说明 |
|---|---|---|---|
| 语言 | PHP | 8.5+ | 严格类型、性能更好 |
| 框架 | Laravel | 13.x | 最新稳定版 |
| 数据库 | MySQL | 8.0+ | 支持 JSON、CTE、窗口函数 |
| 依赖管理 | Composer | 2.x | |
| 认证脚手架 | Laravel Breeze | 最新 | Blade Stack |
| 测试 | Pest / PHPUnit | 最新 | 优先用 Pest |

### 前端（方案一：Blade + Tailwind + Alpine.js）

| 类别 | 选型 | 说明 |
|---|---|---|
| 模板引擎 | Blade | Laravel 内置 |
| 样式 | Tailwind CSS 3 | Breeze 自带 |
| 交互 | Alpine.js | 轻量级，处理弹窗、下拉、切换等 |
| 构建工具 | Vite | Laravel 11 默认 |
| 图标 | Heroicons / Lucide | 与 Tailwind 风格一致 |
| 设计规范 | DESIGN.md | Notion 风格设计系统，详见项目根目录 `DESIGN.md` |

**前端设计说明**：本项目前端样式参照 `DESIGN.md` 中定义的 Notion 风格设计系统实现，包括：
- 色彩：以 `#5645d4`（紫色）为主色调，深海军蓝 `#0a1530` 用于 Hero 区域，搭配柔和的 pastel 卡片背景色
- 字体：使用 Inter（Notion Sans 的开源替代）作为全站字体，遵循文档中的字号/字重层级
- 圆角：按钮 8px、卡片 12px、徽章/标签 pill 形状，保持 Notion 的方正几何风格
- 间距：基于 4px 基础单位的间距系统
- 组件：按钮、卡片、输入框、标签等均遵循 `DESIGN.md` 中的 token 定义

### 推荐生态包（按里程碑引入，不一次装完）

| 用途 | 包名 | 引入时机 |
|---|---|---|
| Markdown 渲染 | `league/commonmark` | M3 文章详情 |
| 图片处理 | `intervention/image` | M5 图片上传 |
| 调试工具 | `barryvdh/laravel-debugbar` | 开发阶段 |
| IDE 辅助 | `barryvdh/laravel-ide-helper` | 开发阶段 |
| 角色权限 | `spatie/laravel-permission` | M7 权限控制 |
| SEO | `artesaos/seotools` | M8 SEO 优化 |
| 后台管理 | `filament/filament` | 可选，作为后期升级 |

---

## 3. 环境要求

- macOS / Linux / Windows（推荐 macOS + Laravel Herd）
- PHP >= 8.3（实际使用 8.5）
- Composer >= 2.5
- Node.js >= 20 LTS（实际使用 26）
- MySQL >= 8.0（本项目使用 Docker 容器运行，端口 3306）
- Git
- Docker（用于运行 MySQL 服务）

**推荐工具链**
- Laravel Herd（一键搞定 PHP / Nginx / Node）
- Docker 运行 MySQL（`docker run -d -p 3306:3306 -e MYSQL_ROOT_PASSWORD=root123456 mysql:8.0`）
- TablePlus 或 DBeaver（数据库 GUI）
- VS Code + 插件：PHP Intelephense、Laravel Blade、Tailwind CSS IntelliSense

---

## 4. 快速开始

```bash
# 1. 克隆项目
git clone <repo-url> myblog
cd myblog

# 2. 安装依赖
composer install
npm install

# 3. 配置环境变量
cp .env.example .env
php artisan key:generate

# 4. 修改 .env 中的数据库配置
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=myblog
# DB_USERNAME=root
# DB_PASSWORD=root123456

# 5. 创建数据库（Docker MySQL，端口 3306）
mysql -h127.0.0.1 -P3306 -uroot -proot123456 -e "CREATE DATABASE myblog DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. 执行迁移和填充
php artisan migrate --seed

# 7. 启动开发服务器（两个终端分别运行）
php artisan serve          # 后端 http://localhost:8000
npm run dev                # 前端资源热更新
```

**默认测试账号**（在 `DatabaseSeeder` 中创建，仅开发环境使用）

| 角色 | 邮箱 | 密码 |
|---|---|---|
| 管理员 | admin@myblog.test | password |
| 普通用户 | user@myblog.test | password |

---

## 5. 功能规划

### 5.1 必做功能（M1 ~ M4）

- [x] 用户认证：注册、登录、注销、密码重置（Breeze 提供）
- [x] 文章管理：创建、编辑、删除、列表、详情
- [x] 分类：增删改查，文章按分类筛选
- [x] 标签：多对多关联，文章按标签筛选
- [x] 评论：登录用户对文章发表评论
- [x] 分页：列表页分页
- [x] 草稿/发布状态切换

### 5.2 进阶功能（M5 ~ M8）

- [x] 文章封面图上传（本地存储）
- [x] Markdown 编辑器与渲染
- [x] 文章浏览量统计（简单实现每次 +1，后续可用 Redis 缓存优化）
- [x] 全文搜索（MySQL FULLTEXT）
- [x] 角色权限（admin / author / user）
- [x] 后台管理面板
- [ ] SEO（slug、meta、sitemap）
- [ ] RSS 订阅
- [x] 定制 404/403/500 错误页面

### 5.3 可选加分项

- [ ] 点赞 / 收藏
- [ ] 评论楼中楼回复（数据库已预留 `parent_id`，前端展示在此阶段实现）
- [ ] 邮件通知（评论提醒）
- [x] 暗色主题
- [ ] 国际化（中英文切换）
- [ ] API 接口（Sanctum + 给小程序/移动端用）

---

## 6. 数据库设计

### 6.1 核心表

**users**（沿用 Breeze 默认表，扩展少量字段）

| 字段 | 类型 | 说明 |
|---|---|---|
| id | bigint unsigned PK | |
| name | varchar(50) | 用户名 |
| email | varchar(100) unique | |
| email_verified_at | timestamp nullable | |
| password | varchar(255) | bcrypt |
| avatar | varchar(255) nullable | 头像 URL |
| role | enum('admin','author','user') | 默认 user，M7 启用 |
| remember_token | varchar(100) | |
| timestamps | | |

**posts**

| 字段 | 类型 | 说明 |
|---|---|---|
| id | bigint unsigned PK | |
| user_id | bigint unsigned FK→users.id | 作者 |
| category_id | bigint unsigned FK→categories.id nullable | |
| title | varchar(150) | |
| slug | varchar(180) unique | URL 友好 |
| excerpt | varchar(300) nullable | 摘要 |
| content | longtext | Markdown 原文 |
| cover | varchar(255) nullable | 封面图路径 |
| status | enum('draft','published') | 默认 draft |
| views | unsignedInteger | 默认 0 |
| published_at | timestamp nullable | 发布时间 |
| timestamps | | |
| softDeletes | | |

索引：`(status, published_at)`、`slug` 唯一索引、FULLTEXT(`title`,`content`)（M6）

**categories**

| 字段 | 类型 | 说明 |
|---|---|---|
| id | bigint unsigned PK | |
| name | varchar(50) | |
| slug | varchar(80) unique | |
| description | varchar(255) nullable | |
| timestamps | | |

**tags**

| 字段 | 类型 | 说明 |
|---|---|---|
| id | bigint unsigned PK | |
| name | varchar(30) | |
| slug | varchar(50) unique | |
| timestamps | | |

**post_tag**（多对多中间表）

| 字段 | 类型 | 说明 |
|---|---|---|
| post_id | FK→posts.id | |
| tag_id | FK→tags.id | |
| 主键 | (post_id, tag_id) | |

**comments**

| 字段 | 类型 | 说明 |
|---|---|---|
| id | bigint unsigned PK | |
| post_id | FK→posts.id | |
| user_id | FK→users.id | |
| parent_id | FK→comments.id nullable | 回复，可选 |
| content | text | |
| status | enum('visible','hidden') | 默认 visible |
| timestamps | | |

### 6.2 模型关系

```
User    1───*  Post
User    1───*  Comment
Post    *───1  Category
Post    *───*  Tag           (post_tag)
Post    1───*  Comment
Comment *───1  User
Comment *───1  Post
```

### 6.3 ER 图

后续用 dbdiagram.io 绘制并导出图片放到 `docs/er.png`，本文档引用：

```
（占位：M2 完成数据库迁移后补充 ER 图）
```

---

## 7. 目录结构

遵循 Laravel 默认结构，仅说明本项目重点目录：

```
myblog/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── PostController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── TagController.php
│   │   │   ├── CommentController.php
│   │   │   └── Admin/              # M7 后台控制器
│   │   ├── Requests/               # 表单验证类
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Post.php
│   │   ├── Category.php
│   │   ├── Tag.php
│   │   └── Comment.php
│   └── Policies/                   # M7 权限策略
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php       # 主布局
│   │   ├── components/             # Blade 组件
│   │   ├── posts/
│   │   ├── categories/
│   │   ├── tags/
│   │   └── auth/                   # Breeze 自带
│   ├── css/app.css
│   └── js/app.js
├── routes/
│   ├── web.php
│   └── auth.php                    # Breeze 自带
├── tests/
│   ├── Feature/
│   └── Unit/
├── docs/                           # 项目文档（ER 图、答辩 PPT 等）
└── README.md
```

---

## 8. 路由与页面规划

### 8.1 前台路由

| Method | URI | Controller@Action | 说明 |
|---|---|---|---|
| GET | `/` | `PostController@index` | 首页（文章列表） |
| GET | `/posts/{post:slug}` | `PostController@show` | 文章详情 |
| GET | `/categories/{category:slug}` | `CategoryController@show` | 分类下文章 |
| GET | `/tags/{tag:slug}` | `TagController@show` | 标签下文章 |
| POST | `/posts/{post}/comments` | `CommentController@store` | 发表评论（需登录） |
| GET | `/search` | `SearchController@index` | 搜索结果（M6） |

### 8.2 用户中心（登录后）

| Method | URI | 说明 |
|---|---|---|
| GET | `/dashboard` | 个人主页（Breeze 自带） |
| GET | `/my/posts` | 我的文章 |
| GET | `/my/posts/create` | 写文章 |
| POST | `/my/posts` | 创建 |
| GET | `/my/posts/{post}/edit` | 编辑 |
| PUT | `/my/posts/{post}` | 更新 |
| DELETE | `/my/posts/{post}` | 删除（软删除） |

### 8.3 后台（M7 引入）

| URI | 说明 |
|---|---|
| `/admin` | 仪表盘 |
| `/admin/posts` | 文章管理 |
| `/admin/categories` | 分类管理 |
| `/admin/tags` | 标签管理 |
| `/admin/comments` | 评论审核 |
| `/admin/users` | 用户管理 |

### 8.4 页面布局概要

> 所有页面样式遵循 `DESIGN.md` 设计规范，以下描述对应的设计 token。

| 页面 | 布局描述 |
|---|---|
| 首页 | 顶部导航栏（白底 + 1px hairline 底边框，Logo + 分类链接 + 搜索框 `search-pill` + 登录/用户头像）；可选 Hero 区域（`hero-band-dark` 深海军蓝背景 + 紫色 CTA）；主体为文章卡片列表（`card-base` 样式，封面图 + 标题 + 摘要 + 分类标签 `badge-tag-*` + 发布时间），右侧可选侧边栏（热门文章 / 标签云）；底部 `footer-region` |
| 文章详情 | 标题（`heading-1`）+ 元信息（作者、时间、分类 `badge-tag-purple`、标签 `badge-tag-*`）→ 正文（`body-md`，Markdown 渲染）→ 评论区（`card-base` 卡片包裹） |
| 分类/标签页 | 与首页类似的文章列表，顶部显示当前分类/标签名称（`heading-2`） |
| 写文章 | 表单页：标题 `text-input`、分类下拉、标签多选、Markdown 编辑区、状态切换 `pill-tab`、提交 `button-primary` |
| 后台 | 左侧菜单（深色背景）+ 右侧内容区的经典管理后台布局 |

> 后续可补充线框图到 `docs/wireframes/` 目录。

---

## 9. 开发规范

### 9.1 Git 工作流

- 主分支：`main`（始终保持可运行）
- 开发分支：`develop`
- 功能分支：`feature/<功能名>`，如 `feature/post-crud`
- 修复分支：`fix/<问题>`

**提交信息**遵循 Conventional Commits：

```
feat:     新功能
fix:      bug 修复
docs:     文档更新
style:    代码格式（不影响功能）
refactor: 重构
test:     测试相关
chore:    构建/工具变动
```

例：`feat(post): add post create and edit pages`

### 9.2 代码风格

- PHP 用 **Laravel Pint**：`./vendor/bin/pint`
- JS / Blade 用 **Prettier**（可选）
- 命名约定：
  - 类：PascalCase（`PostController`）
  - 方法 / 变量：camelCase（`getPublishedPosts`）
  - 路由名：kebab-case（`posts.show`）
  - 数据库表：复数 snake_case（`post_tag`）

### 9.3 控制器规范

- 控制器只做调度，业务复杂时下沉到 **Service** 类
- 表单验证一律用 **FormRequest**，不在控制器里写 validate
- 返回视图统一用 `view('xxx', compact(...))`
- 资源控制器优先用 `Route::resource()`

### 9.4 安全清单

- [x] CSRF：Blade 表单加 `@csrf`
- [x] XSS：输出用 `{{ }}`，避免 `{!! !!}`，Markdown 渲染需手动 sanitize
- [x] SQL 注入：始终用 Eloquent / Query Builder，禁止字符串拼接
- [x] 文件上传：白名单后缀 + MIME 校验 + 重命名
- [x] 鉴权：操作他人资源前用 Policy 检查
- [x] 密码：Hash::make，禁止明文

---

## 10. 开发里程碑

每完成一个里程碑：合并到 `main`、打 tag、更新本节复选框、追加到[变更日志](#14-变更日志)。

### 时间规划参考

| 里程碑 | 预计工时 | 计划起止日期 |
|---|---|---|
| M1 项目初始化 | 0.5 天 | （待排期） |
| M2 数据库与模型 | 1 天 | （待排期） |
| M3 前台浏览 | 2 天 | （待排期） |
| M4 用户写文章 | 2 天 | （待排期） |
| M5 封面图与优化 | 1 天 | （待排期） |
| M6 搜索 | 1 天 | （待排期） |
| M7 权限与后台 | 2~3 天 | （待排期） |
| M8 SEO 与上线 | 1 天 | （待排期） |

> 请根据答辩日期倒推，填入具体日期。M1~M4 为必做，M5~M8 视时间取舍。

### M1 · 项目初始化（预计 0.5 天）

- [x] `composer create-project laravel/laravel myblog`
- [x] 安装 Breeze（Blade + Tailwind）：`composer require laravel/breeze --dev && php artisan breeze:install blade`
- [x] 配置 `.env`，连接 Docker MySQL（127.0.0.1:3306，root/root123456）
- [x] `npm install && npm run dev` 跑通首页
- [x] `npm run build` 验证生产构建正常
- [ ] 初始化 Git，推送到远端
- [x] 安装 debugbar、ide-helper

### M2 · 数据库设计与基础模型（1 天）

- [x] 编写 categories / tags / posts / comments 迁移
- [x] 编写各表 Factory 和 Seeder（生成测试数据 ≥30 篇文章）
- [x] 定义模型关联关系
- [x] `php artisan migrate:fresh --seed` 跑通

### M3 · 前台浏览（2 天）

- [x] 主布局 `layouts/app.blade.php`：导航栏 + footer
- [x] 首页文章列表（分页、按发布时间倒序）
- [x] 文章详情页（Markdown 渲染、显示分类标签）
- [x] 分类页、标签页
- [x] 引入 `league/commonmark` 渲染 Markdown
- [x] 定制 404/403 错误页面

### M4 · 用户写文章（2 天）

- [x] 路由组 `auth` 中间件保护 `/my/posts/*`
- [x] 写文章表单（标题、分类、标签多选、内容、状态）
- [x] PostRequest 表单验证
- [x] 编辑、删除（仅作者本人）
- [x] 评论功能（登录后才能评）

### M5 · 文章封面图与体验优化（1 天）

- [x] 图片上传到 `storage/app/public/covers`
- [x] `php artisan storage:link`
- [x] 文章卡片显示封面
- [x] 浏览量统计（每次访问 +1，简单实现）

### M6 · 搜索（1 天）

- [x] 在 posts 表加 FULLTEXT 索引（迁移用 raw SQL）
- [x] 搜索路由与结果页
- [x] 搜索框组件，加在导航栏

### M7 · 角色权限与后台（2~3 天）

- [x] 引入 `spatie/laravel-permission`
- [x] 定义 admin / author / user 角色
- [x] Policy 定义 Post / Comment 的更新删除规则
- [x] 后台管理路由、布局、CRUD 页面
- [x] 评论审核（隐藏违规评论）

### M8 · SEO 与上线（1 天）

- [x] slug 自动生成（Str::slug）
- [x] meta 标题 / 描述
- [x] sitemap.xml
- [x] 部署文档完善
- [ ] 答辩 PPT、演示数据

---

## 11. 测试策略

> 大作业不强制 100% 覆盖，但建议核心功能至少有 1~2 个测试用例，体现工程质量。

### 11.1 测试类型

- **Feature 测试**：HTTP 请求 + 数据库断言（最有性价比）
- **Unit 测试**：纯函数、Service 类

### 11.2 重点用例

- [x] 游客可访问首页和文章详情
- [x] 未登录用户不能访问写文章页
- [x] 用户只能编辑/删除自己的文章
- [x] 创建文章时表单验证生效
- [x] 评论需要登录

### 11.3 运行测试

```bash
php artisan test
# 或
./vendor/bin/pest
```

---

## 12. 部署说明

### 12.1 本地演示部署

```bash
# 1. 确保 MySQL Docker 容器运行中
docker ps | grep mysql

# 2. 安装依赖
composer install
npm install

# 3. 配置环境
cp .env.example .env
php artisan key:generate
# 编辑 .env 设置数据库连接

# 4. 初始化数据库
php artisan migrate --seed
php artisan storage:link

# 5. 生产构建前端资源
npm run build

# 6. 优化 Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. 启动
php artisan serve --host=0.0.0.0 --port=8000
```

### 12.2 服务器部署

**最低配置**：1 核 1G，Nginx + PHP-FPM 8.3+ + MySQL 8.0。

**关键步骤**：

```bash
# 1. 克隆代码
git clone <repo-url> /var/www/myblog
cd /var/www/myblog

# 2. 安装 PHP 依赖（生产模式）
composer install --no-dev --optimize-autoloader

# 3. 构建前端
npm ci && npm run build

# 4. 配置环境变量
cp .env.example .env
php artisan key:generate
# 编辑 .env：设置 APP_ENV=production, APP_DEBUG=false, 数据库信息等

# 5. 数据库迁移
php artisan migrate --force

# 6. 创建存储链接
php artisan storage:link

# 7. 优化缓存
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 8. 设置目录权限
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 12.3 Nginx 配置示例

```nginx
server {
    listen 80;
    server_name myblog.example.com;
    root /var/www/myblog/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # 静态资源缓存
    location /build/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 12.4 环境变量清单

| 变量 | 说明 | 示例值 |
|------|------|--------|
| `APP_NAME` | 站点名称 | MyBlog |
| `APP_ENV` | 环境 | production |
| `APP_DEBUG` | 调试模式 | false |
| `APP_URL` | 站点 URL | https://myblog.example.com |
| `DB_HOST` | 数据库主机 | 127.0.0.1 |
| `DB_PORT` | 数据库端口 | 3306 |
| `DB_DATABASE` | 数据库名 | myblog |
| `DB_USERNAME` | 数据库用户 | myblog_user |
| `DB_PASSWORD` | 数据库密码 | (安全密码) |
| `MAIL_MAILER` | 邮件驱动 | smtp |
| `MAIL_HOST` | 邮件服务器 | smtp.example.com |

### 12.5 常见问题排查

| 问题 | 解决方案 |
|------|----------|
| 500 错误 | 检查 `storage/logs/laravel.log`，确认 `.env` 配置正确 |
| CSS/JS 404 | 运行 `npm run build`，确认 `public/build/` 目录存在 |
| 图片无法显示 | 运行 `php artisan storage:link` 创建符号链接 |
| 权限错误 | `chmod -R 775 storage bootstrap/cache` |
| 数据库连接失败 | 检查 `.env` 中 DB_* 配置，确认 MySQL 服务运行中 |
| 路由 404 | 运行 `php artisan route:cache` 或检查 Nginx `try_files` 配置 |
| 缓存问题 | `php artisan optimize:clear` 清除所有缓存 |

可选 PaaS：Sevalla、Laravel Forge、Railway。

---

## 13. 常用命令速查

```bash
# 创建模型 + 迁移 + 控制器 + 工厂 + Seeder
php artisan make:model Post -mcfs --resource

# 创建表单请求
php artisan make:request StorePostRequest

# 创建 Policy
php artisan make:policy PostPolicy --model=Post

# 跑迁移
php artisan migrate
php artisan migrate:fresh --seed   # 重置数据库

# 清缓存
php artisan optimize:clear

# 进入 tinker
php artisan tinker

# 路由列表
php artisan route:list

# 代码格式化
./vendor/bin/pint

# 运行测试
php artisan test
```

---

## 14. 变更日志

格式：`YYYY-MM-DD · 里程碑 · 简述`

- 2026-05-28 · 初始化 · 创建开发文档 README.md，确定技术栈与里程碑
- 2026-05-31 · M1 完成 · Laravel 13 + Breeze 安装，MySQL 连接配置，debugbar/ide-helper 安装
- 2026-05-31 · M2 完成 · 数据库迁移（users/categories/tags/posts/comments/post_tag），模型关联，Factory + Seeder（35篇测试文章）
- 2026-05-31 · M3 完成 · 前台首页、文章详情（Markdown渲染）、分类页、标签页、自定义错误页、Notion风格UI
- 2026-05-31 · M4 完成 · 用户写文章（CRUD）、表单验证、评论功能、权限控制、Feature测试（35个测试全部通过）
- 2026-05-31 · M5 完成 · 封面图上传（storage/public/covers）、浏览量统计
- 2026-05-31 · M6 完成 · MySQL FULLTEXT 全文搜索、导航栏搜索框、搜索结果页
- 2026-05-31 · M7 完成 · spatie/laravel-permission 角色权限、后台管理面板（仪表盘/文章/分类/评论审核）
- 2026-05-31 · M8 部分完成 · slug 自动生成、meta 标题描述
- 2026-05-31 · M8 完成 · sitemap.xml、部署文档（Nginx配置/环境变量/问题排查）
- 2026-05-31 · 加分项 · 暗色主题切换（Tailwind dark mode + Alpine.js + localStorage）

---

## 15. 参考资料

- **DESIGN.md**：项目前端设计规范（Notion 风格设计系统，含色彩、字体、组件 token）
- Laravel 官方文档：https://laravel.com/docs/11.x
- Laravel 中文文档：https://learnku.com/docs/laravel/11.x
- Breeze 脚手架：https://laravel.com/docs/11.x/starter-kits#laravel-breeze
- Tailwind CSS：https://tailwindcss.com/docs
- Alpine.js：https://alpinejs.dev
- spatie/laravel-permission：https://spatie.be/docs/laravel-permission
- Filament：https://filamentphp.com（后期升级后台时参考）
- Conventional Commits：https://www.conventionalcommits.org
- dbdiagram.io：https://dbdiagram.io（数据库 ER 图）

---

> 维护提示：开发中如果有架构调整或新增依赖，请同步修改本文档对应章节，并在 [变更日志](#14-变更日志) 追加一条。
