<?php
/**
 * iroha Board Project - Default layout for CakePHP5. / CakePHP5用デフォルトレイアウト
 */
use Cake\Core\Configure;

$session = $this->request->getSession();

// Check if it is the admin screen (determined by URL path). / 管理画面か確認（URLパスで判定）
$path = $this->request->getUri()->getPath();
$is_admin_page = str_contains($path, '/admin')
    && !in_array($this->request->getParam('action'), ['login', 'adminLogin']);

$appName = defined('APP_NAME') ? APP_NAME : 'iroha Board';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset(); ?>
    <title><?= h($session->read('Setting.title') ?? $appName); ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <?php
        if (!$is_admin_page) echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
        echo $this->Html->meta('icon');
        echo $this->Html->css('jquery-ui');
        echo $this->Html->css('bootstrap.min');
        echo $this->Html->css('common.css?20210701');
        if ($is_admin_page) echo $this->Html->css('admin.css?20200701');
        echo $this->Html->css('custom.css?20200701');
        echo $this->Html->script('jquery-1.9.1.min.js');
        echo $this->Html->script('jquery-ui-1.9.2.min.js');
        echo $this->Html->script('bootstrap.min.js');
        echo $this->Html->script('common.js?20220401');
        if ($is_admin_page) echo $this->Html->script('admin.js?20200701');
        if (\Cake\Core\Configure::read('demo_mode')) echo $this->Html->script('demo.js');
        echo $this->Html->script('custom.js?20200701');
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        echo $this->fetch('css-embedded');
        echo $this->fetch('script-embedded');
    ?>
    <style>
        .ib-theme-color { background-color: <?= h($session->read('Setting.color') ?? '#337ab7'); ?>; color: white; }
        .ib-logo a { color: white; text-decoration: none; }
    </style>
</head>
<body>
    <header class="header ib-theme-color">
        <div class="ib-logo ib-left">
            <a href="<?= $this->Url->build('/') ?>"><?= h($session->read('Setting.title') ?? $appName); ?></a>
        </div>
        <nav class="ib-navi">
            <?php
                $currentLang = $currentLang ?? $session->read('Config.language') ?? 'ja_JP';
                $returnPath  = $this->request->getUri()->getPath();
                $btnStyle    = 'margin-top:8px; color:#333;';
            ?>
            <?php if ($currentLang === 'en_US'): ?>
            <div class="ib-navi-item ib-right" style="padding:0 6px;">
                <?= $this->Html->link('日本語', ['controller' => 'Language', 'action' => 'switch', 'ja_JP', '?' => ['return' => $returnPath]], ['class' => 'btn btn-xs btn-default', 'style' => $btnStyle]); ?>
            </div>
            <?php else: ?>
            <div class="ib-navi-item ib-right" style="padding:0 6px;">
                <?= $this->Html->link('English', ['controller' => 'Language', 'action' => 'switch', 'en_US', '?' => ['return' => $returnPath]], ['class' => 'btn btn-xs btn-default', 'style' => $btnStyle]); ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($loginedUser)): ?>
            <div class="ib-navi-sepa ib-right"></div>
            <div class="ib-navi-item ib-right ib-navi-logout">
                <span class="glyphicon glyphicon-log-out"></span>
                <?= $this->Html->link(__('ログアウト'), ['controller' => 'Users', 'action' => $is_admin_page ? 'adminLogout' : 'logout']); ?>
            </div>
            <div class="ib-navi-sepa ib-right ib-navi-sepa-1"></div>
            <div class="ib-navi-item ib-right ib-navi-setting">
                <span class="glyphicon glyphicon-cog"></span>
                <?= $this->Html->link(__('設定'), ['controller' => 'Users', 'action' => $is_admin_page ? 'adminSetting' : 'setting']); ?>
            </div>
            <div class="ib-navi-sepa ib-right ib-navi-sepa-2"></div>
            <div class="ib-navi-item ib-right ib-navi-welcome"><?= __('ようこそ') . ' ' . h($loginedUser['name'] ?? '') . ' ' . __('さん'); ?></div>
            <?php endif; ?>
        </nav>
    </header>
    <main id="container">
        <div id="content" class="row">
            <?= $this->Flash->render(); ?>
            <?= $this->fetch('content'); ?>
        </div>
    </main>
    <footer class="footer ib-theme-color text-center">
        <?= h($session->read('Setting.copyright') ?? ''); ?>
    </footer>
    <?php if (!empty($loginedUser) && $is_admin_page): ?>
    <div class="irohasoft">
        Powered by <a href="https://irohaboard.irohasoft.jp/"><?= $appName; ?></a>
    </div>
    <?php endif; ?>
</body>
</html>
