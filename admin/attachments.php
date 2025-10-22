<?php
require_once __DIR__ . '/../config.php';

// 检查管理员权限
if (!isset($_SESSION['admin_authed']) || !$_SESSION['admin_authed']) {
    header('Location: login.php');
    exit;
}

$err = '';
$ok = '';

// 附件存储目录
$upload_dir = base_dir('uploads');
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// 处理文件上传
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['attachment']['name'];
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_size = $_FILES['attachment']['size'];
        
        // 检查文件大小（限制为10MB）
        if ($file_size > 10 * 1024 * 1024) {
            $err = '文件大小不能超过10MB。';
        } else {
            // 生成唯一文件名以避免冲突
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $unique_name = uniqid() . '.' . $file_ext;
            $destination = $upload_dir . DIRECTORY_SEPARATOR . $unique_name;
            
            // 移动上传的文件
            if (move_uploaded_file($file_tmp, $destination)) {
                $ok = '文件上传成功！';
            } else {
                $err = '文件上传失败，请重试。';
            }
        }
    } else {
        $err = '请选择要上传的文件。';
    }
}

// 处理文件删除
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $file_name = isset($_POST['file_name']) ? $_POST['file_name'] : '';
    if ($file_name !== '') {
        $file_path = $upload_dir . DIRECTORY_SEPARATOR . $file_name;
        if (file_exists($file_path) && unlink($file_path)) {
            $ok = '文件已删除。';
        } else {
            $err = '文件删除失败。';
        }
    }
}

// 获取站点URL
function get_site_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_path = dirname(dirname($script_name)); // 获取基础路径
    return $protocol . '://' . $host . ($base_path === '/' ? '' : $base_path) . '/';
}

// 获取附件列表
function get_attachments() {
    global $upload_dir;
    $attachments = array();
    
    if (is_dir($upload_dir)) {
        $files = scandir($upload_dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && $file !== '.gitkeep') {
                $file_path = $upload_dir . DIRECTORY_SEPARATOR . $file;
                $file_size = filesize($file_path);
                $file_time = filemtime($file_path);
                
                $attachments[] = array(
                    'name' => $file,
                    'size' => $file_size,
                    'time' => $file_time,
                    'url' => get_site_url() . 'uploads/' . $file
                );
            }
        }
    }
    
    // 按时间倒序排列
    usort($attachments, function($a, $b) {
        return $b['time'] - $a['time'];
    });
    
    return $attachments;
}

$attachments = get_attachments();
?>