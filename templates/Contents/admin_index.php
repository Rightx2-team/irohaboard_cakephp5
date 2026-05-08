<?= $this->element('admin_menu'); ?>
<?php $this->start('script-embedded'); ?>
<script>
$(function() {
    $('#sortable-table tbody').sortable({
        helper: function(event, ui) {
            var children = ui.children();
            var clone = ui.clone();
            clone.children().each(function(index) {
                $(this).width(children.eq(index).width());
            });
            return clone;
        },
        update: function(event, ui) {
            var id_list = [];
            $('.content_id').each(function() {
                id_list.push($(this).val());
            });
            $.ajax({
                url: "<?= $this->Url->build(['action' => 'adminOrder']) ?>",
                type: "POST",
                data: { id_list: id_list },
                dataType: "text"
            });
        },
        cursor: "move",
        opacity: 0.5
    });
});
</script>
<?php $this->end(); ?>

<div class="admin-contents-index">
    <div class="ib-breadcrumb">
        <?= $this->Html->link(__('コース一覧'), ['controller' => 'Courses', 'action' => 'adminIndex']); ?>
        &nbsp;/&nbsp;
        <?= h($course->title) ?>
    </div>
    <div class="ib-page-title"><?= __('コンテンツ一覧') ?></div>
    <div class="buttons_container">
        <button type="button" class="btn btn-primary btn-add" onclick="location.href='<?= $this->Url->build(['action' => 'adminAdd', $course->id]) ?>'">+ <?= __('追加') ?></button>
    </div>
    <div class="alert alert-warning"><?= __('ドラッグアンドドロップでコンテンツの並び順が変更できます') ?></div>
    <table id='sortable-table'>
    <thead>
    <tr>
        <th><?= __('コンテンツ名') ?></th>
        <th nowrap><?= __('コンテンツ種別') ?></th>
        <th class="text-center"><?= __('ステータス') ?></th>
        <th class="ib-col-date"><?= __('作成日時') ?></th>
        <th class="ib-col-date"><?= __('更新日時') ?></th>
        <th class="ib-col-action"><?= __('Actions') ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($contents as $content): ?>
    <?php
        switch ($content->kind) {
            case 'test':
                $title = $this->Html->link($content->title, ['controller' => 'ContentsQuestions', 'action' => 'adminIndex', $content->id]);
                break;
            case 'enquete':
                $title = $this->Html->link($content->title, ['controller' => 'EnquetesQuestions', 'action' => 'adminIndex', $content->id]);
                break;
            default:
                $title = h($content->title);
                break;
        }
    ?>
    <tr>
        <td><?= $title ?></td>
        <td><?= h(\Cake\Core\Configure::read('content_kind.' . $content->kind)) ?>&nbsp;</td>
        <td class="text-center"><?= h(\Cake\Core\Configure::read('content_status.' . $content->status)) ?>&nbsp;</td>
        <td class="ib-col-date"><?= h($content->created) ?>&nbsp;</td>
        <td class="ib-col-date"><?= h($content->modified) ?>&nbsp;</td>
        <td class="ib-col-action">
            <button type="button" class="btn btn-success" onclick="location.href='<?= $this->Url->build(['action' => 'adminEdit', $course->id, $content->id]) ?>'"><?= __('編集') ?></button>
            <?php if (!empty($loginedUser) && ($loginedUser['role'] ?? '') === 'admin'): ?>
            <?= $this->Form->postLink(__('削除'), ['action' => 'adminDelete', $content->id], ['class' => 'btn btn-danger', 'confirm' => sprintf(__('[%s] を削除してもよろしいですか?'), $content->title)]); ?>
            <?php endif; ?>
            <input type="hidden" class="content_id" value="<?= h($content->id) ?>">
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
</div>