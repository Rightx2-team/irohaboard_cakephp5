<div class="users-login">
    <div class="panel panel-info form-signin">
        <div class="panel-heading">
            <?= __('受講者ログイン') ?>
        </div>
        <div class="panel-body">
            <?php if (\Cake\Core\Configure::read('show_admin_link')): ?>
            <div class="text-right">
                <a href="<?= $this->Url->build('/admin/users/login') ?>"><?= __('管理者ログインへ') ?></a>
            </div>
            <?php endif; ?>
            <?= $this->Flash->render(); ?>
            <?= $this->Form->create(null); ?>
                <div class="form-group">
                    <?= $this->Form->control('username', ['label' => __('ログインID'), 'class' => 'form-control', 'value' => $username ?? '']); ?>
                </div>
                <div class="form-group">
                    <?= $this->Form->control('password', ['label' => __('パスワード'), 'class' => 'form-control', 'value' => $password ?? '']); ?>
                </div>
                <?= $this->Form->button(__('ログイン'), ['class' => 'btn btn-lg btn-primary btn-block']); ?>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
