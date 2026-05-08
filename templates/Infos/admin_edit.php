<?= $this->element('admin_menu'); ?>
<div class="admin-infos-edit">
    <div class="ib-page-title">
        <?= empty($info->id) ? __('お知らせ追加') : __('お知らせ編集') ?>
    </div>
    <?= $this->Form->create($info); ?>
        <div class="form-group">
            <label><?= __('タイトル') ?></label>
            <?= $this->Form->control('title', ['label' => false, 'class' => 'form-control', 'required' => true]); ?>
        </div>
        <div class="form-group">
            <label><?= __('本文') ?></label>
            <?= $this->Form->control('body', ['label' => false, 'class' => 'form-control', 'type' => 'textarea', 'rows' => 10]); ?>
        </div>
        <div class="form-group">
            <?= $this->Form->button(__('保存'), ['class' => 'btn btn-primary']); ?>
            <a href="<?= $this->Url->build(['action' => 'adminIndex']) ?>" class="btn btn-default"><?= __('キャンセル') ?></a>
        </div>
    <?= $this->Form->end(); ?>
</div>