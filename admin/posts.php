<?php
require_once __DIR__ . '/../config.php';

// 检查管理员权限
if (!isset($_SESSION['admin_authed']) || !$_SESSION['admin_authed']) {
    header('Location: login.php');
    exit;
}

$err = '';
$ok = '';

// 已登录：发布文章
$posts = read_posts();

// 处理文章删除请求
$action = isset($_POST['action']) ? $_POST['action'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    $slug = isset($_POST['slug']) ? $_POST['slug'] : '';
    if ($slug !== '') {
        delete_post($slug);
        $ok = '文章已删除。';
        // 重新加载文章列表
        $posts = read_posts();
    }
}

// 处理编辑请求
$editing_post = null;
if (isset($_GET['edit'])) {
    $edit_slug = $_GET['edit'];
    $editing_post = find_post_by_slug($posts, $edit_slug);
}

// 处理创建或更新请求
$action = isset($_POST['action']) ? $_POST['action'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'create' || $action === 'update')) {
    $is_update = $action === 'update';
    $old_slug = $is_update ? (isset($_POST['old_slug']) ? $_POST['old_slug'] : '') : '';
    
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : date('Y-m-d');
    $tags_input = isset($_POST['tags']) ? $_POST['tags'] : '';
    $tags = array_values(array_filter(array_map('trim', explode(',', $tags_input))));
    $cover = isset($_POST['cover']) ? trim($_POST['cover']) : '';
    $excerpt = isset($_POST['excerpt']) ? trim($_POST['excerpt']) : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    // 获取画中画设置
    $overlay_settings = array();
    if ($is_update) {
        $overlay_settings['overlay_enabled'] = isset($_POST['overlay_enabled']) && $_POST['overlay_enabled'] == 'on' ? true : false;
        $overlay_settings['overlay_blur'] = isset($_POST['overlay_blur']) ? $_POST['overlay_blur'] : '0';
        $overlay_settings['overlay_opacity'] = isset($_POST['overlay_opacity']) ? $_POST['overlay_opacity'] : '0';
        $overlay_settings['overlay_border_radius'] = isset($_POST['overlay_border_radius']) ? $_POST['overlay_border_radius'] : '0';
        $overlay_settings['overlay_text_color'] = isset($_POST['overlay_text_color']) ? $_POST['overlay_text_color'] : '#ffffff';
        $overlay_settings['overlay_bg_color'] = isset($_POST['overlay_bg_color']) ? $_POST['overlay_bg_color'] : '#000000';
    }

    if ($title === '' || $content === '') {
        $err = '标题与正文不能为空。';
    } else {
        if ($slug === '') $slug = normalize_slug($title);
        if ($slug === '') $slug = 'post-' . time();
        
        // 检查slug是否冲突（更新时允许与自己相同）
        $existing_post = find_post_by_slug($posts, $slug);
        if ($existing_post && (!$is_update || $existing_post['slug'] !== $old_slug)) {
            $err = '该别名已存在，请更换。';
        } else {
            // 准备文章数据
            $post_data = array(
                'slug' => $slug,
                'title' => $title,
                'excerpt' => $excerpt !== '' ? $excerpt : mb_substr(strip_tags($content), 0, 80),
                'date' => $date,
                'tags' => $tags,
                'cover' => $cover !== '' ? $cover : 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1600&auto=format&fit=crop'
            );
            
            if ($is_update) {
                // 更新文章
                update_post($old_slug, array_merge($post_data, ['content' => $content]));
                
                // 保存画中画设置
                save_post_overlay_settings($slug, $overlay_settings);
                
                $ok = '文章已更新！';
            } else {
                // 创建新文章
                create_post_content_file($slug, $content);
                array_unshift($posts, $post_data);
                save_posts($posts);
                $ok = '发布成功！';
            }
        }
    }
}
?>