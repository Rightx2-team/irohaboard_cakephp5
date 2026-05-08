<?php $appName = defined('APP_NAME') ? APP_NAME : 'iroha Board'; ?>
<div class="install-error">
    <div class="panel panel-danger">
        <div class="panel-heading">
            <?= $appName ?> Installer
        </div>
        <div class="panel-body">
            <p class="msg"><?= h($body ?? '') ?></p>
        </div>
    </div>
</div>
