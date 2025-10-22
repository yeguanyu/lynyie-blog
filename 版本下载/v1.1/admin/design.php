<?php
require_once __DIR__ . '/../config.php';

// 检查管理员权限
if (!isset($_SESSION['admin_authed']) || !$_SESSION['admin_authed']) {
    header('Location: login.php');
    exit;
}

$err = '';
$ok = '';

// 读取现有的美化设置
$design_settings = array();
$design_file = base_dir('data/design.json');
if (file_exists($design_file)) {
  $design_settings = json_read($design_file);
}

// 设置默认值
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

// 处理美化设置更新
$action = isset($_POST['action']) ? $_POST['action'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_design') {
  $custom_css = isset($_POST['custom_css']) ? $_POST['custom_css'] : '';
  $custom_js = isset($_POST['custom_js']) ? $_POST['custom_js'] : '';
  $custom_html = isset($_POST['custom_html']) ? $_POST['custom_html'] : '';
  $hero_background = isset($_POST['hero_background']) ? $_POST['hero_background'] : '';
  $site_background = isset($_POST['site_background']) ? $_POST['site_background'] : '';
  $pic_blend_percentage = isset($_POST['pic_blend_percentage']) ? $_POST['pic_blend_percentage'] : '0';
  $pic_blur_percentage = isset($_POST['pic_blur_percentage']) ? $_POST['pic_blur_percentage'] : '0';
  $hero_blend_percentage = isset($_POST['hero_blend_percentage']) ? $_POST['hero_blend_percentage'] : '0';
  $hero_border_radius = isset($_POST['hero_border_radius']) ? $_POST['hero_border_radius'] : '0';
  $site_header_blur = isset($_POST['site_header_blur']) ? $_POST['site_header_blur'] : '0';
  $site_header_border_radius = isset($_POST['site_header_border_radius']) ? $_POST['site_header_border_radius'] : '0';
  $site_header_blend = isset($_POST['site_header_blend']) ? $_POST['site_header_blend'] : '0';
  $site_header_opacity = isset($_POST['site_header_opacity']) ? $_POST['site_header_opacity'] : '100';
  $site_footer_content = isset($_POST['site_footer_content']) ? $_POST['site_footer_content'] : '';
  $site_footer_text_color = isset($_POST['site_footer_text_color']) ? $_POST['site_footer_text_color'] : '#000000';
  $site_footer_bg_color = isset($_POST['site_footer_bg_color']) ? $_POST['site_footer_bg_color'] : '#ffffff';
  $site_footer_bg_opacity = isset($_POST['site_footer_bg_opacity']) ? $_POST['site_footer_bg_opacity'] : '100';
  $site_footer_border_radius = isset($_POST['site_footer_border_radius']) ? $_POST['site_footer_border_radius'] : '0';
  $site_footer_blur = isset($_POST['site_footer_blur']) ? $_POST['site_footer_blur'] : '0';
  
  $design_data = array(
    'custom_css' => $custom_css,
    'custom_js' => $custom_js,
    'custom_html' => $custom_html,
    'hero_background' => $hero_background,
    'site_background' => $site_background,
    'pic_blend_percentage' => $pic_blend_percentage,
    'pic_blur_percentage' => $pic_blur_percentage,
    'hero_blend_percentage' => $hero_blend_percentage,
    'hero_border_radius' => $hero_border_radius,
    'site_header_blur' => $site_header_blur,
    'site_header_border_radius' => $site_header_border_radius,
    'site_header_blend' => $site_header_blend,
    'site_header_opacity' => $site_header_opacity,
    'site_footer_content' => $site_footer_content,
    'site_footer_text_color' => $site_footer_text_color,
    'site_footer_bg_color' => $site_footer_bg_color,
    'site_footer_bg_opacity' => $site_footer_bg_opacity,
    'site_footer_border_radius' => $site_footer_border_radius,
    'site_footer_blur' => $site_footer_blur
  );
  
  if (json_write($design_file, $design_data)) {
      $ok = '美化设置已更新！';
      // 更新变量以便显示新内容
      $design_settings = $design_data;
      $custom_css = $design_data['custom_css'];
      $custom_js = $design_data['custom_js'];
      $custom_html = $design_data['custom_html'];
  } else {
      $err = '保存失败，请重试。';
  }
}
?>