<?= $this->element('admin_menu'); ?>
<div class="admin-courses-edit">
    <div class="ib-page-title">
        <?= empty($course->id) ? __('コース追加') : __('コース編集') ?>
    </div>
    <?= $this->Form->create($course); ?>
        <div class="form-group">
            <label><?= __('コース名') ?></label>
            <?= $this->Form->control('title', ['label' => false, 'class' => 'form-control', 'required' => true]); ?>
        </div>
        <div class="form-group">
            <label><?= __('公開開始日時') ?></label>
            <?= $this->Form->control('opened', [
                'label' => false,
                'class' => 'form-control',
                'type' => 'datetime-local',
                'empty' => true,
            ]); ?>
            <small class="text-muted"><?= __('空欄の場合は常時公開') ?></small>
        </div>
        <div class="form-group">
            <label><?= __('コース概要') ?></label>
            <?= $this->Form->control('introduction', ['label' => false, 'class' => 'form-control', 'type' => 'textarea', 'rows' => 3]); ?>
        </div>
        <div class="form-group">
            <label><?= __('表示順') ?></label>
            <?= $this->Form->control('sort_no', ['label' => false, 'class' => 'form-control', 'type' => 'number', 'default' => 0]); ?>
        </div>
        <div class="form-group">
            <label><?= __('所属グループ') ?></label>
            <?= $this->Form->control('groups._ids', [
                'label' => false,
                'class' => 'form-control',
                'type' => 'select',
                'multiple' => 'checkbox',
                'options' => $groups,
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