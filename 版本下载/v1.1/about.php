<?php
require_once __DIR__ . '/config.php';

// 读取关于页面内容（如果存在）
$about_content = '';
$about_file = base_dir('data/about.json');
$default_about_file = base_dir('data/about_default.json');

// 检查是否有自定义的关于内容
if (file_exists($about_file)) {
    $about_data = json_read($about_file);
    $about_content = isset($about_data['content']) ? $about_data['content'] : '';
}

// 如果没有自定义内容，则加载默认内容
if (!$about_content && file_exists($default_about_file)) {
    $default_about_data = json_read($default_about_file);
    $about_content = isset($default_about_data['content']) ? $default_about_data['content'] : '';
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>关于 · <?= h($SITE_NAME) ?></title>
  <meta name="description" content="关于<?= h($SITE_NAME) ?>" />
  <link rel="icon" href="favicon.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/styles.css" />
  <script defer src="assets/script.js"></script>
  <?php
  // 读取美化设置
  $design_settings = read_design_settings();
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
  
  // 构建自定义CSS
  $custom_css = '';
  if (!empty($site_background)) {
      $custom_css .= "body { background-image: url('" . h($site_background) . "'); background-size: cover; background-attachment: fixed; background-repeat: no-repeat; }\n";
  }
  
  // 添加画中画虚化和背景模糊样式
  if (!empty($pic_blend_percentage) && $pic_blend_percentage > 0) {
      $blend_value = floatval($pic_blend_percentage) / 100;
      $custom_css .= ".card-body { background-color: rgba(255, 255, 255, " . $blend_value . "); }\n";
      $custom_css .= "html.dark .card-body { background-color: rgba(18, 18, 18, " . $blend_value . "); }\n";
  }
  
  if (!empty($pic_blur_percentage) && $pic_blur_percentage > 0) {
      $blur_value = floatval($pic_blur_percentage) / 10;
      $custom_css .= ".card { backdrop-filter: blur(" . $blur_value . "px); }\n";
  }
  
  // 添加首页横幅虚化和圆角样式
  if (!empty($hero_blend_percentage) && $hero_blend_percentage > 0) {
      $blend_value = floatval($hero_blend_percentage) / 100;
      $custom_css .= ".hero { background-color: rgba(255, 255, 255, " . $blend_value . ") !important; }\n";
      $custom_css .= "html.dark .hero { background-color: rgba(10, 11, 16, " . $blend_value . ") !important; }\n";
  }
  
  if (!empty($hero_border_radius) && $hero_border_radius > 0) {
      $custom_css .= ".hero { border-radius: " . intval($hero_border_radius) . "px; }\n";
  }
  
  // 添加顶部导航栏模糊、圆角、虚化和透明度样式
  if (!empty($site_header_blur) && $site_header_blur > 0) {
      $blur_value = floatval($site_header_blur);
      $custom_css .= ".site-header { backdrop-filter: blur(" . $blur_value . "px); }\n";
  }
  
  if (!empty($site_header_border_radius) && $site_header_border_radius > 0) {
      $custom_css .= ".site-header { border-radius: " . intval($site_header_border_radius) . "px; }\n";
  }
  
  if (!empty($site_header_blend) && $site_header_blend > 0) {
      $blend_value = floatval($site_header_blend) / 100;
      $custom_css .= ".site-header { background-color: rgba(255, 255, 255, " . $blend_value . ") !important; }\n";
      $custom_css .= "html.dark .site-header { background-color: rgba(18, 18, 18, " . $blend_value . ") !important; }\n";
  }
  
  if (!empty($site_header_opacity)) {
      $opacity_value = floatval($site_header_opacity) / 100;
      $custom_css .= ".site-header { opacity: " . $opacity_value . "; }\n";
  }
  
  if (!empty($design_settings['custom_css'])) {
      $custom_css .= $design_settings['custom_css'];
  }
  
  if (!empty($custom_css)) {
      echo "<style>\n" . $custom_css . "\n</style>\n";
  }
  if (!empty($design_settings['custom_html'])) {
      echo $design_settings['custom_html'] . "\n";
  }
  ?>
</head>
<body>
  <header class="site-header container">
    <div class="brand">
      <a class="logo" href="./"><?= h($SITE_NAME) ?></a>
      <span class="tagline"><?= h($SITE_TAGLINE) ?></span>
    </div>
    <nav class="nav">
      <a href="./" class="nav-link">首页</a>
      <a href="./#about" class="nav-link">关于</a>
      <a href="./#contact" class="nav-link">联系</a>
      <button id="themeToggle" class="btn ghost" title="切换深浅色">🌗</button>
    </nav>
  </header>

  <main class="container">
    <div class="card">
      <div class="card-body">
        <div class="about-header">
          <h1>关于我</h1>
          <p>写字与代码，是我表达世界的方式；追求极简与美感，是我对日常的坚持。</p>
        </div>
        
        <!-- 显示关于页面内容 -->
        <div class="about-content">
          <?php if ($about_content): ?>
            <?= $about_content ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>

  <footer class="site-footer container">
    <h3>关于我</h3>
    <p>我是 LynYie。写字与代码，是我表达世界的方式；追求极简与美感，是我对日常的坚持。</p>
    <div class="contact">
      <a class="btn ghost" href="mailto:hello@example.com">Email</a>
      <a class="btn ghost" href="https://github.com/" target="_blank" rel="noopener">GitHub</a>
      <a class="btn ghost" href="admin.php">发布文章</a>
    </div>
    <small class="copyright">© <?= date('Y') ?> <?= h($SITE_NAME) ?> · 原创</small>
  </footer>
  <?php
  // 添加自定义JavaScript
  $design_settings = read_design_settings();
  if (!empty($design_settings['custom_js'])) {
      echo "<script>\n" . $design_settings['custom_js'] . "\n</script>\n";
  }
  ?>
</body>
</html>