<div class="infos-view">
    <div class="breadcrumb">
        <?= $this->Html->link('<span class="glyphicon glyphicon-home" aria-hidden="true"></span> HOME', ['controller' => 'UsersCourses', 'action' => 'index'], ['escape' => false]) ?>
        &nbsp;/&nbsp;
        <?= $this->Html->link('<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> ' . __('お知らせ一覧'), ['controller' => 'Infos', 'action' => 'index'], ['escape' => false]) ?>
    </div>
    <?php
        $title  = h($info->title);
        $date   = h($info->created);
        $body   = $info->body ?? '';
        $target = \Cake\Core\Configure::read('open_link_same_window') ? [] : ['target' => '_blank'];
        $body   = $this->Text->autoLinkUrls($body, $target);
        $body   = nl2br($body);
    ?>
    <div class="panel panel-success">
        <div class="panel-heading"><?= $title ?></div>
        <div class="panel-body">
            <div class="text-right"><?= $date ?></div>
            <?= $body ?>
        </div>
    </div>
</div>