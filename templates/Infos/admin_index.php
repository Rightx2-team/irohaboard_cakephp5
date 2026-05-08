<?= $this->element('admin_menu'); ?>
<div class="admin-infos-index">
    <div class="ib-page-title"><?= __('お知らせ一覧') ?></div>
    <div class="buttons_container">
        <button type="button" class="btn btn-primary btn-add" onclick="location.href='<?= $this->Url->build(['action' => 'adminAdd']) ?>'">+ <?= __('追加') ?></button>
    </div>
    <table>
    <thead>
    <tr>
        <th><?= __('タイトル') ?></th>
        <th class="ib-col-date"><?= __('作成日時') ?></th>
        <th class="ib-col-action"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($infos as $info): ?>
    <tr>
        <td><?= h($info->title) ?></td>
        <td class="ib-col-date"><?= h($info->created) ?>&nbsp;</td>
        <td class="ib-col-action">
            <button type="button" class="btn btn-success" onclick="location.href='<?= $this->Url->build(['action' => 'adminEdit', $info->id]) ?>'"><?= __('編集') ?></button>
            <?= $this->Form->postLink(__('削除'), ['action' => 'adminDelete', $info->id], ['class' => 'btn btn-danger', 'confirm' => sprintf(__('[%s] を削除してもよろしいですか?'), $info->title)]); ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
</div>
<?= $this->element('paging'); ?>