<?php
require_once __DIR__ . '/config.php';
$posts = read_posts();
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$tag = isset($_GET['tag']) ? trim($_GET['tag']) : '';

// 标签筛选
if ($tag !== '') {
    $posts = array_values(array_filter($posts, function($p) use ($tag) {
        $tags = isset($p['tags']) ? $p['tags'] : array();
        return in_array($tag, $tags);
    }));
}
// 关键词搜索
else if ($q !== '') {
    $needle = mb_strtolower($q);
    $posts = array_values(array_filter($posts, function($p) use ($needle) {
        $title = isset($p['title']) ? $p['title'] : '';
        $excerpt = isset($p['excerpt']) ? $p['excerpt'] : '';
        $tags = isset($p['tags']) ? $p['tags'] : array();
        $hay = mb_strtolower($title . ' ' . $excerpt . ' ' . implode(' ', $tags));
        return strpos($hay, $needle) !== false;
    }));
}
// 按日期倒序
usort($posts, function($a, $b){
    $date_a = isset($a['date']) ? $a['date'] : '1970-01-01';
    $date_b = isset($b['date']) ? $b['date'] : '1970-01-01';
    $ts_a = strtotime($date_a);
    $ts_b = strtotime($date_b);
    return $ts_b - $ts_a;
});
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= h($SITE_NAME) ?> · <?= h($SITE_TAGLINE) ?></title>
  <meta name="description" content="<?= h($SITE_DESCRIPTION) ?>" />
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
<body>
  <header class="site-header container">
    <div class="brand">
      <a class="logo" href="./"><?= h($SITE_NAME) ?></a>
      <span class="tagline"><?= h($SITE_TAGLINE) ?></span>
    </div>
    <nav class="nav">
      <a href="./" class="nav-link active">首页</a>
      <a href="about.php" class="nav-link">关于</a>
      <a href="#contact" class="nav-link">联系</a>
      <button id="themeToggle" class="btn ghost" title="切换深浅色">🌗</button>
    </nav>
  </header>

  <section class="hero container"<?= !empty($hero_background) ? ' style="background-image: url(\''.h($hero_background).'\');"' : '' ?>>
    <div class="hero-text">
      <h1 class="title"><?= h($SITE_NAME) ?></h1>
      <p class="subtitle"><?= h($SITE_TAGLINE) ?></p>
    </div>
    <form action="" method="get" class="search">
      <input type="search" name="q" value="<?= h($q) ?>" placeholder="搜索文章与标签…" />
      <button class="btn primary" type="submit">搜索</button>
    </form>
    
    <?php
    // 收集所有标签
    $all_tags = array();
    foreach ($posts as $p) {
        $tags = isset($p['tags']) ? $p['tags'] : array();
        foreach ($tags as $t) {
            if (!in_array($t, $all_tags)) {
                $all_tags[] = $t;
            }
        }
    }
    ?>
    
    <?php if (!empty($all_tags)): ?>
    <div class="tags-cloud">
      <span>热门标签：</span>
      <?php foreach ($all_tags as $t): ?>
        <a class="tag<?= ($tag === $t) ? ' active' : '' ?>" href="?tag=<?= h($t) ?>"><?= h($t) ?></a>
      <?php endforeach; ?>
      <?php if ($tag !== '' || $q !== ''): ?>
        <a class="tag" href="./">清除筛选</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </section>

  <main class="container">
    <div class="grid">
      <?php if (empty($posts)): ?>
        <div class="empty">未找到匹配的内容。试试不同关键词，或先发布一篇文章。</div>
      <?php else: foreach ($posts as $p): ?>
        <article class="card">
          <a class="cover" href="post.php?slug=<?= h($p['slug']) ?>" style="--cover:url('<?= h($p['cover']) ?>')"></a>
          <div class="card-body">
            <div class="meta">
              <time><?= format_date_cn(isset($p['date']) ? $p['date'] : '') ?></time>
              <div class="tags">
                <?php 
                $tags = isset($p['tags']) ? $p['tags'] : array();
                foreach ($tags as $t): 
                  $tag_style = get_tag_style($t);
                  ?>
                  <span class="custom-tag" style="color: <?= h($tag_style['color']) ?>; background: <?= h($tag_style['bgcolor']) ?>; border: 1px solid <?= h($tag_style['border']) ?>;"><?= h($t) ?></span>
                <?php endforeach; ?>
              </div>
              <span class="post-views">浏览量: <?= h(get_post_views($p['slug'])) ?></span>
            </div>
            <h2 class="card-title">
              <a href="post.php?slug=<?= h($p['slug']) ?>"><?= h($p['title']) ?></a>
            </h2>
            <p class="excerpt"><?= h($p['excerpt']) ?></p>
            <div class="actions">
              <a class="btn link" href="post.php?slug=<?= h($p['slug']) ?>">阅读全文 →</a>
            </div>
          </div>
        </article>
      <?php endforeach; endif; ?>
    </div>
  </main>

  <footer class="site-footer container" id="about">
    <?php if (!empty($site_footer_content)): ?>
      <?= $site_footer_content ?>
    <?php else: ?>
    <h3>关于我</h3>
    <p>我是 LynYie。写字与代码，是我表达世界的方式；追求极简与美感，是我对日常的坚持。</p>
    <div id="contact" class="contact">
      <a class="btn ghost" href="mailto:hello@example.com">Email</a>
      <a class="btn ghost" href="https://github.com/" target="_blank" rel="noopener">GitHub</a>
      <a class="btn ghost" href="admin.php">编辑与后台</a>
    </div>
    <small class="copyright">© <?= date('Y') ?> <?= h($SITE_NAME) ?> · 原创 By LynYie</small>
    <?php endif; ?>
  </footer>
  <!-- 设置悬浮球与面板 -->
  <div class="settings">
    <button class="fab" id="settingsFab" aria-label="打开设置">⚙️</button>
    <div class="settings-panel" id="settingsPanel" aria-hidden="true">
      <div class="backdrop" id="settingsBackdrop"></div>
      
  <?php
  // 添加自定义JavaScript
  $design_settings = read_design_settings();
  if (!empty($design_settings['custom_js'])) {
      echo "<script>\n" . $design_settings['custom_js'] . "\n</script>\n";
  }
  ?>
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
</body>
</html>