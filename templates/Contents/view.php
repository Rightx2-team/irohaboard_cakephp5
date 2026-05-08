<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset(); ?>
    <title><?= h($content->title) ?></title>
    <meta name="application-name" content="<?= defined('APP_NAME') ? APP_NAME : 'iroha Board' ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <?php
        echo $this->Html->meta('icon');
        echo $this->Html->css('jquery-ui');
        echo $this->Html->css('bootstrap.min');
        echo $this->Html->css('common.css');
        echo $this->Html->css('contents_view.css');
        echo $this->Html->css('custom.css');
        echo $this->Html->script('jquery-1.9.1.min.js');
        echo $this->Html->script('jquery-ui-1.9.2.min.js');
        echo $this->Html->script('bootstrap.min.js');
        echo $this->Html->script('common.js');
        echo $this->Html->script('contents_view.js');
        echo $this->Html->script('custom.js');
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        echo $this->fetch('css-embedded');
        echo $this->fetch('script-embedded');
    ?>
    <script>
    var URL_RECORDS_ADD    = '<?= $this->Url->build(['controller' => 'Records', 'action' => 'add', $content->id]) ?>';
    var URL_CONTNES_INDEX  = '<?= $this->Url->build(['controller' => 'Contents', 'action' => 'index', $content->course_id]) ?>';
    var BUTTON_PC_LIST     = <?= json_encode(\Cake\Core\Configure::read('record_understanding_pc')) ?>;
    var BUTTON_SPN_LIST    = <?= json_encode(\Cake\Core\Configure::read('record_understanding_spn')) ?>;
    </script>
</head>
<body>
<?php
    $body = '';
    switch ($content->kind) {
        case 'url':
            $body = '<iframe id="contentFrame" width="100%" height="100%" scrolling="yes" src="' . h($content->url) . '"></iframe>';
            break;
        case 'movie':
            $url = h($content->url);
            if (strpos($url, 'http') === false) {
                $url = $this->Url->build(['controller' => 'Contents', 'action' => 'fileMovie', $content->id]);
            }
            $body = '<video src="' . $url . '" controls width="100%" oncontextmenu="return false;"></video>';
            break;
        case 'text':
            $body = h($content->body);
            $body = $this->Text->autoLinkUrls($body);
            $body = nl2br($body);
            break;
        case 'html':
            $body = $content->body ?? '';
            break;
        case 'label':
            $body = h($content->body ?? '');
            break;
    }
?>
<div class="content-view">
    <div class="content-title"><?= h($content->title) ?></div>
    <div class="content-body content-body-<?= h($content->kind) ?>"><?= $body ?></div>
    <div class="content-foot">
        <div class="content-menu">
            <div class="select-message text-success"><?= __('理解度を選択して終了してください') ?></div>
            <span class='understanding-pc'></span>
            <span class='understanding-spn'></span>
            <button type="button" class="btn btn-danger" onclick="finish(0);"><?= __('中断') ?></button>
            <button type="button" class="btn btn-default" onclick="finish(-1);"><?= __('戻る') ?></button>
        </div>
    </div>
</div>
</body>
</html>