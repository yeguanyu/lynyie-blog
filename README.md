# LynYie 博客系统

一个极致精美、专注体验的个人博客系统。

## 系统运行截图

https://1nvweb.top/homefunctions/storage/%E5%B1%8F%E5%B9%95%E6%88%AA%E5%9B%BE%202025-10-22%20142524.png
![图片描述](https://1nvweb.top/homefunctions/storage/%E5%B1%8F%E5%B9%95%E6%88%AA%E5%9B%BE%202025-10-22%20142524.png)
![图片描述](https://1nvweb.top/homefunctions/storage/屏幕截图 2025-10-22 143036.png)
![图片描述](https://1nvweb.top/homefunctions/storage/屏幕截图 2025-10-22 143051.png)

## 功能模块

### 前台功能
- 首页文章展示
- 文章详情页
- 关于页面
- 标签云展示
- 文章搜索
- 深浅主题切换

### 后台管理
- 文章管理（发布、编辑、删除）
- 标签管理
- 关于页面编辑
- 首页美化设置
- 附件管理
- 个人中心

## 技术架构

- **后端**：PHP + JSON数据存储（无数据库依赖）
- **前端**：HTML5 + CSS3 + JavaScript
- **样式**：自适应响应式设计
- **主题**：CSS变量实现深浅双主题

## 快速开始

1. 将所有文件上传到支持PHP的服务器
2. 确保`data`和`posts`目录有写入权限
3. 访问 `admin/login.php` 进入后台
4. 默认密码：`lynyie-admin-123`（请尽快修改）

## 目录结构

```
├── admin/          # 后台管理模块
├── assets/         # 静态资源（CSS、JS）
├── data/           # 数据存储目录
├── posts/          # 文章内容存储目录
├── uploads/        # 附件上传目录
├── index.php       # 首页
├── post.php        # 文章页
├── about.php       # 关于页
├── admin.php       # 后台入口
└── config.php      # 系统配置文件
```

## 安全提醒

请在正式部署前修改默认管理员密码！
