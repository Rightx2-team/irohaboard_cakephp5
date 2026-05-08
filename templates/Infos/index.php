<div class="infos-index">
    <div class="breadcrumb">
        <?= $this->Html->link('<span class="glyphicon glyphicon-home" aria-hidden="true"></span> HOME', ['controller' => 'UsersCourses', 'action' => 'index'], ['escape' => false]) ?>
    </div>
    <div class="panel panel-success">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            <?= __('お知らせ一覧') ?>
        </div>
        <div class="panel-body">
            <table cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th width="100"><?= __('日付') ?></th>
                <th><?= __('タイトル') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($infos as $info): ?>
            <tr>
                <td valign="top"><?= h($info->created) ?>&nbsp;</td>
                <td><?= $this->Html->link(h($info->title), ['action' => 'view', $info->id]) ?>&nbsp;</td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
        </div>
    </div>
</div>