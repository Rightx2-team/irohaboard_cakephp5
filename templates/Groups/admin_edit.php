<?= $this->element('admin_menu'); ?>
<div class="admin-groups-edit">
    <div class="ib-page-title">
        <?= empty($group->id) ? __('グループ追加') : __('グループ編集') ?>
    </div>
    <?= $this->Form->create($group); ?>
        <div class="form-group">
            <label><?= __('グループ名') ?></label>
            <?= $this->Form->control('title', ['label' => false, 'class' => 'form-control', 'required' => true]); ?>
        </div>
        <div class="form-group">
            <label><?= __('公開状態') ?></label>
            <?= $this->Form->control('status', [
                'label' => false,
                'class' => 'form-control',
                'type' => 'select',
                'options' => ['1' => __('公開'), '0' => __('非公開')],
            ]); ?>
        </div>
        <div class="form-group">
            <label><?= __('メモ') ?></label>
            <?= $this->Form->control('comment', ['label' => false, 'class' => 'form-control', 'type' => 'textarea', 'rows' => 3]); ?>
        </div>
        <div class="form-group">
            <?= $this->Form->button(__('保存'), ['class' => 'btn btn-primary']); ?>
            <a href="<?= $this->Url->build(['action' => 'adminIndex']) ?>" class="btn btn-default"><?= __('キャンセル') ?></a>
        </div>
    <?= $this->Form->end(); ?>
</div>