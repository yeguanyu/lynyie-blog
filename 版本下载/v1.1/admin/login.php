<?php
require_once __DIR__ . '/../config.php';

$err = '';

// 登录
if (!isset($_SESSION['admin_authed']) || !$_SESSION['admin_authed']) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pwd = isset($_POST['password']) ? $_POST['password'] : '';
        if ($pwd === $ADMIN_PASSWORD) {
            $_SESSION['admin_authed'] = true;
            header('Location: index.php');
            exit;
        } else {
            $err = '密码错误，请重试。';
        }
    }
    ?>
    <!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>后台登录 · <?= h($SITE_NAME) ?></title>
  <link rel="icon" href="../favicon.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/styles.css" />
  <script defer src="../assets/script.js"></script>
</head>
    <body>
      <header class="site-header container">
        <div class="brand">
          <a class="logo" href="../"><?= h($SITE_NAME) ?></a>
          <span class="tagline">后台登录</span>
        </div>
      </header>
      <main class="container">
        <form class="card form" method="post">
          <h2>输入后台密码</h2>
          <?php if ($err): ?><div class="msg error"><?= h($err) ?></div><?php endif; ?>
          <label>密码
            <input type="password" name="password" placeholder="请输入后台密码" required />
          </label>
          <div class="actions">
            <button class="btn primary" type="submit">登录</button>
            <a class="btn ghost" href="../">返回首页</a>
          </div>
          <p class="hint">默认密码为 <code><?= h($ADMIN_PASSWORD) ?></code>，请在 <code>config.php</code> 中修改。</p>
        </form>
      </main>
    </body>
    </html>
    <?php
    exit;
}
?>