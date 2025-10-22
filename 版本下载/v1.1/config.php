<?php
session_start();

// 站点信息
$SITE_NAME = 'LynYie';
$SITE_TAGLINE = '写字与代码，生活中的光';
$SITE_DESCRIPTION = '一个极致精美、专注体验的个人博客';
$ADMIN_PASSWORD = 'lynyie-admin-123'; // 请尽快在上线前修改

date_default_timezone_set('Asia/Shanghai');

function h($s) {
    return htmlspecialchars(isset($s) ? $s : '', ENT_QUOTES, 'UTF-8');
}

function base_dir($path = '') {
    $base = __DIR__;
    return rtrim($base, DIRECTORY_SEPARATOR) . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : '');
}

function json_read($path) {
    if (!file_exists($path)) return [];
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function json_write($path, $data) {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return file_put_contents($path, $json) !== false;
}

function read_design_settings() {
    $design_file = base_dir('data/design.json');
    if (file_exists($design_file)) {
        return json_read($design_file);
    }
    return array();
}

function posts_path() {
    return base_dir('data/posts.json');
}

function posts_dir() {
    return base_dir('posts');
}

function read_posts() {
    return json_read(posts_path());
}

function save_posts($posts) {
    return json_write(posts_path(), $posts);
}

function find_post_by_slug($posts, $slug) {
    foreach ($posts as $p) {
        if ((isset($p['slug']) ? $p['slug'] : '') === $slug) return $p;
    }
    return null;
}

function ensure_posts_dir() {
    $dir = posts_dir();
    if (!is_dir($dir)) mkdir($dir, 0777, true);
}

function create_post_content_file($slug, $html) {
    ensure_posts_dir();
    $path = posts_dir() . DIRECTORY_SEPARATOR . $slug . '.html';
    return file_put_contents($path, $html);
}

function read_post_content($slug) {
    $path = posts_dir() . DIRECTORY_SEPARATOR . $slug . '.html';
    if (!file_exists($path)) return '';
    return file_get_contents($path);
}

function normalize_slug($text) {
    $text = strtolower($text);
    // 替换空白为-，移除不可见字符
    $text = preg_replace('/\s+/', '-', $text);
    // 仅保留数字、字母、连字符
    $text = preg_replace('/[^a-z0-9\-]/', '', $text);
    // 合并多余连字符
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

function format_date_cn($dateStr) {
    $ts = strtotime($dateStr);
    if (!$ts) return h($dateStr);
    return date('Y年n月j日', $ts);
}

function delete_post($slug) {
    // 删除文章内容文件
    $contentPath = posts_dir() . DIRECTORY_SEPARATOR . $slug . '.html';
    if (file_exists($contentPath)) {
        unlink($contentPath);
    }
    
    // 从posts.json中移除文章记录
    $posts = read_posts();
    $posts = array_values(array_filter($posts, function($p) use ($slug) {
        return (isset($p['slug']) ? $p['slug'] : '') !== $slug;
    }));
    
    return save_posts($posts);
}

function update_post($oldSlug, $data) {
    // 如果slug改变，删除旧文件
    if ($oldSlug !== $data['slug']) {
        $oldPath = posts_dir() . DIRECTORY_SEPARATOR . $oldSlug . '.html';
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }
    
    // 创建或更新文章内容文件
    create_post_content_file($data['slug'], $data['content']);
    
    // 更新posts.json中的文章记录
    $posts = read_posts();
    $found = false;
    
    foreach ($posts as &$post) {
        if ((isset($post['slug']) ? $post['slug'] : '') === $oldSlug) {
            $post = array_merge($post, $data);
            $found = true;
            break;
        }
    }
    
    // 如果没有找到旧文章，添加新文章
    if (!$found) {
        array_unshift($posts, $data);
    }
    
    return save_posts($posts);
}

function get_post_views($slug) {
    $views_file = base_dir('data/views.json');
    $views_data = json_read($views_file);
    return isset($views_data[$slug]) ? $views_data[$slug] : 0;
}

function increment_post_views($slug) {
    $views_file = base_dir('data/views.json');
    $views_data = json_read($views_file);
    $views_data[$slug] = (isset($views_data[$slug]) ? $views_data[$slug] : 0) + 1;
    return json_write($views_file, $views_data);
}

// 标签相关函数
function read_tags() {
    $tags_file = base_dir('data/tags.json');
    if (!file_exists($tags_file)) {
        return [];
    }
    $raw = file_get_contents($tags_file);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function save_tags($tags) {
    $tags_file = base_dir('data/tags.json');
    return json_write($tags_file, $tags);
}

function get_tag_style($tag_name) {
    $tags = read_tags();
    foreach ($tags as $tag) {
        if ($tag['name'] === $tag_name) {
            return $tag;
        }
    }
    // 返回默认样式
    return [
        'name' => $tag_name,
        'color' => '#2bd4ff',
        'bgcolor' => 'rgba(43,212,255,0.10)',
        'border' => 'rgba(43,212,255,0.25)'
    ];
}

// 文章级别的overlay设置
function get_post_overlay_settings($slug) {
    $overlay_file = posts_dir() . DIRECTORY_SEPARATOR . $slug . '_overlay.json';
    if (file_exists($overlay_file)) {
        return json_read($overlay_file);
    }
    // 返回默认设置
    return [
        'overlay_enabled' => false,
        'overlay_blur' => '0',
        'overlay_opacity' => '0',
        'overlay_border_radius' => '0',
        'overlay_text_color' => '#ffffff',
        'overlay_bg_color' => '#000000'
    ];
}

function save_post_overlay_settings($slug, $settings) {
    $overlay_file = posts_dir() . DIRECTORY_SEPARATOR . $slug . '_overlay.json';
    return json_write($overlay_file, $settings);
}

// 登录日志相关函数
function get_login_logs_file() {
    return base_dir('data/login_logs.json');
}

function read_login_logs() {
    $logs_file = get_login_logs_file();
    if (!file_exists($logs_file)) {
        return [];
    }
    $raw = file_get_contents($logs_file);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function add_login_log($ip, $action, $user_agent = '') {
    $logs = read_login_logs();
    
    // 创建新的日志条目
    $log_entry = [
        'id' => uniqid(),
        'ip' => $ip,
        'action' => $action, // login 或 logout
        'timestamp' => date('Y-m-d H:i:s'),
        'user_agent' => $user_agent
    ];
    
    // 将新条目添加到日志数组开头
    array_unshift($logs, $log_entry);
    
    // 只保留最近100条记录
    $logs = array_slice($logs, 0, 100);
    
    // 保存日志
    $logs_file = get_login_logs_file();
    return json_write($logs_file, $logs);
}

function delete_login_log($log_id) {
    $logs = read_login_logs();
    
    // 过滤掉要删除的日志条目
    $logs = array_filter($logs, function($log) use ($log_id) {
        return $log['id'] !== $log_id;
    });
    
    // 重新索引数组
    $logs = array_values($logs);
    
    // 保存日志
    $logs_file = get_login_logs_file();
    return json_write($logs_file, $logs);
}

function clear_login_logs() {
    $logs_file = get_login_logs_file();
    return file_put_contents($logs_file, '[]') !== false;
}

// Cookie授权相关函数
function get_cookie_auths_file() {
    return base_dir('data/cookie_auths.json');
}

function read_cookie_auths() {
    $auths_file = get_cookie_auths_file();
    if (!file_exists($auths_file)) {
        return [];
    }
    $raw = file_get_contents($auths_file);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function add_cookie_auth($ip, $user_agent = '') {
    $auths = read_cookie_auths();
    
    // 检查是否已存在相同的IP和User-Agent组合
    foreach ($auths as $auth) {
        if ($auth['ip'] === $ip && $auth['user_agent'] === $user_agent) {
            // 如果已存在，更新时间戳
            $auth['last_used'] = date('Y-m-d H:i:s');
            return true;
        }
    }
    
    // 创建新的授权条目
    $auth_entry = [
        'id' => uniqid(),
        'ip' => $ip,
        'user_agent' => $user_agent,
        'created_at' => date('Y-m-d H:i:s'),
        'last_used' => date('Y-m-d H:i:s')
    ];
    
    // 将新条目添加到授权数组
    $auths[] = $auth_entry;
    
    // 保存授权列表
    $auths_file = get_cookie_auths_file();
    return json_write($auths_file, $auths);
}

function delete_cookie_auth($auth_id) {
    $auths = read_cookie_auths();
    
    // 过滤掉要删除的授权条目
    $auths = array_filter($auths, function($auth) use ($auth_id) {
        return $auth['id'] !== $auth_id;
    });
    
    // 重新索引数组
    $auths = array_values($auths);
    
    // 保存授权列表
    $auths_file = get_cookie_auths_file();
    return json_write($auths_file, $auths);
}

function clear_cookie_auths() {
    $auths_file = get_cookie_auths_file();
    return file_put_contents($auths_file, '[]') !== false;
}
