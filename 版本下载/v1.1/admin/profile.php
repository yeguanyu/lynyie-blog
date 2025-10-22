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

// 处理登入日志删除请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_login_log') {
    $log_id = isset($_POST['log_id']) ? $_POST['log_id'] : '';
    if ($log_id !== '') {
        if (delete_login_log($log_id)) {
            $ok = '登入日志已删除。';
        } else {
            $err = '删除登入日志失败。';
        }
    }
}

// 处理Cookie授权删除请求
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_cookie_auth') {
    $auth_id = isset($_POST['auth_id']) ? $_POST['auth_id'] : '';
    if ($auth_id !== '') {
        if (delete_cookie_auth($auth_id)) {
            $ok = 'Cookie授权已删除。';
        } else {
            $err = '删除Cookie授权失败。';
        }
    }
}

// 处理清空所有登入日志请求
if (isset($_GET['action']) && $_GET['action'] === 'clear_login_logs') {
    if (clear_login_logs()) {
        $ok = '所有登入日志已清空。';
    } else {
        $err = '清空登入日志失败。';
    }
}

// 处理清空所有Cookie授权请求
if (isset($_GET['action']) && $_GET['action'] === 'clear_cookie_auths') {
    if (clear_cookie_auths()) {
        $ok = '所有Cookie授权已清空。';
    } else {
        $err = '清空Cookie授权失败。';
    }
}

// 读取登入日志和Cookie授权数据
$login_logs = read_login_logs();
$cookie_auths = read_cookie_auths();
?>

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
    
    <!-- 登入日志 -->
    <div class="card" style="margin-bottom: 30px;">
      <div class="card-body">
        <h3><a href="javascript:void(0)" onclick="toggleSection('loginLogs')" style="color: var(--text);">登入日志 <span id="loginLogsToggle">+</span></a></h3>
        <div id="loginLogs" style="display: none;">
          <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
            <p style="color: var(--text);">记录各IP各时间的登入登出操作。</p>
            <a class="btn secondary" href="index.php?section=profile&action=clear_login_logs" onclick="return confirm('确定要清空所有登入日志吗？')">清空日志</a>
          </div>
          <?php if (empty($login_logs)): ?>
            <p class="empty">暂无登入日志。</p>
          <?php else: ?>
            <div class="table-container">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>时间</th>
                    <th>IP地址</th>
                    <th>操作</th>
                    <th>User-Agent</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($login_logs as $log): ?>
                    <tr>
                      <td><?= h($log['timestamp']) ?></td>
                      <td><?= h($log['ip']) ?></td>
                      <td><?= h($log['action'] === 'login' ? '登入' : '登出') ?></td>
                      <td style="max-width: 300px; word-break: break-all;"><?= h($log['user_agent']) ?></td>
                      <td>
                        <form method="post" style="display: inline;">
                          <input type="hidden" name="action" value="delete_login_log" />
                          <input type="hidden" name="log_id" value="<?= h($log['id']) ?>" />
                          <button class="btn danger small" type="submit" onclick="return confirm('确定要删除这条登入日志吗？')">删除</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <!-- Cookie授权 -->
    <div class="card" style="margin-bottom: 30px;">
      <div class="card-body">
        <h3><a href="javascript:void(0)" onclick="toggleSection('cookieAuths')" style="color: var(--text);">Cookie授权 <span id="cookieAuthsToggle">+</span></a></h3>
        <div id="cookieAuths" style="display: none;">
          <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
            <p style="color: var(--text);">已授权的Cookie列表。</p>
            <a class="btn secondary" href="index.php?section=profile&action=clear_cookie_auths" onclick="return confirm('确定要清空所有Cookie授权吗？')">清空授权</a>
          </div>
          <?php if (empty($cookie_auths)): ?>
            <p class="empty">暂无Cookie授权。</p>
          <?php else: ?>
            <div class="table-container">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>创建时间</th>
                    <th>最后使用</th>
                    <th>IP地址</th>
                    <th>User-Agent</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($cookie_auths as $auth): ?>
                    <tr>
                      <td><?= h($auth['created_at']) ?></td>
                      <td><?= h($auth['last_used']) ?></td>
                      <td><?= h($auth['ip']) ?></td>
                      <td style="max-width: 300px; word-break: break-all;"><?= h($auth['user_agent']) ?></td>
                      <td>
                        <form method="post" style="display: inline;">
                          <input type="hidden" name="action" value="delete_cookie_auth" />
                          <input type="hidden" name="auth_id" value="<?= h($auth['id']) ?>" />
                          <button class="btn danger small" type="submit" onclick="return confirm('确定要删除这个Cookie授权吗？')">删除</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
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