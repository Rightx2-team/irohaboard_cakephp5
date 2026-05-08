<?php
use Cake\Core\Configure;
?>
<div class="ib-page-title"><?= h($message ?? 'Internal Server Error') ?></div>
<p class="error">
    <strong><?= __d('cake', 'Error') ?>:</strong>
    <?= __d('cake', '内部エラーが発生しました。') ?>
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