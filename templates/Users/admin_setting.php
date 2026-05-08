<?= $this->element('admin_menu');?>
<div class="admin-users-setting">
	<div class="panel panel-default">
		<div class="panel-heading">
			<?= __('設定')?>
		</div>
		<div class="panel-body">
		<?php
			echo $this->Form->create(null, \Cake\Core\Configure::read('form_defaults'));
			echo $this->Form->control('User.new_password', [
				'label' => __('新しいパスワード'),
				'type' => 'password',
				'autocomplete' => 'new-password'
			]);
			echo $this->Form->control('User.new_password2', [
				'label' => __('新しいパスワード (確認用)'),
				'type' => 'password',
				'autocomplete' => 'new-password'
			]);
			echo \Cake\Core\Configure::read('form_submit_before')
				.$this->Form->submit(__('保存'), \Cake\Core\Configure::read('form_submit_defaults'))
				.\Cake\Core\Configure::read('form_submit_after');
			echo $this->Form->end();
		?>
		</div>
	</div>
</div>