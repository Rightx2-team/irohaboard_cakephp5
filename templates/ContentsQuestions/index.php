<?php $this->start('css-embedded'); ?>
<style type='text/css'>
	<?php if($is_admin_record) { ?>
	.ib-navi-item { display: none; }
	.ib-logo a { pointer-events: none; }
	<?php }?>
</style>
<?php $this->end(); ?>

<?php $this->start('script-embedded'); ?>
<script>
	var TIMELIMIT_SEC	= parseInt('<?= (int)$content->timelimit ?>') * 60;
	var IS_RECORD		= '<?= $is_record ?>';
	var MSG_TIMELIMIT	= '<?= __('制限時間を過ぎましたので自動採点を行います。') ?>';
	var MSG_REST_TIME	= '<?= __('残り時間') ?>';
	var MSG_TIME		= '<?= __('経過') ?>';
</script>
<?= $this->Html->script('contents_questions.js?20220401');?>
<?php $this->end(); ?>

<div class="contents-questions-index">
	<div class="breadcrumb">
	<?php
	if ($is_admin_record) {
		$course_url = ['controller' => 'Contents', 'action' => 'adminRecord', $content->course_id, $record->user_id];
	} else {
		$course_url = ['controller' => 'Contents', 'action' => 'index', $content->course_id];
	}
	?>
	</div>
	<div id="lblStudySec" class="btn btn-info"></div>

	<!-- テスト結果ヘッダ表示 -->
	<?php if ($is_record): ?>
		<?php
			$result_color = ($record->is_passed == 1) ? 'text-primary' : 'text-danger';
			$result_label = ($record->is_passed == 1) ? __('合格') : __('不合格');
		?>
		<table class="result-table">
			<caption><?= __('テスト結果'); ?></caption>
			<tr>
				<td><?= __('合否'); ?></td>
				<td><div class="<?= $result_color; ?>"><?= $result_label; ?></div></td>
			</tr>
			<tr>
				<td><?= __('得点'); ?></td>
				<td><?= $record->score . ' / ' . $record->full_score; ?></td>
			</tr>
			<tr>
				<td><?= __('合格基準得点'); ?></td>
				<td><?= $record->pass_score ? $record->pass_score : __('設定されていません'); ?></td>
			</tr>
		</table>
	<?php endif; ?>

	<!-- 問題一覧 -->
	<?php
		$question_index = 1;

		// Create an array that can reference results by question ID as key. / 問題IDをキーに成績を参照できる配列を作成
		$question_records = [];
		if ($is_record) {
			foreach ($record->records_questions as $rec) {
				$question_records[$rec->question_id] = $rec;
			}
		}

		echo $this->Form->create(null);
	?>
		<?php foreach ($contentsQuestions as $contentsQuestion): ?>
			<?php
			$title       = $contentsQuestion->title;
			$body        = $contentsQuestion->body;
			$question_id = $contentsQuestion->id;

			$option_tag   = '';
			$option_index = 1;
			$option_list  = explode('|', $contentsQuestion->options ?? '');
			$correct_list = explode(',', $contentsQuestion->correct ?? '');
			$answer_list  = [];

			if (isset($question_records[$question_id])) {
				$answer_list = explode(',', $question_records[$question_id]->answer ?? '');
			}

			foreach ($option_list as $option) {
				$is_checked  = '';
				$is_disabled = $is_record ? 'disabled' : '';

				if (count($correct_list) > 1) {
					$is_checked = in_array($option_index, $answer_list) ? ' checked' : '';
					$option_tag .= sprintf('<input type="checkbox" value="%s" name="data[answer_%s][]" %s %s> %s<br>',
						$option_index, $question_id, $is_checked, $is_disabled, h($option));
				} else {
					if (count($answer_list) > 0) {
						$is_checked = ($answer_list[0] == $option_index) ? 'checked' : '';
					}
					$option_tag .= sprintf('<input type="radio" value="%s" name="data[answer_%s]" %s %s> %s<br>',
						$option_index, $question_id, $is_checked, $is_disabled, h($option));
				}
				$option_index++;
			}

			$explain_tag = '';
			$correct_tag = '';
			$result_tag  = '';
			$is_correct  = false;

			if ($is_record) {
				if (isset($question_records[$question_id]->is_correct)) {
					$is_correct = ($question_records[$question_id]->is_correct == '1');
				}

				$wrong_mode    = $content->wrong_mode;
				$correct_label = '';
				foreach ($correct_list as $correct_no) {
					$correct_label .= ($correct_label == '') ? ($option_list[$correct_no - 1] ?? '') : ', ' . ($option_list[$correct_no - 1] ?? '');
				}

				if ($is_correct) {
					$result_tag  = sprintf('<p>%s<span class="result-currect">%s</span></p>', $this->Html->image('correct.png', ['width' => '60', 'height' => '60']), __('正解'));
					$explain_tag = getExplain($contentsQuestion->explain ?? '');
				} else {
					$result_tag = sprintf('<p>%s<span class="result-wrong">%s</span></p>', $this->Html->image('wrong.png', ['width' => '60', 'height' => '60']), __('不正解'));
					switch ($wrong_mode) {
						case 0:
							break;
						case 1:
							$correct_tag = sprintf('<p class="correct-text bg-success">%s : %s</p>', __('正解'), $correct_label);
							$explain_tag = getExplain($contentsQuestion->explain ?? '');
							break;
						case 2:
							$explain_tag = getExplain($contentsQuestion->explain ?? '');
							break;
					}
				}
			}
			?>
			<div class="panel panel-info question question-<?= $question_index; ?>">
				<div class="panel-heading"><?= __('問') . $question_index; ?></div>
				<div class="panel-body">
					<h4><?= h($title) ?></h4>
					<div class="question-text bg-warning">
						<?= $body ?>
					</div>
					<div class="radio-group">
						<?= $option_tag; ?>
					</div>
					<?= $result_tag ?>
					<?= $correct_tag ?>
					<?= $explain_tag ?>
				</div>
			</div>
			<?php $question_index++; ?>
		<?php endforeach; ?>

		<?php
			echo '<div class="form-inline"><!--start-->';
			if (!$is_record) {
				echo $this->Form->hidden('study_sec');
				echo '<input type="button" value="' . __('採点') . '" class="btn btn-primary btn-lg btn-score" onclick="$(\'#confirmModal\').modal()">';
				echo '&nbsp;';
			}
			echo '<input type="button" value="' . __('戻る') . '" class="btn btn-default btn-lg" onclick="location.href=\'' . $this->Url->build($course_url) . '\'">';
			echo '</div><!--end-->';
			echo $this->Form->end();
		?>
	<br>
</div>

<?php
function getExplain($explain)
{
	$check = str_replace(['<p>', '</p>', '<br>'], '', $explain);
	if ($check === '') return '';
	return sprintf('<div class="correct-text bg-danger">%s : %s</div>', __('解説'), $explain);
}
?>

<!--採点確認ダイアログ-->
<div class="modal fade" id="confirmModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title"><?= __('採点確認'); ?></h4>
			</div>
			<div class="modal-body">
				<p><?= __('採点してよろしいですか？'); ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?= __('キャンセル'); ?></button>
				<button type="button" class="btn btn-primary btn-score" onclick="sendData();"><?= __('採点'); ?></button>
			</div>
		</div>
	</div>
</div>
