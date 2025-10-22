<?php if (isset($_GET['section']) && $_GET['section'] === 'attachments'): ?>
<!-- 附件管理 -->
<section class="card">
  <h2>附件管理</h2>
  
  <?php if ($err): ?><div class="msg error"><?= h($err) ?></div><?php endif; ?>
  <?php if ($ok): ?><div class="msg success"><?= h($ok) ?></div><?php endif; ?>
  
  <!-- 文件上传表单 -->
  <form class="form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="upload" />
    <label>选择文件
      <input type="file" name="attachment" required />
    </label>
    <div class="actions">
      <button class="btn primary" type="submit">上传附件</button>
    </div>
  </form>
</section>

<!-- 附件列表 -->
<h3>已上传附件</h3>
<?php if (empty($attachments)): ?>
  <p class="hint">暂无附件。</p>
<?php else: ?>
  <div class="attachments-grid">
    <?php foreach ($attachments as $attachment): ?>
      <?php
        // 判断是否为图片文件
        $is_image = false;
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $file_extension = strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION));
        if (in_array($file_extension, $image_extensions)) {
          $is_image = true;
        }
      ?>
      <div class="attachment-card">
        <?php if ($is_image): ?>
          <!-- 图片文件显示缩略图 -->
          <div class="attachment-preview">
            <img src="<?= h($attachment['url']) ?>" alt="<?= h($attachment['name']) ?>" class="attachment-image">
          </div>
        <?php else: ?>
          <!-- 非图片文件显示图标 -->
          <div class="attachment-preview">
            <div class="file-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                <polyline points="13 2 13 9 20 9"></polyline>
              </svg>
            </div>
          </div>
        <?php endif; ?>
        
        <div class="attachment-info">
          <div class="attachment-name" title="<?= h($attachment['name']) ?>"><?= h($attachment['name']) ?></div>
          <div class="attachment-meta">
            <span class="attachment-size"><?= format_file_size($attachment['size']) ?></span>
            <span class="attachment-date"><?= date('Y-m-d H:i', $attachment['time']) ?></span>
          </div>
          <div class="attachment-actions">
            <a class="btn small primary" href="<?= h($attachment['url']) ?>" target="_blank">查看</a>
            <button class="btn small ghost" onclick="copyToClipboard('<?= h($attachment['url']) ?>', this)">复制链接</button>
            <form method="post" style="display: inline-block;" onsubmit="return confirm('确定要删除这个附件吗？')">
              <input type="hidden" name="action" value="delete" />
              <input type="hidden" name="file_name" value="<?= h($attachment['name']) ?>" />
              <button class="btn small danger" type="submit">删除</button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<style>
/* 附件网格布局 */
.attachments-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

/* 附件卡片样式 */
.attachment-card {
  border: 1px solid var(--border);
  border-radius: 8px;
  overflow: hidden;
  background: var(--surface);
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.attachment-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  border-color: var(--primary);
}

/* 预览区域 */
.attachment-preview {
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
  border-bottom: 1px solid var(--border);
  overflow: hidden;
}

/* 图片预览 */
.attachment-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

/* 文件图标 */
.file-icon {
  color: #7c5cff;
  opacity: 0.7;
}

/* 附件信息 */
.attachment-info {
  padding: 15px;
}

/* 附件名称 */
.attachment-name {
  font-weight: 600;
  margin-bottom: 8px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* 附件元信息 */
.attachment-meta {
  display: flex;
  justify-content: space-between;
  font-size: 0.85em;
  color: var(--muted);
  margin-bottom: 15px;
}

/* 附件操作按钮 */
.attachment-actions {
  display: flex;
  gap: 8px;
}

.attachment-actions .btn {
  flex: 1;
  min-width: 0;
  text-align: center;
}

/* 响应式设计 */
@media (max-width: 768px) {
  .attachments-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
  }
  
  .attachment-preview {
    height: 150px;
  }
  
  .attachment-actions {
    flex-direction: column;
  }
}
</style>

<script>
// 复制链接到剪贴板功能
function copyToClipboard(text, button) {
  const textarea = document.createElement('textarea');
  textarea.value = text;
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand('copy');
  document.body.removeChild(textarea);
  
  // 显示提示信息
  const originalText = button.textContent;
  button.textContent = '已复制!';
  button.classList.add('success');
  
  setTimeout(() => {
    button.textContent = originalText;
    button.classList.remove('success');
  }, 2000);
}
</script>
<?php endif; ?>