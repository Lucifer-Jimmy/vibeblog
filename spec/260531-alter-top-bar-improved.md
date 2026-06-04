# 260531-alter-top-bar · 导航栏重构与首页样式调整

## CONTEXT（背景）

当前导航栏直接陈列所有分类，标签多了会很臃肿。首页文章列表是三列卡片网格，信息密度高但阅读体验一般。暗色模式下页面跳转时存在白屏闪烁。

## REQUIREMENTS（需求）

### P0 · 导航栏重构

**现状**：导航栏展示所有分类链接（动态数量）
**目标**：改为固定四个板块 —— 首页 | 归档 | 分类 | 友链

- "首页"：链接到 `/`
- "归档"：链接到新页面 `/archives`
- "分类"：链接到新页面 `/categories`（展示所有分类列表）
- "友链"：链接到新页面 `/links`（静态页面，暂时展示占位内容即可）

### P0 · 首页文章列表样式调整

**现状**：三列卡片网格（grid-cols-3）
**目标**：改为单列、居中、圆角矩形卡片

- 一行一篇文章，卡片两侧留白（max-width 约 768px 居中）
- 按发布时间倒序
- 每页展示 5 篇
- 卡片内容保留：标题、摘要、分类标签、发布时间、作者

### P1 · 新增归档页面

路由：`GET /archives`

展示规则：
- 所有已发布文章，按发布时间倒序
- 按年份分组（如 "2026"、"2025"）
- 每篇文章只显示：月-日 + 文章标题（可点击跳转）+ 作者名
- 年份越新排越前

### P2 · 修复暗色模式翻页闪烁

**现象**：暗色模式下点击分页链接跳转时，页面会短暂显示白色背景再变暗
**原因**：`dark` class 由 Alpine.js 在 DOM ready 后才添加到 `<html>`，页面加载瞬间没有 dark class
**期望**：页面加载时无白屏闪烁，暗色模式应在 HTML 渲染前生效

## DESIGN（实现方向）

1. 导航栏：修改 `resources/views/layouts/navigation.blade.php`，移除动态分类循环，改为四个固定链接
2. 首页：修改 `resources/views/posts/index.blade.php`，grid 改为单列居中布局
3. 归档页：新建 `ArchiveController@index` + `resources/views/archives/index.blade.php`
4. 分类列表页：新建 `resources/views/categories/index.blade.php`（展示所有分类卡片）
5. 暗色闪烁：在 `<head>` 中加入内联 `<script>` 同步读取 localStorage 并设置 dark class（在 Alpine 初始化之前）

## RULES（约束）

1. 遵循现有 Tailwind + DESIGN.md 样式规范
2. 不做破坏性变更，不删除现有功能
3. 只做必要的最小化修改，不引入新依赖或未来功能
4. 新增路由需在 `routes/web.php` 中注册

## VERIFICATION（验收标准）

- [ ] 导航栏显示"首页 | 归档 | 分类 | 友链"四个链接，不再显示动态分类
- [ ] 首页文章列表为单列居中，每页 5 篇
- [ ] `/archives` 页面按年份分组展示文章
- [ ] `/categories` 页面展示所有分类
- [ ] `/links` 页面可访问（占位内容即可）
- [ ] 暗色模式下翻页无白屏闪烁
- [ ] 亮色模式功能不受影响
- [ ] `php artisan test` 全部通过
