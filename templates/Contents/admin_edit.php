<?= $this->element('admin_menu'); ?>
<?php $this->start('css-embedded'); ?>
<?= $this->Html->css('summernote.css'); ?>
<?php $this->end(); ?>
<?php $this->start('script-embedded'); ?>
<?= $this->Html->script('summernote.min.js'); ?>
<?= $this->Html->script('lang/summernote-ja-JP.js'); ?>
<script>
$(function() {
    $('input[name="kind"]').change(function() {
        changeKind();
    });

    // When saving, if in code view mode, deactivate it (to reflect the content being edited). / 保存時、コード表示モードの場合、解除する（編集中の内容を反映するため）
    $('form').submit(function() {
        if ($('input[name="kind"]:checked').val() === 'html') {
            if ($('#body').summernote('codeview.isActivated')) {
                $('#body').summernote('codeview.deactivate');
            }
        }
    });

    changeKind();

    function changeKind() {
        var kind = $('input[name="kind"]:checked').val() || 'label';
        $('[class*="kind-"]').hide();
        $('.kind-' + kind).show();

        // Start / destroy summernote. / summernote の起動／破棄
        if (kind === 'html') {
            CommonUtil.setRichTextEditor('#body', <?= \Cake\Core\Configure::read('upload_image_maxsize') ?>, '<?= $this->Url->build('/') ?>');
        } else {
            if ($('#body').data('summernote')) {
                $('#body').summernote('destroy');
            }
        }

        // Switch the upload frame. / アップロードフレームの切り替え
        var uploadUrl = '';
        if (kind === 'file') {
            uploadUrl = '<?= $this->Url->build(['action' => 'adminUpload', 'file']) ?>';
        } else if (kind === 'movie') {
            uploadUrl = '<?= $this->Url->build(['action' => 'adminUpload', 'movie']) ?>';
        }

        if (uploadUrl) {
            $('#uploadFrame').attr('src', uploadUrl).show();
            $('#uploadFrameWrap').show();
        } else {
            $('#uploadFrameWrap').hide();
        }
    }
});

// Function called when upload is complete (called from the template inside the iframe). / アップロード完了時に呼ばれる関数（iframe内のテンプレートから呼び出される）
function onUploadComplete(fileUrl, fileName) {
    $('#ContentUrl').val(fileUrl);
    $('#ContentFileName').val(fileName);
}
</script>
<?php $this->end(); ?>

<div class="admin-contents-edit">
    <div class="ib-breadcrumb">
        <?= $this->Html->link(__('コース一覧'), ['controller' => 'Courses', 'action' => 'adminIndex']); ?>
        &nbsp;/&nbsp;
        <?= $this->Html->link(h($course->title), ['controller' => 'Contents', 'action' => 'adminIndex', $course->id]); ?>
        &nbsp;/&nbsp;
        <?= empty($content->id) ? __('コンテンツ追加') : __('コンテンツ編集') ?>
    </div>
    <div class="ib-page-title">
        <?= empty($content->id) ? __('コンテンツ追加') : __('コンテンツ編集') ?>
    </div>
    <?= $this->Form->create($content); ?>
        <div class="form-group">
            <label><?= __('コンテンツ名') ?></label>
            <?= $this->Form->control('title', ['label' => false, 'class' => 'form-control', 'required' => true]); ?>
        </div>
        <div class="form-group">
            <label><?= __('コンテンツ種別') ?></label>
            <?= $this->Form->control('kind', [
                'label' => false,
                'type' => 'radio',
                'options' => \Cake\Core\Configure::read('content_kind_comment'),
                'separator' => '&nbsp;&nbsp;&nbsp;',
                'required' => false,
                'hiddenField' => false,
            ]); ?>
        </div>
        <div class="kind kind-movie kind-url kind-file form-group">
            <label><?= __('URL / ファイル名') ?></label>
            <?= $this->Form->control('url', ['label' => false, 'class' => 'form-control', 'id' => 'ContentUrl']); ?>
        </div>
        <div class="kind kind-file form-group">
            <label><?= __('表示ファイル名') ?></label>
            <?= $this->Form->control('file_name', ['label' => false, 'class' => 'form-control', 'id' => 'ContentFileName']); ?>
        </div>
        <div id="uploadFrameWrap" class="kind kind-file kind-movie form-group" style="display:none;">
            <label><?= __('ファイルアップロード') ?></label>
            <iframe id="uploadFrame" src="" frameborder="0" width="100%" height="180" style="border:1px solid #ddd; border-radius:4px; background:#fafafa;"></iframe>
        </div>
        <div class="kind kind-html kind-label form-group">
            <label><?= __('本文') ?></label>
            <?= $this->Form->control('body', ['label' => false, 'class' => 'form-control', 'type' => 'textarea', 'rows' => 10]); ?>
        </div>
        <div class="kind kind-test form-group">
            <label><?= __('合格ライン (%)') ?></label>
            <?= $this->Form->control('pass_rate', ['label' => false, 'class' => 'form-control', 'type' => 'number', 'min' => 0, 'max' => 100, 'default' => 60]); ?>
        </div>
        <div class="kind kind-test form-group">
            <label><?= __('出題数') ?></label>
            <?= $this->Form->control('question_count', ['label' => false, 'class' => 'form-control', 'type' => 'number', 'default' => 10]); ?>
        </div>
        <div class="kind kind-test form-group">
            <label><?= __('制限時間 (秒, 0で無制限)') ?></label>
            <?= $this->Form->control('timelimit', ['label' => false, 'class' => 'form-control', 'type' => 'number', 'default' => 0]); ?>
        </div>
        <div class="form-group">
            <label><?= __('ステータス') ?></label>
            <?= $this->Form->control('status', [
                'label' => false,
                'class' => 'form-control',
                'type' => 'select',
                'options' => \Cake\Core\Configure::read('content_status'),
            ]); ?>
        </div>
        <div class="form-group">
            <label><?= __('メモ') ?></label>
            <?= $this->Form->control('comment', ['label' => false, 'class' => 'form-control', 'type' => 'textarea', 'rows' => 3]); ?>
        </div>
        <div class="form-group">
            <?= $this->Form->hidden('course_id', ['value' => $course->id]); ?>
            <?= $this->Form->button(__('保存'), ['class' => 'btn btn-primary']); ?>
            <a href="<?= $this->Url->build(['action' => 'adminIndex', $course->id]) ?>" class="btn btn-default"><?= __('キャンセル') ?></a>
        </div>
    <?= $this->Form->end(); ?>
</div>