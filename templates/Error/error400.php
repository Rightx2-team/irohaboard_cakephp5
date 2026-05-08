<?php
use Cake\Core\Configure;
?>
<div class="ib-page-title"><?= h($message ?? 'Bad Request') ?></div>
<p class="error">
    <strong><?= __d('cake', 'Error') ?>:</strong>
    <?php
    if (($message ?? '') === 'The request has been black-holed') {
        echo __d('cake', 'トークンの有効期限が切れています。前の画面に戻り、画面を一度リフレッシュしてから再度お試しください。');
    } else {
        printf(__d('cake', '指定されたアドレス %s へのリクエストは無効です。'), "<strong>'" . h($url ?? '') . "'</strong>");
    }
    ?>
</p>
<?php
if (\Cake\Core\Configure::read('debug')) {
    if (isset($error)) {
        echo '<h3>' . h($error->getMessage()) . '</h3>';
        echo '<p>' . h($error->getFile()) . ' : ' . $error->getLine() . '</p>';
        echo '<pre>' . h($error->getTraceAsString()) . '</pre>';
    }
}
?>