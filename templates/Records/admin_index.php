<?= $this->element('admin_menu'); ?>
<?php $this->start('script-embedded'); ?>
<script>
function openRecord(course_id, user_id) {
    var baseUrl = '/cakephp5_app/admin/contents/record';
    window.open(
        baseUrl + '/' + course_id + '/' + user_id,
        'irohaboard_record',
        'width=1100, height=700, menubar=no, toolbar=no, scrollbars=yes'
    );
}
function downloadCSV() {
    document.getElementById('RecordCmd').value = 'csv';
    document.querySelector('form').submit();
}
</script>
<?php $this->end(); ?>

<div class="admin-records-index">
    <div class="ib-page-title"><?= __('学習履歴一覧') ?></div>
    <div class="ib-horizontal">
        <?= $this->Form->create(null, ['method' => 'get']); ?>
        <div class="ib-search-buttons">
            <?= $this->Form->submit(__('検索'), ['class' => 'btn btn-info']); ?>
            <?= $this->Form->hidden('cmd', ['id' => 'RecordCmd']); ?>
            <button type="button" class="btn btn-default" onclick="downloadCSV()"><?= __('CSV出力') ?></button>
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:10px; margin:8px 0;">
            <div>
                <label><?= __('コース') ?></label><br>
                <?= $this->Form->control('course_id', ['label' => false, 'type' => 'select', 'options' => $courses, 'empty' => __('全て'), 'class' => 'form-control', 'value' => $this->request->getQuery('course_id', '')]); ?>
            </div>
            <div>
                <label><?= __('コンテンツ種別') ?></label><br>
                <?= $this->Form->control('content_category', ['label' => false, 'type' => 'select', 'options' => \Cake\Core\Configure::read('content_category'), 'empty' => __('全て'), 'class' => 'form-control', 'value' => $content_category]); ?>
            </div>
            <div>
                <label><?= __('コンテンツ名') ?></label><br>
                <?= $this->Form->control('content_title', ['label' => false, 'class' => 'form-control', 'value' => $this->request->getQuery('content_title', '')]); ?>
            </div>
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:10px; margin:8px 0;">
            <div>
                <label><?= __('グループ') ?></label><br>
                <?= $this->Form->control('group_id', ['label' => false, 'type' => 'select', 'options' => $groups, 'empty' => __('全て'), 'class' => 'form-control', 'value' => $this->request->getQuery('group_id', '')]); ?>
            </div>
            <div>
                <label><?= __('ログインID') ?></label><br>
                <?= $this->Form->control('username', ['label' => false, 'class' => 'form-control', 'value' => $this->request->getQuery('username', '')]); ?>
            </div>
            <div>
                <label><?= __('氏名') ?></label><br>
                <?= $this->Form->control('name', ['label' => false, 'class' => 'form-control', 'value' => $this->request->getQuery('name', '')]); ?>
            </div>
        </div>
        <div style="display:flex; gap:10px; align-items:flex-end; margin:8px 0;">
            <div>
                <label><?= __('対象期間') ?></label><br>
                <?= $this->Form->control('from_date', ['label' => false, 'type' => 'date', 'class' => 'form-control', 'value' => $from_date]); ?>
            </div>
            <div style="padding-bottom:8px;">〜</div>
            <div>
                <label>&nbsp;</label><br>
                <?= $this->Form->control('to_date', ['label' => false, 'type' => 'date', 'class' => 'form-control', 'value' => $to_date]); ?>
            </div>
        </div>
        <?= $this->Form->end(); ?>
    </div>

    <table cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th nowrap><?= $this->Paginator->sort('Users.username', __('ログインID')); ?></th>
        <th nowrap><?= $this->Paginator->sort('Users.name', __('氏名')); ?></th>
        <th nowrap><?= $this->Paginator->sort('Records.course_id', __('コース')); ?></th>
        <th nowrap><?= $this->Paginator->sort('Records.content_id', __('コンテンツ')); ?></th>
        <th nowrap class="ib-col-center"><?= $this->Paginator->sort('Records.score', __('得点')); ?></th>
        <th class="ib-col-center" nowrap><?= $this->Paginator->sort('Records.pass_score', __('合格点')); ?></th>
        <th class="ib-col-center"><?= __('結果') ?></th>
        <th class="ib-col-center"><?= __('理解度') ?></th>
        <th class="ib-col-center"><?= $this->Paginator->sort('Records.study_sec', __('学習時間')); ?></th>
        <th class="ib-col-datetime"><?= $this->Paginator->sort('Records.created', __('学習日時')); ?></th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($records as $record): ?>
        <tr>
            <td><?= h($record->user->username ?? ''); ?>&nbsp;</td>
            <td><?= h($record->user->name ?? ''); ?>&nbsp;</td>
            <td><a href="javascript:openRecord(<?= h($record->course_id); ?>, <?= h($record->user_id); ?>);"><?= h($record->course->title ?? ''); ?></a>&nbsp;</td>
            <td><?= h($record->content->title ?? ''); ?>&nbsp;</td>
            <td class="ib-col-center"><?= h($record->score); ?>&nbsp;</td>
            <td class="ib-col-center"><?= h($record->pass_score); ?>&nbsp;</td>
            <?php if (($record->content->kind ?? '') === 'enquete'): ?>
            <td class="ib-col-center"><?= __('回答') ?></td>
            <?php else: ?>
            <td nowrap class="ib-col-center"><?= h(\Cake\Core\Configure::read('record_result.' . $record->is_passed)); ?></td>
            <?php endif; ?>
            <td nowrap class="ib-col-center"><?= h(\Cake\Core\Configure::read('record_understanding.' . $record->understanding)); ?>&nbsp;</td>
            <td class="ib-col-center"><?= h(\Utils::getHNSBySec($record->study_sec)); ?>&nbsp;</td>
            <td class="ib-col-date"><?= h(\Utils::getYMDHN($record->created)); ?>&nbsp;</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
    <?= $this->element('paging'); ?>
</div>