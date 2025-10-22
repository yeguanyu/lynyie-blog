<?php
require_once __DIR__ . '/../config.php';

// 检查管理员权限
if (!isset($_SESSION['admin_authed']) || !$_SESSION['admin_authed']) {
    header('Location: login.php');
    exit;
}

$err = '';
$ok = '';

// 读取现有的关于页面内容
$about_content = '';
$about_file = base_dir('data/about.json');
if (file_exists($about_file)) {
  $about_data = json_read($about_file);
  $about_content = isset($about_data['content']) ? $about_data['content'] : '';
}

// 读取默认的关于页面内容
$default_about_content = '';
$default_about_file = base_dir('data/about_default.json');
if (file_exists($default_about_file)) {
  $default_about_data = json_read($default_about_file);
  $default_about_content = isset($default_about_data['content']) ? $default_about_data['content'] : '';
}

// 处理关于页面更新
$action = isset($_POST['action']) ? $_POST['action'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_about') {
  $content = isset($_POST['about_content']) ? $_POST['about_content'] : '';
  $about_data = array('content' => $content);
  if (json_write($about_file, $about_data)) {
      $ok = '关于页面已更新！';
      $about_content = $content;
  } else {
      $err = '保存失败，请重试。';
  }
}

// 处理默认关于页面更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save_default_about') {
  $content = isset($_POST['content']) ? $_POST['content'] : '';
  $default_about_data = array('content' => $content);
  if (json_write($default_about_file, $default_about_data)) {
      $ok = '默认关于页面已更新！';
      $default_about_content = $content;
  } else {
      $err = '保存失败，请重试。';
  }
}
?>