<?= $this->element('admin_menu'); ?>
<div class="admin-groups-index">
    <div class="ib-page-title"><?= __('グループ一覧') ?></div>
    <div class="buttons_container">
        <button type="button" class="btn btn-primary btn-add" onclick="location.href='<?= $this->Url->build(['action' => 'adminAdd']) ?>'">+ <?= __('追加') ?></button>
    </div>
    <table>
    <thead>
    <tr>
        <th><?= __('グループ名') ?></th>
        <th><?= __('メモ') ?></th>
        <th class="ib-col-date"><?= __('更新日時') ?></th>
        <th class="ib-col-action"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($groups as $group): ?>
    <tr>
        <td><?= h($group->title) ?></td>
        <td><?= h($group->comment) ?>&nbsp;</td>
        <td class="ib-col-date"><?= h($group->modified) ?>&nbsp;</td>
        <td class="ib-col-action">
            <button type="button" class="btn btn-success" onclick="location.href='<?= $this->Url->build(['action' => 'adminEdit', $group->id]) ?>'"><?= __('編集') ?></button>
            <?= $this->Form->postLink(__('削除'), ['action' => 'adminDelete', $group->id], ['class' => 'btn btn-danger', 'confirm' => sprintf(__('[%s] を削除してもよろしいですか?'), $group->title)]); ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
</div>
<?= $this->element('paging'); ?>