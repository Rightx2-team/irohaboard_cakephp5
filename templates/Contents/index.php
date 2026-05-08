<?php
$is_admin_record = method_exists($this, 'isAdminPage') && $this->isAdminPage() && method_exists($this, 'isRecordPage') && $this->isRecordPage();
?>
<?php $this->start('css-embedded'); ?>
<style>
@media only screen and (max-width:800px) {
    .responsive-table tbody td:nth-of-type(2):before { width: 100px; display: inline-block; content: "<?= __('種別') . ' : ' ?>"; }
    .responsive-table tbody td:nth-of-type(3):before { content: "<?= __('学習開始日') . ' : ' ?>"; }
    .responsive-table tbody td:nth-of-type(4):before { content: "<?= __('前回学習日') . ' : ' ?>"; }
    .responsive-table tbody td:nth-of-type(5):before { content: "<?= __('学習時間') . ' : ' ?>"; }
    .responsive-table tbody td:nth-of-type(6):before { content: "<?= __('学習回数') . ' : ' ?>"; }
    .responsive-table tbody td:nth-of-type(7):before { content: "<?= __('理解度') . ' : ' ?>"; }
    .responsive-table tbody td:nth-of-type(8):before { content: "<?= __('完了') . ' : ' ?>"; }
}
<?php if ($is_admin_record): ?>
.ib-navi-item { display: none; }
.ib-logo a { pointer-events: none; }
<?php endif; ?>
</style>
<?php $this->end(); ?>

<div class="contents-index">
    <div class="breadcrumb">
        <?php if (!$is_admin_record): ?>
            <?= $this->Html->link('<span class="glyphicon glyphicon-home" aria-hidden="true"></span> HOME', ['controller' => 'UsersCourses', 'action' => 'index'], ['escape' => false]) ?>
        <?php endif; ?>
    </div>

    <div class="panel panel-info">
        <?php
            // $course はエンティティ形式（Courses Table->get() が返す）
            $courseTitle = is_array($course) ? ($course['title'] ?? '') : ($course->title ?? '');
            $courseIntro = is_array($course) ? ($course['introduction'] ?? '') : ($course->introduction ?? '');
        ?>
        <div class="panel-heading"><?= h($courseTitle) ?></div>
        <div class="panel-body">
            <?php if (!empty($courseIntro)): ?>
            <div class="well">
                <?php
                    $introduction = $this->Text->autoLinkUrls($courseIntro, ['target' => '_blank']);
                    echo nl2br($introduction);
                ?>
            </div>
            <?php endif; ?>

            <table class="responsive-table">
                <thead>
                    <tr>
                        <th><?= __('コンテンツ名') ?></th>
                        <th class="ib-col-center"><?= __('種別') ?></th>
                        <th class="ib-col-date"><?= __('学習開始日') ?></th>
                        <th class="ib-col-date"><?= __('前回学習日') ?></th>
                        <th nowrap class="ib-col-center"><?= __('学習時間') ?></th>
                        <th nowrap class="ib-col-center"><?= __('学習回数') ?></th>
                        <th nowrap class="ib-col-center"><?= __('理解度') ?></th>
                        <th nowrap class="ib-col-center"><?= __('完了') ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($contents as $c): ?>
                <?php
                    // $c は rawQuery の結果で、フラットな連想配列
                    $c_id     = $c['id'] ?? 0;
                    $c_kind   = $c['kind'] ?? '';
                    $c_title  = $c['title'] ?? '';
                    $c_url    = $c['url'] ?? '';
                    $c_status = $c['status'] ?? 0;

                    $first_date   = $c['first_date']   ?? null;
                    $last_date    = $c['last_date']    ?? null;
                    $record_id    = $c['record_id']    ?? null;
                    $study_sec    = $c['study_sec']    ?? 0;
                    $study_count  = $c['study_count']  ?? 0;
                    $understanding_val = $c['understanding'] ?? null;
                    $is_passed    = $c['is_passed']    ?? null;
                    $is_complete  = $c['is_complete']  ?? 0;

                    $icon = '';
                    $title_link = '';
                    $kind = \Cake\Core\Configure::read('content_kind.' . $c_kind);
                    $understanding = '';

                    switch ($c_kind) {
                        case 'test':
                            $icon = 'glyphicon glyphicon-check text-danger';
                            $title_link = $this->Html->link($c_title, ['controller' => 'ContentsQuestions', 'action' => 'index', $c_id]);
                            if ($record_id) {
                                $result = \Cake\Core\Configure::read('record_result.' . $is_passed);
                                $understanding = $this->Html->link($result ?: '', ['controller' => 'ContentsQuestions', 'action' => 'record', $c_id, $record_id]);
                            }
                            break;
                        case 'enquete':
                            $icon = 'glyphicon glyphicon-check text-danger';
                            if ($record_id) {
                                $understanding = $this->Html->link(__('回答'), ['controller' => 'EnquetesQuestions', 'action' => 'record', $c_id, $record_id]);
                            }
                            $title_link = $this->Html->link($c_title, ['controller' => 'EnquetesQuestions', 'action' => 'index', $c_id]);
                            break;
                        case 'file':
                            $icon = 'glyphicon glyphicon-file text-success';
                            if (strpos($c_url, 'http') === 0) {
                                $title_link = $this->Html->link($c_title, $c_url, ['target' => '_blank']);
                            } else {
                                $title_link = $this->Html->link($c_title, ['controller' => 'Contents', 'action' => 'fileDownload', $c_id], ['target' => '_blank']);
                            }
                            break;
                        default:
                            $icon = 'glyphicon glyphicon-play-circle text-info';
                            $title_link = $this->Html->link($c_title, ['controller' => 'Contents', 'action' => 'view', $c_id]);
                            $kind = __('学習');
                            if ($understanding_val !== null) {
                                $understanding = h(\Cake\Core\Configure::read('record_understanding.' . $understanding_val));
                            }
                            break;
                    }

                    if ($is_admin_record) {
                        $title_link = h($c_title);
                    }

                    if ($c_status == 0) {
                        $title_link .= ' <span class="status-closed">(' . __('非公開') . ')</span>';
                    }
                ?>
                <?php if ($c_kind === 'label'): ?>
                    <tr>
                        <td colspan="8" class="content-label"><?= h($c_title) ?>&nbsp;</td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td><span class="<?= $icon ?>"></span>&nbsp;<?= $title_link ?>&nbsp;</td>
                        <td class="ib-col-center" nowrap><?= h($kind) ?>&nbsp;</td>
                        <td class="ib-col-date"><?= h($first_date) ?>&nbsp;</td>
                        <td class="ib-col-date"><?= h($last_date) ?>&nbsp;</td>
                        <td class="ib-col-center"><?= h($study_sec) ?>&nbsp;</td>
                        <td class="ib-col-center"><?= h($study_count) ?>&nbsp;</td>
                        <td nowrap class="ib-col-center"><?= $understanding ?></td>
                        <td class="ib-col-center"><?= ($is_complete == 1) ? '<span class="glyphicon glyphicon-ok text-muted"></span>' : '' ?></td>
                    </tr>
                <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>