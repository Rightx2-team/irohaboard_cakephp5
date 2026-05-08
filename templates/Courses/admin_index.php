<?= $this->element('admin_menu'); ?>
<div class="admin-courses-index">
    <div class="ib-page-title"><?= __('コース一覧') ?></div>
    <div class="buttons_container">
        <button type="button" class="btn btn-primary btn-add" onclick="location.href='<?= $this->Url->build(['action' => 'adminAdd']) ?>'">+ <?= __('追加') ?></button>
    </div>
    <table>
    <thead>
    <tr>
        <th><?= __('コース名') ?></th>
        <th><?= __('メモ') ?></th>
        <th><?= __('表示順') ?></th>
        <th class="ib-col-date"><?= __('更新日時') ?></th>
        <th class="ib-col-action"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($courses as $course): ?>
    <tr>
        <td>
            <?= $this->Html->link($course->title, ['controller' => 'Contents', 'action' => 'adminIndex', $course->id]) ?>
        </td>
        <td><?= h($course->comment) ?>&nbsp;</td>
        <td><?= h($course->sort_no) ?></td>
        <td class="ib-col-date"><?= h($course->modified) ?>&nbsp;</td>
        <td class="ib-col-action">
            <button type="button" class="btn btn-success" onclick="location.href='<?= $this->Url->build(['action' => 'adminEdit', $course->id]) ?>'"><?= __('編集') ?></button>
            <?= $this->Form->postLink(__('削除'), ['action' => 'adminDelete', $course->id], ['class' => 'btn btn-danger', 'confirm' => sprintf(__('[%s] を削除してもよろしいですか?'), $course->title)]); ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    <?= $this->element('paging') ?>
</div>