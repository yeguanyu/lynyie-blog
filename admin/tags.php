<?php
require_once __DIR__ . '/../config.php';

// 检查管理员权限
if (!isset($_SESSION['admin_authed']) || !$_SESSION['admin_authed']) {
    header('Location: login.php');
    exit;
}

$err = '';
$ok = '';

// 处理标签创建或更新请求
$action = isset($_POST['action']) ? $_POST['action'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'create_tag' || $action === 'update_tag')) {
    $is_update = $action === 'update_tag';
    $old_name = $is_update ? (isset($_POST['old_name']) ? $_POST['old_name'] : '') : '';
    
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $color = isset($_POST['color']) ? trim($_POST['color']) : '#7c5cff';
    $bgcolor = isset($_POST['bgcolor']) ? trim($_POST['bgcolor']) : 'rgba(124,92,255,0.10)';
    $border = isset($_POST['border']) ? trim($_POST['border']) : 'rgba(124,92,255,0.25)';
    
    if ($name === '') {
        $err = '标签名称不能为空。';
    } else {
        // 读取现有标签
        $tags = read_tags();
        
        // 检查标签是否已存在（更新时允许与自己相同）
        $existing_tag = null;
        foreach ($tags as $tag) {
            if ($tag['name'] === $name && (!$is_update || $tag['name'] !== $old_name)) {
                $existing_tag = $tag;
                break;
            }
        }
        
        if ($existing_tag && !$is_update) {
            $err = '该标签已存在。';
        } else {
            // 准备标签数据
            $tag_data = array(
                'name' => $name,
                'color' => $color,
                'bgcolor' => $bgcolor,
                'border' => $border
            );
            
            if ($is_update) {
                // 更新标签
                $updated = false;
                foreach ($tags as &$tag) {
                    if ($tag['name'] === $old_name) {
                        $tag = $tag_data;
                        $updated = true;
                        break;
                    }
                }
                
                if ($updated) {
                    save_tags($tags);
                    $ok = '标签已更新！';
                } else {
                    $err = '更新标签失败。';
                }
            } else {
                // 创建新标签
                $tags[] = $tag_data;
                save_tags($tags);
                $ok = '标签创建成功！';
            }
        }
    }
}

// 处理标签删除
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete_tag') {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    if ($name !== '') {
        $tags = read_tags();
        $tags = array_values(array_filter($tags, function($tag) use ($name) {
            return $tag['name'] !== $name;
        }));
        save_tags($tags);
        $ok = '标签已删除。';
    }
}

// 获取要编辑的标签
$editing_tag = null;
if (isset($_GET['edit'])) {
    $edit_name = $_GET['edit'];
    $tags = read_tags();
    foreach ($tags as $tag) {
        if ($tag['name'] === $edit_name) {
            $editing_tag = $tag;
            break;
        }
    }
}

$tags = read_tags();
?>