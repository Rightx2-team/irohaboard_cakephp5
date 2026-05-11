<?= $this->element('admin_menu'); ?>
<style>
    .admin-users-edit {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    .admin-users-edit .form-group {
        margin-bottom: 15px;
    }
    .admin-users-edit .form-group > label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .admin-users-edit .checkbox-group {
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #fafafa;
        padding: 12px 16px;
        box-sizing: border-box;
    }
    .admin-users-edit .checkbox-group .checkbox-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }
    .admin-users-edit .checkbox-group .checkbox {
        display: flex;
        align-items: center;
        min-width: 200px;
        padding: 2px 0;
    }
    .admin-users-edit .checkbox-group input[type="checkbox"] {
        flex-shrink: 0;
        margin: 0 8px 0 0;
        width: 16px;
        height: 16px;
    }
    .admin-users-edit .checkbox-group label {
        margin: 0;
        font-weight: normal;
        cursor: pointer;
        line-height: 1.4;
    }
    .admin-users-edit .form-control {
        max-width: 100%;
    }
</style>
<div class="admin-users-edit">
    <div class="ib-page-title">
        <?= empty($user->id) ? __('ユーザ追加') : __('ユーザ編集') ?>
    </div>
    <?= $this->Form->create($user); ?>
        <div class="form-group">
            <label><?= __('ログインID') ?></label>
            <?= $this->Form->control('username', ['label' => false, 'class' => 'form-control', 'required' => true]); ?>
        </div>
        <div class="form-group">
            <label><?= __('氏名') ?></label>
            <?= $this->Form->control('name', ['label' => false, 'class' => 'form-control', 'required' => true]); ?>
        </div>
        <div class="form-group">
            <label><?= __('権限') ?></label>
            <?= $this->Form->control('role', [
                'label' => false,
                'class' => 'form-control',
                'type' => 'select',
                'options' => ['user' => __('一般ユーザ'), 'admin' => __('管理者')],
            ]); ?>
        </div>
        <div class="form-group">
            <label><?= __('認証方式') ?> / Authentication Method</label>
            <select name="auth_type" id="auth_type" class="form-control" style="max-width:300px;" onchange="togglePasswordFields()">
                <option value="local" <?= ($user->auth_type ?? 'local') === 'local' ? 'selected' : '' ?>>
                    <?= __('ローカル') ?> (Local DB)
                </option>
                <option value="ldap" <?= ($user->auth_type ?? '') === 'ldap' ? 'selected' : '' ?>>
                    AD / LDAP (daitetsu.local)
                </option>
            </select>
            <small class="text-muted">AD / <?= __('ローカル') ?></small>
        </div>
        <div class="form-group">
            <label><?= __('メールアドレス') ?></label>
            <?= $this->Form->control('email', ['label' => false, 'class' => 'form-control', 'type' => 'email']); ?>
        </div>
        <div class="form-group">
            <label><?= __('コメント') ?></label>
            <?= $this->Form->control('comment', ['label' => false, 'class' => 'form-control', 'type' => 'textarea', 'rows' => 3]); ?>
        </div>
        <?php
            $selectedGroups  = !empty($user->groups)  ? array_map(fn($g) => $g->id, $user->groups)  : [];
            $selectedCourses = !empty($user->courses) ? array_map(fn($c) => $c->id, $user->courses) : [];
        ?>
        <div class="form-group">
            <label><?= __('所属グループ') ?></label>
            <div class="checkbox-group">
                <?php if (empty($groups)): ?>
                    <span style="color:#888;"><?= __('グループがまだ登録されていません') ?></span>
                <?php else: ?>
                    <div class="checkbox-row">
                        <?php foreach ($groups as $id => $title): ?>
                            <div class="checkbox">
                                <input type="checkbox" name="groups[_ids][]" value="<?= h($id) ?>" id="group-<?= h($id) ?>"
                                    <?= in_array($id, $selectedGroups) ? 'checked' : '' ?>>
                                <label for="group-<?= h($id) ?>"><?= h($title) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="form-group">
            <label><?= __('受講コース') ?></label>
            <div class="checkbox-group">
                <?php if (empty($courses)): ?>
                    <span style="color:#888;"><?= __('コースがまだ登録されていません') ?></span>
                <?php else: ?>
                    <div class="checkbox-row">
                        <?php foreach ($courses as $id => $title): ?>
                            <div class="checkbox">
                                <input type="checkbox" name="courses[_ids][]" value="<?= h($id) ?>" id="course-<?= h($id) ?>"
                                    <?= in_array($id, $selectedCourses) ? 'checked' : '' ?>>
                                <label for="course-<?= h($id) ?>"><?= h($title) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div id="password-fields">
            <div class="form-group">
                <label><?= empty($user->id) ? __('パスワード') : __('新しいパスワード (変更する場合のみ)') ?></label>
                <?= $this->Form->control('new_password', ['label' => false, 'class' => 'form-control', 'type' => 'password', 'autocomplete' => 'new-password', 'value' => '']); ?>
            </div>
            <div class="form-group">
                <label><?= __('パスワード (確認用)') ?></label>
                <?= $this->Form->control('new_password2', ['label' => false, 'class' => 'form-control', 'type' => 'password', 'autocomplete' => 'new-password', 'value' => '']); ?>
            </div>
        </div>
        <script>
        function togglePasswordFields() {
            var authType = document.getElementById('auth_type').value;
            var pwFields = document.getElementById('password-fields');
            pwFields.style.display = (authType === 'ldap') ? 'none' : '';
        }
        togglePasswordFields();
        </script>
        <div class="form-group">
            <?= $this->Form->button(__('保存'), ['class' => 'btn btn-primary']); ?>
            <a href="<?= $this->Url->build(['action' => 'adminIndex']) ?>" class="btn btn-default"><?= __('キャンセル') ?></a>
        </div>
    <?= $this->Form->end(); ?>
</div>