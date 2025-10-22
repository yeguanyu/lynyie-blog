<?php
require_once __DIR__ . '/../config.php';

// 检查管理员权限
if (!isset($_SESSION['admin_authed']) || !$_SESSION['admin_authed']) {
    header('Location: login.php');
    exit;
}

// 包含辅助函数
include __DIR__ . '/functions.php';

// 包含各功能模块
include __DIR__ . '/posts.php';

// 如果访问标签管理页面，包含标签管理模块
if (isset($_GET['section']) && $_GET['section'] === 'tags') {
    include __DIR__ . '/tags.php';
}

// 如果访问关于页面编辑，包含关于页面编辑模块
if (isset($_GET['section']) && $_GET['section'] === 'about') {
    include __DIR__ . '/about-editor.php';
}

// 如果访问美化设置页面，包含美化设置模块
if (isset($_GET['section']) && $_GET['section'] === 'design') {
    include __DIR__ . '/design.php';
}

// 如果访问附件管理页面，包含附件管理模块
if (isset($_GET['section']) && $_GET['section'] === 'attachments') {
    include __DIR__ . '/attachments.php';
}

$design_settings = read_design_settings();
$custom_css = isset($design_settings['custom_css']) ? $design_settings['custom_css'] : '';
$custom_js = isset($design_settings['custom_js']) ? $design_settings['custom_js'] : '';
$custom_html = isset($design_settings['custom_html']) ? $design_settings['custom_html'] : '';
$hero_background = isset($design_settings['hero_background']) ? $design_settings['hero_background'] : '';
$site_background = isset($design_settings['site_background']) ? $design_settings['site_background'] : '';
$pic_blend_percentage = isset($design_settings['pic_blend_percentage']) ? $design_settings['pic_blend_percentage'] : '0';
$pic_blur_percentage = isset($design_settings['pic_blur_percentage']) ? $design_settings['pic_blur_percentage'] : '0';
$hero_blend_percentage = isset($design_settings['hero_blend_percentage']) ? $design_settings['hero_blend_percentage'] : '0';
$hero_border_radius = isset($design_settings['hero_border_radius']) ? $design_settings['hero_border_radius'] : '0';
$site_header_blur = isset($design_settings['site_header_blur']) ? $design_settings['site_header_blur'] : '0';
$site_header_border_radius = isset($design_settings['site_header_border_radius']) ? $design_settings['site_header_border_radius'] : '0';
$site_header_blend = isset($design_settings['site_header_blend']) ? $design_settings['site_header_blend'] : '0';
$site_header_opacity = isset($design_settings['site_header_opacity']) ? $design_settings['site_header_opacity'] : '100';
$site_footer_content = isset($design_settings['site_footer_content']) ? $design_settings['site_footer_content'] : '';
$site_footer_text_color = isset($design_settings['site_footer_text_color']) ? $design_settings['site_footer_text_color'] : '#000000';
$site_footer_bg_color = isset($design_settings['site_footer_bg_color']) ? $design_settings['site_footer_bg_color'] : '#ffffff';
$site_footer_bg_opacity = isset($design_settings['site_footer_bg_opacity']) ? $design_settings['site_footer_bg_opacity'] : '100';
$site_footer_border_radius = isset($design_settings['site_footer_border_radius']) ? $design_settings['site_footer_border_radius'] : '0';
$site_footer_blur = isset($design_settings['site_footer_blur']) ? $design_settings['site_footer_blur'] : '0';
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>后台管理 · <?= h($SITE_NAME) ?></title>
  <link rel="icon" href="../favicon.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/styles.css" />
  <script defer src="../assets/script.js"></script>
  <style>
    .admin-layout {
      display: flex;
      min-height: calc(100vh - 100px);
      margin: 20px 0;
    }
    
    .sidebar {
      width: 250px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 20px 0;
      margin-right: 20px;
      height: fit-content;
      position: sticky;
      top: 123px;
    }
    
    .sidebar h3 {
      padding: 0 20px 10px;
      margin: 0;
      border-bottom: 1px solid var(--border);
      padding-bottom: 15px;
    }
    
    .sidebar-nav {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .sidebar-nav li {
      margin: 0;
    }
    
    .sidebar-nav a {
      display: block;
      padding: 12px 20px;
      color: var(--muted);
      text-decoration: none;
      font-weight: 600;
      border-left: 3px solid transparent;
      transition: all 0.2s ease;
    }
    
    .sidebar-nav a:hover,
    .sidebar-nav a.active {
      color: var(--text);
      background: rgba(255,255,255,0.06);
      border-left: 3px solid var(--primary);
    }
    
    .main-content {
      flex: 1;
    }
    
    @media (max-width: 768px) {
      .admin-layout {
        flex-direction: column;
      }
      
      .sidebar {
        width: 100%;
        margin-right: 0;
        margin-bottom: 20px;
        position: static;
      }
    }
  </style>
</head>
<body>
  <header class="site-header container">
    <div class="brand">
      <a class="logo" href="../"><?= h($SITE_NAME) ?></a>
      <span class="tagline">后台管理</span>
    </div>
    <nav class="nav">
      <a href="../" class="nav-link">首页</a>
      <button id="themeToggle" class="btn ghost">🌗</button>
    </nav>
  </header>

  <main class="container">
    <div class="admin-layout">
      <aside class="sidebar">
        <h3>管理面板</h3>
        <ul class="sidebar-nav">
          <li>
            <a href="javascript:void(0)" class="<?= (!isset($_GET['section']) || $_GET['section'] === 'posts') ? 'active' : '' ?>" onclick="togglePostsMenu(event)">文章管理 <span id="posts-menu-toggle">+</span></a>
            <ul id="posts-submenu" style="display: none; list-style: none; padding-left: 20px;">
              <li><a href="index.php?section=posts&subsection=new" class="<?= (isset($_GET['section']) && $_GET['section'] === 'posts' && isset($_GET['subsection']) && $_GET['subsection'] === 'new') ? 'active' : '' ?>">新文章发布</a></li>
              <li><a href="index.php?section=posts&subsection=existing" class="<?= (isset($_GET['section']) && $_GET['section'] === 'posts' && isset($_GET['subsection']) && $_GET['subsection'] === 'existing') ? 'active' : '' ?>">已有文章</a></li>
            </ul>
          </li>
          <li><a href="index.php?section=about" class="<?= (isset($_GET['section']) && $_GET['section'] === 'about') ? 'active' : '' ?>">关于页面</a></li>
          <li><a href="index.php?section=design" class="<?= (isset($_GET['section']) && $_GET['section'] === 'design') ? 'active' : '' ?>">首页美化</a></li>
          <li><a href="index.php?section=tags" class="<?= (isset($_GET['section']) && $_GET['section'] === 'tags') ? 'active' : '' ?>">标签管理</a></li>
          <li><a href="index.php?section=attachments" class="<?= (isset($_GET['section']) && $_GET['section'] === 'attachments') ? 'active' : '' ?>">附件管理</a></li>
          <li><a href="index.php?section=profile" class="<?= (isset($_GET['section']) && $_GET['section'] === 'profile') ? 'active' : '' ?>">个人中心</a></li>
        </ul>
      </aside>
      
      <div class="main-content">
      <?php if (!isset($_GET['section']) || $_GET['section'] === 'posts'): ?>
      <!-- 文章管理 -->
      <?php if (!isset($_GET['subsection']) || $_GET['subsection'] === 'new'): ?>
      <!-- 新文章发布 -->
      <form class="card form" method="post">
        <?php if ($editing_post): ?>
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="old_slug" value="<?= h($editing_post['slug']) ?>" />
        <h2>编辑文章</h2>
        <?php else: ?>
        <input type="hidden" name="action" value="create" />
        <h2>新文章</h2>
        <?php endif; ?>
        
        <?php if ($err): ?><div class="msg error"><?= h($err) ?></div><?php endif; ?>
        <?php if ($ok): ?>
          <div class="msg ok"><?= h($ok) ?> 
            <?php if (isset($posts[0])): ?>
              <a class="btn link" href="../post.php?slug=<?= h($posts[0]['slug']) ?>">查看 →</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        
        <label>标题
          <input type="text" name="title" placeholder="文章标题" value="<?= h(isset($editing_post['title']) ? $editing_post['title'] : '') ?>" required />
        </label>
        <label>别名（URL Slug，可留空自动生成）
          <input type="text" name="slug" placeholder="例如: design-notes" value="<?= h(isset($editing_post['slug']) ? $editing_post['slug'] : '') ?>" />
        </label>
        <label>日期
          <input type="date" name="date" value="<?= h(isset($editing_post['date']) ? $editing_post['date'] : date('Y-m-d')) ?>" />
        </label>
        <label>标签（逗号分隔）
          <input type="text" name="tags" placeholder="随笔, 设计" value="<?= h(implode(', ', isset($editing_post['tags']) ? $editing_post['tags'] : array())) ?>" />
        </label>
        <label>封面图片 URL（可留空使用默认）
          <input type="url" name="cover" placeholder="https://..." value="<?= h(isset($editing_post['cover']) ? $editing_post['cover'] : '') ?>" />
        </label>
        <label>摘要（可留空自动截取）
          <textarea name="excerpt" rows="2" placeholder="一句话摘要…"><?= h(isset($editing_post['excerpt']) ? $editing_post['excerpt'] : '') ?></textarea>
        </label>
        <label>正文（支持 HTML，换行自动保留）
          <textarea name="content" rows="12" placeholder="在此书写你的内容…"><?= h(isset($editing_post['slug']) ? read_post_content($editing_post['slug']) : '') ?></textarea>
        </label>
        
        <!-- 文章画中画设置区域 -->
        <?php if ($editing_post): 
          // 获取文章的overlay设置
          $overlay_settings = get_post_overlay_settings($editing_post['slug']);
          $overlay_enabled = isset($overlay_settings['overlay_enabled']) ? $overlay_settings['overlay_enabled'] : false;
          $overlay_blur = isset($overlay_settings['overlay_blur']) ? $overlay_settings['overlay_blur'] : '0';
          $overlay_opacity = isset($overlay_settings['overlay_opacity']) ? $overlay_settings['overlay_opacity'] : '0';
          $overlay_border_radius = isset($overlay_settings['overlay_border_radius']) ? $overlay_settings['overlay_border_radius'] : '0';
          $overlay_text_color = isset($overlay_settings['overlay_text_color']) ? $overlay_settings['overlay_text_color'] : '#ffffff';
          $overlay_bg_color = isset($overlay_settings['overlay_bg_color']) ? $overlay_settings['overlay_bg_color'] : '#000000';
        ?>
        <div class="card" style="margin-top: 20px;">
          <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleSection('postOverlaySettings')">
              <h3>文章画中画设置</h3>
              <span id="postOverlaySettingsToggle" style="font-size: 1.5em;">-</span>
            </div>
            <div id="postOverlaySettings">
              <label>
                <input type="checkbox" name="overlay_enabled" <?= $overlay_enabled ? 'checked' : '' ?> />
                启用画中画背景
                <small>启用后将在文章页面显示画中画背景效果</small>
              </label>
              
              <label>背景模糊程度
                <input type="range" name="overlay_blur" min="0" max="20" value="<?= h($overlay_blur) ?>" oninput="this.nextElementSibling.value = this.value + 'px'" />
                <output><?= h($overlay_blur) ?>px</output>
                <small>调整画中画背景模糊程度，0px为无模糊，20px为最大模糊</small>
              </label>
              
              <label>背景透明度
                <input type="range" name="overlay_opacity" min="0" max="100" value="<?= h($overlay_opacity) ?>" oninput="this.nextElementSibling.value = this.value + '%'" />
                <output><?= h($overlay_opacity) ?>%</output>
                <small>调整画中画背景透明度，0%为完全透明，100%为不透明</small>
              </label>
              
              <label>圆角大小
                <input type="range" name="overlay_border_radius" min="0" max="50" value="<?= h($overlay_border_radius) ?>" oninput="this.nextElementSibling.value = this.value + 'px'" />
                <output><?= h($overlay_border_radius) ?>px</output>
                <small>调整画中画圆角大小，0px为无圆角，50px为最大圆角</small>
              </label>
              
              <label>文字颜色
                <input type="color" name="overlay_text_color" value="<?= h($overlay_text_color) ?>" />
                <small>设置画中画区域文字的颜色</small>
              </label>
              
              <label>背景颜色
                <input type="color" name="overlay_bg_color" value="<?= h($overlay_bg_color) ?>" />
                <small>设置画中画区域背景的颜色</small>
              </label>
            </div>
          </div>
        </div>
        <?php endif; ?>
        
        <div class="actions">
          <?php if ($editing_post): ?>
            <button class="btn primary" type="submit">更新</button>
            <a class="btn ghost" href="index.php">取消</a>
          <?php else: ?>
            <button class="btn primary" type="submit">发布</button>
          <?php endif; ?>
          <a class="btn ghost" href="../">返回首页</a>
        </div>
      </form>
      <?php endif; ?>
      
      <?php if (!isset($_GET['subsection']) || $_GET['subsection'] === 'existing'): ?>
      <!-- 已有文章 -->
      <section class="card">
        <h3>已有文章（最新在前）</h3>
        <div class="grid article-list">
          <?php foreach ($posts as $p): ?>
            <article class="card article-item">
              <a class="cover" href="../post.php?slug=<?= h($p['slug']) ?>" style="--cover:url('<?= h(isset($p['cover']) ? $p['cover'] : '') ?>')"></a>
              <div class="card-body">
                <h3 class="card-title">
                  <a class="title" href="../post.php?slug=<?= h($p['slug']) ?>"><?= h($p['title']) ?></a>
                </h3>
                <div class="article-meta">
                  <span class="date"><?= format_date_cn(isset($p['date']) ? $p['date'] : '') ?></span>
                  <span class="post-views">浏览量: <?= h(get_post_views($p['slug'])) ?></span>
                </div>
                <div class="actions">
                  <a class="btn link" href="index.php?edit=<?= h($p['slug']) ?>">编辑</a>
                  <form method="post" style="display:inline;" onsubmit="return confirm('确定要删除这篇文章吗？')">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="slug" value="<?= h($p['slug']) ?>" />
                    <button class="btn link" type="submit">删除</button>
                  </form>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>
      <?php elseif (isset($_GET['section']) && $_GET['section'] === 'about'): ?>
      <!-- 关于页面编辑 -->
      <section class="card">
        <div class="card-body">
          <h2>编辑关于页面</h2>
          
          <?php 
          $action = isset($_POST['action']) ? $_POST['action'] : '';
          if ($err && ($action === 'update_about' || $action === 'save_default_about')): ?>
            <div class="msg error"><?= h($err) ?></div>
          <?php endif; ?>
          
          <?php if ($ok && ($action === 'update_about' || $action === 'save_default_about')): ?>
            <div class="msg ok"><?= h($ok) ?></div>
          <?php endif; ?>
          
          <form method="post">
            <input type="hidden" name="action" value="update_about" />
            <label>页面内容（支持 HTML）
              <textarea name="about_content" rows="10" placeholder="在此书写关于页面的内容…"><?= h($about_content) ?></textarea>
            </label>
            <div class="actions">
              <button class="btn primary" type="submit">更新关于页面</button>
              <button type="button" class="btn secondary" onclick="resetToDefault()">重置为默认</button>
              <a class="btn ghost" href="../about.php" target="_blank">预览</a>
            </div>
          </form>
          
          <form method="post" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
            <input type="hidden" name="action" value="save_default_about" />
            <label>默认关于页面内容（支持 HTML）
              <textarea name="content" rows="10" placeholder="在此输入默认关于页面的内容…"><?= h($default_about_content) ?></textarea>
            </label>
            <div class="actions">
              <button type="submit" class="btn primary">保存默认内容</button>
            </div>
          </form>
        </div>
      </section>
      <?php elseif (isset($_GET['section']) && $_GET['section'] === 'tags'): ?>
      <!-- 标签管理 -->
      <section class="card">
        <div class="card-body">
          <h2>标签管理</h2>
          
          <?php if ($err && ($action === 'create_tag' || $action === 'update_tag' || $action === 'delete_tag')): ?>
            <div class="msg error"><?= h($err) ?></div>
          <?php endif; ?>
          
          <?php if ($ok && ($action === 'create_tag' || $action === 'update_tag' || $action === 'delete_tag')): ?>
            <div class="msg ok"><?= h($ok) ?></div>
          <?php endif; ?>
          
          <form method="post" class="tag-form">
            <input type="hidden" name="action" value="<?= $editing_tag ? 'update_tag' : 'create_tag' ?>" />
            <?php if ($editing_tag): ?>
              <input type="hidden" name="old_name" value="<?= h($editing_tag['name']) ?>" />
            <?php endif; ?>
            
            <label>标签名称
              <input type="text" name="name" placeholder="标签名称" value="<?= h($editing_tag ? $editing_tag['name'] : '') ?>" required />
            </label>
            
            <label>文字颜色
              <input type="color" name="color" value="<?= h($editing_tag ? $editing_tag['color'] : '#7c5cff') ?>" />
            </label>
            
            <label>背景颜色
              <input type="color" name="bgcolor" value="<?= h($editing_tag ? $editing_tag['bgcolor'] : 'rgba(124,92,255,0.10)') ?>" />
            </label>
            
            <label>边框颜色
              <input type="color" name="border" value="<?= h($editing_tag ? $editing_tag['border'] : 'rgba(124,92,255,0.25)') ?>" />
            </label>
            
            <div class="actions">
              <button class="btn primary" type="submit"><?= $editing_tag ? '更新标签' : '创建标签' ?></button>
              <?php if ($editing_tag): ?>
                <a class="btn ghost" href="index.php?section=tags">取消</a>
              <?php endif; ?>
            </div>
          </form>
          
          <section class="card" style="margin-top: 30px;">
            <h3>现有标签</h3>
            <div class="list tag-list">
              <?php foreach ($tags as $tag): ?>
                <div class="list-item tag-item">
                  <div class="tag-info">
                    <span class="tag" style="color: <?= h($tag['color']) ?>; background: <?= h($tag['bgcolor']) ?>; border: 1px solid <?= h($tag['border']) ?>;"><?= h($tag['name']) ?></span>
                  </div>
                  <div class="actions">
                    <a class="btn link" href="index.php?section=tags&edit=<?= urlencode($tag['name']) ?>">编辑</a>
                    <form method="post" style="display:inline;" onsubmit="return confirm('确定要删除这个标签吗？')">
                      <input type="hidden" name="action" value="delete_tag" />
                      <input type="hidden" name="name" value="<?= h($tag['name']) ?>" />
                      <button class="btn link" type="submit">删除</button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        </div>
      </section>
      <?php elseif (isset($_GET['section']) && $_GET['section'] === 'design'): ?>
      <!-- 美化设置 -->
      <section class="card">
        <div class="card-body">
          <h2>美化设置</h2>
          
          <?php if ($err && $action === 'update_design'): ?>
            <div class="msg error"><?= h($err) ?></div>
          <?php endif; ?>
          
          <?php if ($ok && $action === 'update_design'): ?>
            <div class="msg ok"><?= h($ok) ?></div>
          <?php endif; ?>
          
          <form method="post">
            <input type="hidden" name="action" value="update_design" />
            
            <label>首页横幅背景图片 URL
              <input type="url" name="hero_background" placeholder="https://example.com/background.jpg" value="<?= h($hero_background) ?>" />
              <small>设置首页横幅区域的背景图片</small>
            </label>
            
            <label>网站背景图片 URL
              <input type="url" name="site_background" placeholder="https://example.com/site-background.jpg" value="<?= h($site_background) ?>" />
              <small>设置整个网站的背景图片</small>
            </label>
            
            <!-- 画中画设置区域 -->
            <div class="card" style="margin-top: 20px;">
              <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleSection('picSettings')">
                  <h3>文章画中画设置</h3>
                  <span id="picSettingsToggle" style="font-size: 1.5em;">-</span>
                </div>
                <div id="picSettings">
                  <label>画中画框虚化程度
                    <input type="range" name="pic_blend_percentage" min="0" max="100" value="<?= h($pic_blend_percentage) ?>" oninput="this.nextElementSibling.value = this.value + '%'" />
                    <output><?= h($pic_blend_percentage) ?>%</output>
                    <small>调整画中画框虚化程度，0%为无虚化，100%为完全虚化</small>
                  </label>
                  
                  <label>画中画背景模糊程度
                    <input type="range" name="pic_blur_percentage" min="0" max="100" value="<?= h($pic_blur_percentage) ?>" oninput="this.nextElementSibling.value = this.value + '%'" />
                    <output><?= h($pic_blur_percentage) ?>%</output>
                    <small>调整画中画背景模糊程度，0%为无模糊，100%为完全模糊</small>
                  </label>
                </div>
              </div>
            </div>
            
            <!-- 首页横幅设置区域 -->
            <div class="card" style="margin-top: 20px;">
              <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleSection('heroSettings')">
                  <h3>首页横幅设置</h3>
                  <span id="heroSettingsToggle" style="font-size: 1.5em;">-</span>
                </div>
                <div id="heroSettings">
                  <label>首页横幅虚化程度
                    <input type="range" name="hero_blend_percentage" min="0" max="100" value="<?= h($hero_blend_percentage) ?>" oninput="this.nextElementSibling.value = this.value + '%'" />
                    <output><?= h($hero_blend_percentage) ?>%</output>
                    <small>调整首页横幅虚化程度，0%为无虚化，100%为完全虚化</small>
                  </label>
                  
                  <label>首页横幅圆角大小
                    <input type="range" name="hero_border_radius" min="0" max="50" value="<?= h($hero_border_radius) ?>" oninput="this.nextElementSibling.value = this.value + 'px'" />
                    <output><?= h($hero_border_radius) ?>px</output>
                    <small>调整首页横幅圆角大小，0px为无圆角，50px为最大圆角</small>
                  </label>
                </div>
              </div>
            </div>
            
            <!-- 顶部导航栏设置区域 -->
            <div class="card" style="margin-top: 20px;">
              <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleSection('headerSettings')">
                  <h3>顶部导航栏设置</h3>
                  <span id="headerSettingsToggle" style="font-size: 1.5em;">-</span>
                </div>
                <div id="headerSettings">
                  <label>顶部导航栏背景模糊程度
                    <input type="range" name="site_header_blur" min="0" max="20" value="<?= h($site_header_blur) ?>" oninput="this.nextElementSibling.value = this.value + 'px'" />
                    <output><?= h($site_header_blur) ?>px</output>
                    <small>调整顶部导航栏背景模糊程度，0px为无模糊，20px为最大模糊</small>
                  </label>
                  
                  <label>顶部导航栏圆角大小
                    <input type="range" name="site_header_border_radius" min="0" max="20" value="<?= h($site_header_border_radius) ?>" oninput="this.nextElementSibling.value = this.value + 'px'" />
                    <output><?= h($site_header_border_radius) ?>px</output>
                    <small>调整顶部导航栏圆角大小，0px为无圆角，20px为最大圆角</small>
                  </label>
                  
                  <label>顶部导航栏背景虚化程度
                    <input type="range" name="site_header_blend" min="0" max="100" value="<?= h($site_header_blend) ?>" oninput="this.nextElementSibling.value = this.value + '%'" />
                    <output><?= h($site_header_blend) ?>%</output>
                    <small>调整顶部导航栏背景虚化程度，0%为无虚化，100%为完全虚化</small>
                  </label>
                  
                  <label>顶部导航栏背景透明度
                    <input type="range" name="site_header_opacity" min="0" max="100" value="<?= h($site_header_opacity) ?>" oninput="this.nextElementSibling.value = this.value + '%'" />
                    <output><?= h($site_header_opacity) ?>%</output>
                    <small>调整顶部导航栏背景透明度，0%为完全透明，100%为不透明</small>
                  </label>
                </div>
              </div>
            </div>
            
            <!-- Site Footer 设置区域 -->
            <div class="card" style="margin-top: 20px;">
              <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="toggleSection('footerSettings')">
                  <h3>Site Footer 设置</h3>
                  <span id="footerSettingsToggle" style="font-size: 1.5em;">-</span>
                </div>
                <div id="footerSettings">
                  <label>Footer 内容
                <textarea name="site_footer_content" rows="4" placeholder="在此输入网站底部内容"><?= h(!empty($site_footer_content) ? $site_footer_content : '<h3>关于我</h3>\n    <p>我是 LynYie。写字与代码，是我表达世界的方式；追求极简与美感，是我对日常的坚持。</p>\n    <div id=\"contact\" class=\"contact\">\n      <a class=\"btn ghost\" href=\"mailto:hello@example.com\">Email</a>\n      <a class=\"btn ghost\" href=\"https://github.com/\" target=\"_blank\" rel=\"noopener\">GitHub</a>\n      <a class=\"btn ghost\" href=\"admin.php\">编辑与后台</a>\n    </div>\n    <small class=\"copyright\">© <?= date("Y") ?> <?= h($SITE_NAME) ?> · 原创</small>') ?></textarea>
                <small>设置网站底部显示的内容，支持HTML标签</small>
              </label>
                  
                  <label>Footer 文字颜色
                    <input type="color" name="site_footer_text_color" value="<?= h($site_footer_text_color) ?>" />
                    <small>设置网站底部文字的颜色</small>
                  </label>
                  
                  <label>Footer 背景颜色
                    <input type="color" name="site_footer_bg_color" value="<?= h($site_footer_bg_color) ?>" />
                    <small>设置网站底部背景的颜色</small>
                  </label>
                  
                  <label>Footer 背景透明度
            <input type="range" name="site_footer_bg_opacity" min="0" max="100" value="<?= h($site_footer_bg_opacity) ?>" oninput="this.nextElementSibling.value = this.value + '%'" />
            <output><?= h($site_footer_bg_opacity) ?>%</output>
            <small>调整网站底部背景透明度，0%为完全透明，100%为不透明</small>
          </label>

          <label for="site_footer_border_radius">圆角大小 (px):
            <input type="number" id="site_footer_border_radius" name="site_footer_border_radius" min="0" max="50" value="<?= h($site_footer_border_radius) ?>">
          </label>

          <label for="site_footer_blur">背景模糊 (px):
            <input type="number" id="site_footer_blur" name="site_footer_blur" min="0" max="20" value="<?= h($site_footer_blur) ?>">
          </label>
                </div>
              </div>
            </div>
            
            <label>自定义 CSS
              <textarea name="custom_css" rows="6" placeholder="/* 在此处添加您的自定义 CSS */"><?= h($custom_css) ?></textarea>
            </label>
            <label>自定义 JavaScript
              <textarea name="custom_js" rows="6" placeholder="// 在此处添加您的自定义 JavaScript"><?= h($custom_js) ?></textarea>
            </label>
            <label>页眉自定义 HTML（如统计代码等）
              <textarea name="custom_html" rows="4" placeholder="<!-- 在此处添加页眉自定义 HTML -->"><?= h($custom_html) ?></textarea>
            </label>
            <div class="actions">
              <button class="btn primary" type="submit">保存设置</button>
              <button type="button" class="btn secondary" onclick="clearDesignSettings()">清空设置</button>
            </div>
          </form>
        </div>
      </section>
      <?php elseif (isset($_GET['section']) && $_GET['section'] === 'attachments'): ?>
      <?php include __DIR__ . '/attachments-ui.php'; ?>
      <?php elseif (isset($_GET['section']) && $_GET['section'] === 'profile'): ?>
      <?php include __DIR__ . '/profile.php'; ?>
      <?php endif; ?>
        
        <script>
        function clearDesignSettings() {
            if (confirm('确定要清空所有美化设置吗？这将删除您当前的所有自定义样式和脚本。')) {
                // 清空所有美化设置表单字段
                document.querySelector('textarea[name="custom_css"]').value = '';
                document.querySelector('textarea[name="custom_js"]').value = '';
                document.querySelector('textarea[name="custom_html"]').value = '';
            }
        }
        
        function resetToDefault() {
            if (confirm('确定要重置关于页面内容为默认内容吗？')) {
                // 从默认内容字段复制到主编辑字段
                const defaultContent = document.querySelector('textarea[name="content"]').value;
                document.querySelector('textarea[name="about_content"]').value = defaultContent;
            }
        }
        
        // 展开/收缩功能
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            const toggleIcon = document.getElementById(sectionId + 'Toggle');
            
            if (section.style.display === 'none') {
                section.style.display = 'block';
                toggleIcon.textContent = '-';
            } else {
                section.style.display = 'none';
                toggleIcon.textContent = '+';
            }
        }
        
        // 文章管理菜单展开/收缩功能
        function togglePostsMenu(event) {
            event.preventDefault();
            const submenu = document.getElementById('posts-submenu');
            const toggleIcon = document.getElementById('posts-menu-toggle');
            
            if (submenu.style.display === 'none') {
                submenu.style.display = 'block';
                toggleIcon.textContent = '-';
            } else {
                submenu.style.display = 'none';
                toggleIcon.textContent = '+';
            }
        }
        
        // 初始化时收缩所有区域
        document.addEventListener('DOMContentLoaded', function() {
            // 默认收缩所有区域
            const sections = ['picSettings', 'heroSettings', 'headerSettings', 'footerSettings', 'postOverlaySettings'];
            sections.forEach(function(sectionId) {
                const section = document.getElementById(sectionId);
                const toggleIcon = document.getElementById(sectionId + 'Toggle');
                
                if (section) {
                    section.style.display = 'none';
                    toggleIcon.textContent = '+';
                }
            });
            
            // 如果当前在文章管理页面，自动展开文章管理菜单
            const isPostsSection = !new URLSearchParams(window.location.search).get('section') || 
                                 new URLSearchParams(window.location.search).get('section') === 'posts';
            if (isPostsSection) {
                const submenu = document.getElementById('posts-submenu');
                const toggleIcon = document.getElementById('posts-menu-toggle');
                if (submenu) {
                    submenu.style.display = 'block';
                    toggleIcon.textContent = '-';
                }
                
                // 根据subsection参数自动滚动到相应区域
                const subsection = new URLSearchParams(window.location.search).get('subsection');
                if (subsection === 'existing') {
                    // 确保已有文章区域可见
                    setTimeout(() => {
                        const existingPostsSection = document.querySelector('section.card h3');
                        if (existingPostsSection) {
                            existingPostsSection.scrollIntoView({ behavior: 'smooth' });
                        }
                    }, 100);
                }
            }
        });
        </script>
  </main>
</body>
</html>