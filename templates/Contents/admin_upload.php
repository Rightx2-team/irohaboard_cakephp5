<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset(); ?>
    <title><?= __('ファイルアップロード') ?></title>
    <?= $this->Html->css('bootstrap.min') ?>
    <?= $this->Html->css('common.css') ?>
    <?= $this->Html->script('jquery-1.9.1.min.js') ?>
</head>
<body style="padding: 10px; margin: 0; font-size: 13px;">
<?php if ($mode === 'complete'): ?>
    <script>
        (function() {
            var fileUrl  = '<?= h($file_url) ?>';
            var fileName = '<?= h($file_name) ?>';
            try {
                if (window.parent && window.parent !== window) {
                    // jQuery経由で試す
                    if (window.parent.jQuery) {
                        window.parent.jQuery('#ContentUrl').val(fileUrl).trigger('change');
                        window.parent.jQuery('#ContentFileName').val(fileName).trigger('change');
                    } else if (window.parent.$) {
                        window.parent.$('#ContentUrl').val(fileUrl).trigger('change');
                        window.parent.$('#ContentFileName').val(fileName).trigger('change');
                    }

                    // DOM 直接操作（フォールバック）
                    var parentDoc = window.parent.document;
                    var urlInput = parentDoc.getElementById('ContentUrl') || parentDoc.querySelector('input[name="url"]');
                    var fileNameInput = parentDoc.getElementById('ContentFileName') || parentDoc.querySelector('input[name="file_name"]');
                    if (urlInput) urlInput.value = fileUrl;
                    if (fileNameInput) fileNameInput.value = fileName;

                    if (typeof window.parent.onUploadComplete === 'function') {
                        window.parent.onUploadComplete(fileUrl, fileName);
                    }
                }
            } catch (e) {
                console.error('Upload complete error:', e);
            }
        })();
    </script>
    <div class="alert alert-success" style="margin: 0;">
        <?= __('アップロード完了') ?>: <strong><?= h($file_name) ?></strong><br>
        <small style="color: #666;"><?= __('保存名') ?>: <?= h($file_url) ?></small>
    </div>
<?php elseif ($mode === 'error'): ?>
    <div class="alert alert-danger" style="margin: 0 0 10px 0;">
        <?= $this->Flash->render() ?>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div class="form-group" style="margin: 8px 0;">
        <input type="file" name="data[Content][file]" required>
        <button type="submit" class="btn btn-primary"><?= __('アップロード') ?></button>
    </div>
</form>
<div class="text-muted" style="margin-top: 8px;">
    <small>
        <?= __('許可拡張子') ?>: <?= h($upload_extensions_str ?? '') ?><br>
        <?= __('最大サイズ') ?>: <?= number_format(($upload_maxsize ?? 0) / 1024 / 1024, 1) ?> MB
    </small>
</div>
</body>
</html>