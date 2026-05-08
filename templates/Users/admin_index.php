<?= $this->element('admin_menu'); ?>
<div class="admin-users-index">
    <div class="ib-page-title"><?= __('ユーザ一覧') ?></div>
    <div class="buttons_container">
        <?php if (($loginedUser['role'] ?? '') === 'admin'): ?>
        <button type="button" class="btn btn-primary btn-add" onclick="location.href='<?= $this->Url->build(['action' => 'adminAdd']) ?>'">+ <?= __('追加') ?></button>
        <?php endif; ?>
    </div>
    <table>
    <thead>
    <tr>
        <th nowrap><?= __('ログインID') ?></th>
        <th nowrap><?= __('氏名') ?></th>
        <th nowrap><?= __('権限') ?></th>
        <th><?= __('メールアドレス') ?></th>
        <th class="ib-col-datetime"><?= __('作成日時') ?></th>
        <?php if (($loginedUser['role'] ?? '') === 'admin'): ?>
        <th class="ib-col-action"><?= __('Actions') ?></th>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= h($user->username) ?></td>
        <td><?= h($user->name) ?></td>
        <td nowrap><?= h(\Cake\Core\Configure::read('user_role.' . $user->role)) ?></td>
        <td><?= h($user->email) ?>&nbsp;</td>
        <td class="ib-col-datetime"><?= h($user->created) ?>&nbsp;</td>
        <?php if (($loginedUser['role'] ?? '') === 'admin'): ?>
        <td class="ib-col-action">
            <button type="button" class="btn btn-success" onclick="location.href='<?= $this->Url->build(['action' => 'adminEdit', $user->id]) ?>'"><?= __('編集') ?></button>
            <?= $this->Form->postLink(__('削除'), ['action' => 'adminDelete', $user->id], ['class' => 'btn btn-danger', 'confirm' => sprintf(__('[%s] を削除してもよろしいですか?'), $user->name)]); ?>
        </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    <?= $this->element('paging') ?>
</div>
