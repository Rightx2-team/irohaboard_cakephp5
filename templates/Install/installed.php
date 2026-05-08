<?php $appName = defined('APP_NAME') ? APP_NAME : 'iroha Board'; ?>
<div class="install-installed">
    <div class="panel panel-info">
        <div class="panel-heading">
            <?= $appName ?> Installer
        </div>
        <div class="panel-body">
            <p class="msg">既にインストールされています。</p>
        </div>
        <div class="panel-footer text-center">
            <button class="btn btn-primary" onclick="location.href='<?= $this->Url->build('/admin/users/login') ?>'">管理者ログイン画面へ</button>
        </div>
    </div>
</div>
