<?php
require_once __DIR__ . '/config.php';
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$posts = read_posts();
$post = find_post_by_slug($posts, $slug);
if (!$post) {
    http_response_code(404);
}
$content = read_post_content($slug);

// 增加浏览量
if ($post) {
    increment_post_views($slug);
    $views = get_post_views($slug);
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= h(isset($post['title']) ? $post['title'] : '未找到文章') ?> · <?= h($SITE_NAME) ?></title>
  <meta name="description" content="<?= h(isset($post['excerpt']) ? $post['excerpt'] : $SITE_DESCRIPTION) ?>" />
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
  $site_footer_content = isset($design_settings['site_footer_content']) ? $design_settings['site_footer_content'] : '';
  $site_footer_text_color = isset($design_settings['site_footer_text_color']) ? $design_settings['site_footer_text_color'] : '#000000';
  $site_footer_bg_color = isset($design_settings['site_footer_bg_color']) ? $design_settings['site_footer_bg_color'] : '#ffffff';
  $site_footer_bg_opacity = isset($design_settings['site_footer_bg_opacity']) ? $design_settings['site_footer_bg_opacity'] : '100';
  $site_footer_border_radius = isset($design_settings['site_footer_border_radius']) ? $design_settings['site_footer_border_radius'] : '0';
  $site_footer_blur = isset($design_settings['site_footer_blur']) ? $design_settings['site_footer_blur'] : '0';
  
  // 读取文章级别的overlay设置
  $overlay_settings = get_post_overlay_settings($slug);
  $overlay_enabled = isset($overlay_settings['overlay_enabled']) ? $overlay_settings['overlay_enabled'] : false;
  $overlay_blur = isset($overlay_settings['overlay_blur']) ? $overlay_settings['overlay_blur'] : '0';
  $overlay_opacity = isset($overlay_settings['overlay_opacity']) ? $overlay_settings['overlay_opacity'] : '0';
  $overlay_border_radius = isset($overlay_settings['overlay_border_radius']) ? $overlay_settings['overlay_border_radius'] : '0';
  $overlay_text_color = isset($overlay_settings['overlay_text_color']) ? $overlay_settings['overlay_text_color'] : '#ffffff';
  $overlay_bg_color = isset($overlay_settings['overlay_bg_color']) ? $overlay_settings['overlay_bg_color'] : '#000000';
  
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
  
  // 添加文章级别的画中画设置
  if ($overlay_enabled) {
      // 添加背景模糊样式
      if (!empty($overlay_blur) && $overlay_blur > 0) {
          $blur_value = floatval($overlay_blur);
          $custom_css .= ".post-hero { backdrop-filter: blur(" . $blur_value . "px); }\n";
      }
      
      // 添加背景透明度和颜色样式
      if (!empty($overlay_opacity)) {
          $opacity_value = floatval($overlay_opacity) / 100;
          // 解析十六进制颜色值
          if (strlen($overlay_bg_color) == 7 && $overlay_bg_color[0] == '#') {
              $r = hexdec(substr($overlay_bg_color, 1, 2));
              $g = hexdec(substr($overlay_bg_color, 3, 2));
              $b = hexdec(substr($overlay_bg_color, 5, 2));
              $custom_css .= ".post-hero .overlay { background-color: rgba(" . $r . ", " . $g . ", " . $b . ", " . $opacity_value . "); }\n";
          } else {
              $custom_css .= ".post-hero .overlay { background-color: " . $overlay_bg_color . "; opacity: " . $opacity_value . "; }\n";
          }
      }
      
      // 添加圆角样式
      if (!empty($overlay_border_radius) && $overlay_border_radius > 0) {
          $custom_css .= ".post-hero { border-radius: " . intval($overlay_border_radius) . "px; }\n";
      }
      
      // 添加文字颜色样式
      if (!empty($overlay_text_color) && $overlay_text_color !== '#ffffff') {
          $custom_css .= ".hero-inner { color: " . $overlay_text_color . "; }\n";
      }
  }
  
  // 添加首页横幅虚化和圆角样式
  if (!empty($hero_blend_percentage) && $hero_blend_percentage > 0) {
      $blend_value = floatval($hero_blend_percentage) / 100;
      $custom_css .= ".post-hero { background-color: rgba(255, 255, 255, " . $blend_value . ") !important; }\n";
      $custom_css .= "html.dark .post-hero { background-color: rgba(10, 11, 16, " . $blend_value . ") !important; }\n";
  }
  
  if (!empty($hero_border_radius) && $hero_border_radius > 0) {
      $custom_css .= ".post-hero { border-radius: " . intval($hero_border_radius) . "px; }\n";
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
  
  // 添加site-footer样式
  if (!empty($site_footer_text_color) && $site_footer_text_color !== '#000000') {
      $custom_css .= ".site-footer { color: " . $site_footer_text_color . "; }\n";
  }
  
  if (!empty($site_footer_bg_color) && $site_footer_bg_color !== '#ffffff') {
      // 计算背景颜色的RGBA值
      $opacity_value = floatval($site_footer_bg_opacity) / 100;
      // 解析十六进制颜色值
      if (strlen($site_footer_bg_color) == 7 && $site_footer_bg_color[0] == '#') {
          $r = hexdec(substr($site_footer_bg_color, 1, 2));
          $g = hexdec(substr($site_footer_bg_color, 3, 2));
          $b = hexdec(substr($site_footer_bg_color, 5, 2));
          $custom_css .= ".site-footer { background-color: rgba(" . $r . ", " . $g . ", " . $b . ", " . $opacity_value . "); }\n";
      } else {
          $custom_css .= ".site-footer { background-color: " . $site_footer_bg_color . "; }\n";
      }
  }
  
  // 添加site-footer圆角和模糊样式
  if (isset($site_footer_border_radius) && $site_footer_border_radius > 0) {
      $custom_css .= ".site-footer { border-radius: " . intval($site_footer_border_radius) . "px; }\n";
  }
  
  if (isset($site_footer_blur) && $site_footer_blur > 0) {
      $custom_css .= ".site-footer { backdrop-filter: blur(" . intval($site_footer_blur) . "px); }\n";
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
<body class="post-page">
  <header class="site-header container">
    <div class="brand">
      <a class="logo" href="./"><?= h($SITE_NAME) ?></a>
      <span class="tagline"><?= h($SITE_TAGLINE) ?></span>
    </div>
    <nav class="nav">
      <a href="./" class="nav-link">首页</a>
      <button id="themeToggle" class="btn ghost" title="切换深浅色">🌗</button>
    </nav>
  </header>

  <?php if ($post): ?>
  <section class="post-hero"<?= $overlay_enabled ? ' style="--cover:url(\''.h($post['cover']).'\')"' : '' ?>>
    <?php if ($overlay_enabled): ?>
    <div class="overlay"></div>
    <?php endif; ?>
    <div class="container hero-inner">
      <h1 class="post-title"><?= h($post['title']) ?></h1>
      <div class="post-meta">
        <time><?= format_date_cn(isset($post['date']) ? $post['date'] : '') ?></time>
        <div class="tags">
          <?php foreach ($post['tags'] as $t): 
            $tag_style = get_tag_style($t);
            ?>
            <span class="custom-tag" style="color: <?= h($tag_style['color']) ?>; background: <?= h($tag_style['bgcolor']) ?>; border: 1px solid <?= h($tag_style['border']) ?>;"><?= h($t) ?></span>
          <?php endforeach; ?>
        </div>
        <span class="post-views">浏览量: <?= h(isset($views) ? $views : 0) ?></span>
      </div>
    </div>
  </section>
  <main class="container prose">
    <?= $content ?: '<p class="empty">暂无内容。</p>' ?>
  </main>
  <?php else: ?>
  <main class="container">
    <div class="empty">抱歉，未找到该文章。</div>
  </main>
  <?php endif; ?>

  <footer class="site-footer container">
    <?php if (!empty($site_footer_content)): ?>
      <?= $site_footer_content ?>
    <?php else: ?>
    <h3>关于我</h3>
    <p>我是 LynYie。写字与代码，是我表达世界的方式；追求极简与美感，是我对日常的坚持。</p>
    <div class="contact">
      <a class="btn ghost" href="mailto:hello@example.com">Email</a>
      <a class="btn ghost" href="https://github.com/" target="_blank" rel="noopener">GitHub</a>
      <a class="btn ghost" href="admin.php">编辑与后台</a>
    </div>
    <small class="copyright">© <?= date('Y') ?> <?= h($SITE_NAME) ?> · 原创</small>
    <?php endif; ?>
  </footer>
  <!-- 设置悬浮球与面板 -->
  <div class="settings">
    <button class="fab" id="settingsFab" aria-label="打开设置">⚙️</button>
    <div class="settings-panel" id="settingsPanel" aria-hidden="true">
      <div class="backdrop" id="settingsBackdrop"></div>
      <div class="settings-window" role="dialog" aria-modal="true" aria-labelledby="settingsTitle">
        <div class="settings-header">
          <strong id="settingsTitle">外观设置</strong>
          <button class="close" id="settingsClose" aria-label="关闭">✕</button>
        </div>
        <div class="settings-body">
          <div class="setting-row">
            <span>主题</span>
            <div class="options">
              <label class="opt"><input type="radio" name="theme" value="dark"> 暗黑模式</label>
              <label class="opt"><input type="radio" name="theme" value="light"> 百日模式</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php
  // 添加自定义JavaScript
  $design_settings = read_design_settings();
  if (!empty($design_settings['custom_js'])) {
      echo "<script>\n" . $design_settings['custom_js'] . "\n</script>\n";
  }
  ?>
</body>
</html>