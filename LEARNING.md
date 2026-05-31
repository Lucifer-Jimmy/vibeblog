# LEARNING.md · 数据库初始化机制说明

## 问题：首次部署时，项目会自动初始化数据库吗？

**不会自动初始化。** 需要手动执行命令。

Laravel 的数据库初始化分为两步：
1. **迁移（Migration）** — 创建表结构
2. **填充（Seeding）** — 插入初始数据

---

## 首次部署需要执行的命令

```bash
# 第一步：创建数据库（MySQL 层面）
mysql -uroot -p -e "CREATE DATABASE myblog DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 第二步：运行迁移（创建所有表）
php artisan migrate

# 第三步（可选）：填充测试数据
php artisan migrate --seed    # 迁移 + 填充一起执行
# 或者
php artisan db:seed           # 单独填充（表已存在时）
```

> 生产环境通常只执行 `php artisan migrate --force`（不带 `--seed`），因为不需要假数据。

---

## 代码位置一览

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

---

## 执行流程图

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

---

## 开发环境 vs 生产环境的区别

| 场景 | 命令 | 说明 |
|------|------|------|
| 首次开发 | `php artisan migrate --seed` | 建表 + 填充假数据 |
| 开发中重置 | `php artisan migrate:fresh --seed` | 删除所有表重建 + 重新填充 |
| 生产首次部署 | `php artisan migrate --force` | 只建表，不填充假数据 |
| 生产后续更新 | `php artisan migrate --force` | 只执行新增的迁移 |
| 自动化测试 | PHPUnit + `RefreshDatabase` trait | 每个测试自动迁移+回滚，使用 myblog_test 库 |

---

## 关键概念

- **Migration 是幂等的**：Laravel 在 `migrations` 表中记录已执行的迁移，不会重复执行
- **Seeder 不是幂等的**：重复执行会插入重复数据，所以开发中用 `migrate:fresh --seed` 重置
- **Factory 是 Seeder 的工具**：Factory 定义"怎么生成一条假数据"，Seeder 决定"生成多少条、怎么关联"
- **`--force` 标志**：生产环境必须加此标志才能执行迁移（防止误操作）
