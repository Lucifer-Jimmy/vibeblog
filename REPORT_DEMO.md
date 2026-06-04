# MyBlog 个人博客系统 · 课程项目报告

---

## 1. 项目目的

本项目是一个基于 Laravel + MySQL 实现的**个人博客系统**，旨在为用户提供文章发布、分类管理、标签归档、评论互动等完整的博客功能。

系统面向三类用户：
- **访客**：浏览文章、按分类/标签筛选、全文搜索、查看归档
- **注册用户**：发布和管理自己的文章、发表评论
- **管理员**：后台管理所有文章、分类、评论、友链，控制站点设置（如注册开关）

项目采用前后端一体的 MVC 架构，前端使用 Tailwind CSS 实现 Notion 风格的现代化 UI，支持亮色/暗色主题切换，后端基于 Laravel 框架提供完整的认证、授权、CRUD 功能。

---

## 2. 项目内容和分工情况

本项目为三人小组协作完成，分工如下：

| 成员 | 负责模块 | 主要工作内容 |
|------|----------|-------------|
| 成员 A | 数据库设计 | 数据库表结构设计、迁移文件编写、模型关联定义、数据填充 |
| 成员 B | 后端实现 | Laravel 路由、控制器、中间件、表单验证、业务逻辑 |
| 成员 C | 前端实现 | Blade 模板、Tailwind 样式、Alpine.js 交互、暗色主题、响应式布局 |

---

## 3. 项目过程

### 3.1 数据库设计与实现

#### 3.1.1 数据库概览

系统使用 MySQL 8.0，共设计 13 张表：

| 表名 | 用途 |
|------|------|
| users | 用户表（含角色字段） |
| posts | 文章表（支持软删除） |
| categories | 分类表 |
| tags | 标签表 |
| post_tag | 文章-标签多对多中间表 |
| comments | 评论表（支持楼中楼） |
| links | 友链表 |
| settings | 站点配置表（key-value） |
| cache / cache_locks | 缓存表 |
| sessions | 会话表 |
| password_reset_tokens | 密码重置令牌 |
| migrations | 迁移记录表 |

#### 3.1.2 核心表结构设计

**用户表（users）**

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name', 50);
    $table->string('email', 100)->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('avatar', 255)->nullable();
    $table->enum('role', ['admin', 'author', 'user'])->default('user');
    $table->rememberToken();
    $table->timestamps();
});
```

通过 `role` 枚举字段区分管理员、作者和普通用户，避免引入复杂的权限包。

**文章表（posts）**

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $table->string('title', 150);
    $table->string('slug', 180)->unique();
    $table->string('excerpt', 300)->nullable();
    $table->longText('content');
    $table->string('cover', 255)->nullable();
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->unsignedInteger('views')->default(0);
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['status', 'published_at']);
});
```

设计要点：
- 使用 `slug` 字段生成 SEO 友好的 URL
- `status` 支持草稿/发布状态切换
- `softDeletes` 实现软删除，防止误删
- 复合索引 `(status, published_at)` 优化首页查询性能
- 外键约束保证数据完整性

**评论表（comments）**

```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
    $table->text('content');
    $table->enum('status', ['visible', 'hidden'])->default('visible');
    $table->timestamps();
});
```

通过 `parent_id` 自引用外键实现楼中楼回复功能。

**全文搜索索引**

```php
// 使用原生 SQL 添加 FULLTEXT 索引
DB::statement('ALTER TABLE posts ADD FULLTEXT INDEX posts_fulltext (title, content)');
```

#### 3.1.3 模型关系

```
User    1───*  Post        （一个用户有多篇文章）
User    1───*  Comment     （一个用户有多条评论）
Post    *───1  Category    （多篇文章属于一个分类）
Post    *───*  Tag         （多对多，通过 post_tag 中间表）
Post    1───*  Comment     （一篇文章有多条评论）
Comment *───1  Comment     （楼中楼：评论可以回复评论）
```

代表性模型代码（Post 模型）：

```php
class Post extends Model
{
    use HasFactory, SoftDeletes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // 查询作用域：只查已发布文章
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
```

#### 3.1.4 数据填充

使用 Laravel Factory + Seeder 生成测试数据：

```php
// DatabaseSeeder.php
public function run(): void
{
    User::factory()->create([
        'name' => 'Admin',
        'email' => 'admin@myblog.test',
        'role' => 'admin',
    ]);

    User::factory()->create([
        'name' => 'User',
        'email' => 'user@myblog.test',
        'role' => 'user',
    ]);

    $this->call([PostSeeder::class]);
}

// PostSeeder.php - 创建 6 个分类、10 个标签、35 篇文章
Post::factory(35)
    ->recycle($users)
    ->recycle($categories)
    ->create()
    ->each(function (Post $post) use ($tags) {
        $post->tags()->attach(
            $tags->random(rand(1, 4))->pluck('id')->toArray()
        );
    });
```

---

### 3.2 后端实现

#### 3.2.1 技术架构

后端基于 Laravel 13 框架，采用 MVC 架构：

- **路由层**（routes/web.php）：定义 URL 与控制器的映射
- **控制器层**（app/Http/Controllers/）：处理请求逻辑
- **模型层**（app/Models/）：数据库交互与业务规则
- **中间件层**（app/Http/Middleware/）：请求过滤与权限控制
- **表单请求**（app/Http/Requests/）：输入验证

#### 3.2.2 路由设计

系统路由分为三组：



```php
// 1. 前台公开路由（无需登录）
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/archives', [ArchiveController::class, 'index'])->name('archives');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// 2. 用户路由（需要登录）
Route::middleware('auth')->group(function () {
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::get('/my/posts', [MyPostController::class, 'index']);
    Route::get('/my/posts/create', [MyPostController::class, 'create']);
    Route::post('/my/posts', [MyPostController::class, 'store']);
    // ...
});

// 3. 后台管理路由（需要登录 + 管理员身份）
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index']);
    Route::get('/settings', [Admin\SettingController::class, 'index']);
    // ...
});
```

使用 `{post:slug}` 路由模型绑定，通过 slug 字段解析文章，实现 SEO 友好的 URL。

#### 3.2.3 控制器实现

**文章详情控制器**（含 Markdown 渲染和浏览量统计）：

```php
public function show(Post $post)
{
    if ($post->status !== 'published' && $post->user_id !== auth()->id()) {
        abort(404);
    }

    $post->load(['user', 'category', 'tags', 'comments' => function ($query) {
        $query->visible()->whereNull('parent_id')
              ->with(['user', 'replies.user'])->latest();
    }]);

    $post->increment('views');

    // GFM Markdown 渲染
    $environment = new Environment([
        'html_input' => 'strip',
        'allow_unsafe_links' => false,
    ]);
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new GithubFlavoredMarkdownExtension());

    $converter = new MarkdownConverter($environment);
    $htmlContent = $converter->convert($post->content)->getContent();

    return view('posts.show', compact('post', 'htmlContent'));
}
```

**全文搜索控制器**：

```php
public function index(Request $request)
{
    $query = $request->input('q', '');
    $posts = collect();

    if (strlen($query) >= 2) {
        $posts = Post::published()
            ->whereRaw('MATCH(title, content) AGAINST(? IN BOOLEAN MODE)', [$query . '*'])
            ->orderByDesc('published_at')
            ->paginate(10)
            ->appends(['q' => $query]);
    }

    return view('search.index', compact('posts', 'query'));
}
```

#### 3.2.4 中间件

**管理员权限中间件**：

```php
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            abort(403, '无权访问后台');
        }
        return $next($request);
    }
}
```

**注册开关中间件**：

```php
class RegistrationEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (setting('registration_enabled', 'true') !== 'true') {
            abort(403, '注册功能已关闭');
        }
        return $next($request);
    }
}
```

#### 3.2.5 表单验证

使用 FormRequest 类集中管理验证规则：

```php
class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:150',
            'category_id' => 'nullable|exists:categories,id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:300',
            'status' => 'required|in:draft,published',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ];
    }
}
```

#### 3.2.6 站点设置系统

使用 key-value 模式的 Settings 表实现灵活的站点配置：

```php
class Setting extends Model
{
    protected $primaryKey = 'key';
    public $timestamps = false;

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::find($key)?->value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
```

配合全局辅助函数 `setting()` 在任意位置读取配置。

---

### 3.3 前端实现

#### 3.3.1 技术选型

| 技术 | 用途 |
|------|------|
| Blade | Laravel 模板引擎 |
| Tailwind CSS 3 | 原子化 CSS 框架 |
| Alpine.js | 轻量级 JavaScript 交互 |
| Vite | 前端构建工具 |
| @tailwindcss/typography | Markdown 内容排版 |
| @tailwindcss/forms | 表单样式美化 |

#### 3.3.2 设计系统

基于 Notion 风格定义了完整的设计 token：

```javascript
// tailwind.config.js
export default {
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: { sans: ['Inter', ...defaultTheme.fontFamily.sans] },
            colors: {
                primary: { DEFAULT: '#5645d4', pressed: '#4534b3' },
                navy: { DEFAULT: '#0a1530' },
                ink: { DEFAULT: '#1a1a1a' },
                charcoal: '#37352f',
                steel: '#787671',
                surface: { DEFAULT: '#f6f5f4', soft: '#fafaf9' },
                hairline: { DEFAULT: '#e5e3df' },
            },
            borderRadius: { btn: '8px', card: '12px' },
        },
    },
    plugins: [forms, typography],
};
```

#### 3.3.3 布局系统

主布局采用 flex 列布局确保页脚始终贴底：

```html
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col bg-surface-soft dark:bg-gray-900">
        @include('layouts.navigation')
        <main class="flex-1">{{ $slot }}</main>
        <footer class="bg-white dark:bg-gray-800 border-t border-hairline">
            <!-- footer content -->
        </footer>
    </div>
</body>
```

#### 3.3.4 暗色主题实现

采用 Tailwind `dark` class 策略 + Alpine.js + localStorage 三层配合：

**第一层：防闪烁内联脚本**（在 CSS 加载前同步执行）

```html
<head>
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
```

**第二层：Alpine.js 响应式状态管理**

```html
<html x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
```

**第三层：切换按钮**（太阳/月亮图标）

```html
<button @click="darkMode = !darkMode">
    <svg x-show="darkMode"><!-- 太阳图标 --></svg>
    <svg x-show="!darkMode"><!-- 月亮图标 --></svg>
</button>
```

#### 3.3.5 文章卡片组件

使用 Blade 组件实现可复用的文章卡片：

```html
@props(['post'])
<article class="bg-white dark:bg-gray-800 rounded-card border border-hairline
               dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
    @if($post->cover)
        <img src="{{ asset('storage/' . $post->cover) }}" class="w-full h-48 object-cover">
    @endif
    <div class="p-6">
        <h2 class="text-lg font-semibold text-ink dark:text-gray-100 line-clamp-2">
            <a href="{{ route('posts.show', $post) }}" class="hover:text-primary">
                {{ $post->title }}
            </a>
        </h2>
    </div>
</article>
```

#### 3.3.6 打字机效果

首页标语使用 Alpine.js 实现打字机动画：

```javascript
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
```

配合 CSS `step-end` 动画实现光标闪烁：

```css
.animate-blink {
    animation: blink 1s step-end infinite;
}
@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}
```

#### 3.3.7 后台管理界面

后台采用经典的左侧深色导航 + 右侧内容区布局：

```html
<div class="min-h-screen flex">
    <aside class="w-64 bg-navy text-white">
        <nav class="px-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}">仪表盘</a>
            <a href="{{ route('admin.posts.index') }}">文章管理</a>
            <a href="{{ route('admin.categories.index') }}">分类管理</a>
            <a href="{{ route('admin.comments.index') }}">评论审核</a>
            <a href="{{ route('admin.links.index') }}">友链管理</a>
            <a href="{{ route('admin.settings.index') }}">站点设置</a>
        </nav>
    </aside>
    <div class="flex-1 p-6 bg-surface-soft">{{ $slot }}</div>
</div>
```

---

## 4. 项目总结

### 4.1 项目成果

本项目成功实现了一个功能完整的个人博客系统，涵盖以下核心功能：

- ✅ 用户认证（注册、登录、注销、密码重置）
- ✅ 文章管理（CRUD、草稿/发布、封面图上传、GFM Markdown 渲染）
- ✅ 分类与标签（多对多关联、按分类/标签筛选）
- ✅ 评论系统（登录评论、楼中楼回复、管理员审核）
- ✅ 全文搜索（MySQL FULLTEXT 索引）
- ✅ 归档页面（按年份分组、时间线展示、分页）
- ✅ 友链管理（前台展示 + 后台 CRUD）
- ✅ 后台管理面板（仪表盘、文章/分类/评论/友链/设置管理）
- ✅ 暗色主题（无闪烁切换、localStorage 持久化）
- ✅ SEO 优化（slug URL、meta 标签、sitemap.xml）
- ✅ 站点设置（注册开关等可扩展配置）
- ✅ 响应式设计（移动端适配）
- ✅ 自动化测试（35 个测试用例全部通过）

### 4.2 技术栈总结

| 层级 | 技术 |
|------|------|
| 后端框架 | Laravel 13 (PHP 8.5) |
| 数据库 | MySQL 8.0 (Docker) |
| 前端样式 | Tailwind CSS 3 |
| 前端交互 | Alpine.js |
| 模板引擎 | Blade |
| 构建工具 | Vite |
| Markdown | league/commonmark 2.x (GFM) |
| 认证 | Laravel Breeze |
| 测试 | PHPUnit (35 个测试用例) |

### 4.3 学习收获

1. **MVC 架构实践**：通过 Laravel 框架深入理解了模型-视图-控制器的分层思想
2. **数据库设计**：学会了外键约束、索引优化、多对多关系的实际应用
3. **前端工程化**：掌握了 Tailwind 原子化 CSS、组件化开发、暗色主题适配
4. **安全意识**：实践了 CSRF 防护、XSS 防御、SQL 注入防护、权限控制
5. **软件工程规范**：Git 版本控制、自动化测试、文档维护的重要性

### 4.4 不足与展望

- 可引入 Redis 缓存优化高频查询性能
- 评论可增加邮件通知功能
- 可开发 API 接口供移动端使用
- 可引入 CI/CD 实现自动化部署
