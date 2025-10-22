<?php
require_once __DIR__ . '/../config.php';

// 检查管理员权限
if (!isset($_SESSION['admin_authed']) || !$_SESSION['admin_authed']) {
    header('Location: login.php');
    exit;
}

$err = '';
$ok = '';

// 处理密码修改请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    // 验证当前密码
    if ($current_password === '' || $new_password === '' || $confirm_password === '') {
        $err = '所有字段都是必填的。';
    } else if ($new_password !== $confirm_password) {
        $err = '新密码和确认密码不匹配。';
    } else if (strlen($new_password) < 6) {
        $err = '新密码长度至少为6个字符。';
    } else if ($current_password === $new_password) {
        $err = '新密码不能与当前密码相同。';
    } else if ($current_password !== $ADMIN_PASSWORD) {
        $err = '当前密码不正确。';
    } else {
        // 这里我们无法直接修改配置文件中的密码，因为它是硬编码的
        // 在实际应用中，应该将密码存储在数据库或专门的配置文件中
        $ok = '密码修改成功！（注意：此演示版本不支持直接修改配置文件中的密码）';
        // 如果要实现真实的功能，需要将密码存储在单独的安全文件中，并在这里进行修改
    }
}

// 处理退出登录请求
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // 清除会话
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    // 重定向到登录页面
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>个人中心 · <?= h($SITE_NAME) ?> 后台管理</title>
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
        <section class="card">
          <div class="card-body">
            <h2>个人中心</h2>
            
            <?php if ($err): ?>
              <div class="msg error"><?= h($err) ?></div>
            <?php endif; ?>
            
            <?php if ($ok): ?>
              <div class="msg ok"><?= h($ok) ?></div>
            <?php endif; ?>
            
            <!-- 修改密码表单 -->
            <div class="card" style="margin-bottom: 30px;">
              <div class="card-body">
                <h3>修改密码</h3>
                <form method="post">
                  <input type="hidden" name="action" value="change_password" />
                  <label>当前密码
                    <input type="password" name="current_password" placeholder="请输入当前密码" />
                  </label>
                  <label>新密码
                    <input type="password" name="new_password" placeholder="请输入新密码（至少6个字符）" />
                  </label>
                  <label>确认新密码
                    <input type="password" name="confirm_password" placeholder="请再次输入新密码" />
                  </label>
                  <div class="actions">
                    <button class="btn primary" type="submit">修改密码</button>
                  </div>
                </form>
              </div>
            </div>
            
            <!-- 退出登录 -->
            <div class="card">
              <div class="card-body">
                <h3>退出登录</h3>
                <p>点击下面的按钮安全退出管理系统。</p>
                <div class="actions">
                  <a class="btn secondary" href="index.php?section=profile&action=logout" onclick="return confirm('确定要退出登录吗？')">退出登录</a>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </main>
</body>
</html>