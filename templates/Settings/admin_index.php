<?= $this->element('admin_menu'); ?>
<?php $this->start('script-embedded'); ?>
<script>
$(document).ready(function() {
    $('option').each(function() {
        $(this).css('color', 'white');
        $(this).css('background', $(this).val());
        $(this).css('font-weight', 'bold');
    });
});
</script>
<?php $this->end(); ?>
<div class="admin-settings-index">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= __('システム設定') ?>
        </div>
        <div class="panel-body">
            <?= $this->Form->create(null); ?>
                <div class="form-group">
                    <label><?= __('システム名') ?></label>
                    <?= $this->Form->control('title', [
                        'label' => false,
                        'class' => 'form-control',
                        'value' => $settings['title'] ?? '',
                    ]); ?>
                </div>
                <div class="form-group">
                    <label><?= __('コピーライト') ?></label>
                    <?= $this->Form->control('copyright', [
                        'label' => false,
                        'class' => 'form-control',
                        'value' => $settings['copyright'] ?? '',
                    ]); ?>
                </div>
                <div class="form-group">
                    <label><?= __('テーマカラー') ?></label>
                    <?= $this->Form->control('color', [
                        'label' => false,
                        'class' => 'form-control',
                        'type' => 'select',
                        'options' => $colors,
                        'default' => $settings['color'] ?? '',
                    ]); ?>
                </div>
                <div class="form-group">
                    <label><?= __('全体のお知らせ') ?></label>
                    <?= $this->Form->control('information', [
                        'label' => false,
                        'class' => 'form-control',
                        'type' => 'textarea',
                        'rows' => 5,
                        'value' => $settings['information'] ?? '',
                    ]); ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->button(__('保存'), ['class' => 'btn btn-primary']); ?>
                </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>