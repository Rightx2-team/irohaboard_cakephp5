<div class="users-courses-index">
    <div class="panel panel-success">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
            <?= __('お知らせ') ?>
        </div>
        <div class="panel-body">
            <?php if (!empty($info)): ?>
            <div class="well">
                <?= nl2br(h($info)) ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($infos) && count($infos) > 0): ?>
            <table>
            <tbody>
            <?php foreach ($infos as $info_item): ?>
            <?php
                // Compatible with both entity and array formats. / エンティティ/配列の両対応
                $created = is_array($info_item) ? ($info_item['created'] ?? '') : ($info_item->created ?? '');
                $title   = is_array($info_item) ? ($info_item['title']   ?? '') : ($info_item->title   ?? '');
                $id      = is_array($info_item) ? ($info_item['id']      ?? 0)  : ($info_item->id      ?? 0);
            ?>
            <tr>
                <td width="100" valign="top"><?= h($created) ?></td>
                <td><?= $this->Html->link(h($title), ['controller' => 'Infos', 'action' => 'view', $id]) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
            <div class="text-right"><?= $this->Html->link(__('一覧を表示'), ['controller' => 'Infos', 'action' => 'index']) ?></div>
            <?php endif; ?>

            <?= $no_info ?? '' ?>
        </div>
    </div>

    <div class="panel panel-info">
        <div class="panel-heading">
            <span class="glyphicon glyphicon-book" aria-hidden="true"></span>
            <?= __('コース一覧') ?>
        </div>
        <div class="panel-body">
            <ul class="list-group">
            <?php foreach ($courses as $course): ?>
            <?php
                // Compatible with the array format returned by rawQuery. / rawQuery で返される配列形式に対応
                $course_id    = is_array($course) ? ($course['id']    ?? 0)  : ($course->id    ?? 0);
                $course_title = is_array($course) ? ($course['title'] ?? '') : ($course->title ?? '');
                $left_cnt     = is_array($course) ? ($course['left_cnt']   ?? null) : ($course->left_cnt   ?? null);
                $first_date   = is_array($course) ? ($course['first_date'] ?? null) : ($course->first_date ?? null);
                $last_date    = is_array($course) ? ($course['last_date']  ?? null) : ($course->last_date  ?? null);
            ?>
                <a href="<?= $this->Url->build(['controller' => 'Contents', 'action' => 'index', $course_id]) ?>" class="list-group-item">
                    <h4 class="list-group-item-heading"><?= h($course_title) ?></h4>
                    <?php if ($first_date || $last_date): ?>
                    <small>
                        <?php if ($first_date): ?>
                            <?= __('開始') ?>: <?= h($first_date) ?>&nbsp;
                        <?php endif; ?>
                        <?php if ($last_date): ?>
                            <?= __('最終') ?>: <?= h($last_date) ?>&nbsp;
                        <?php endif; ?>
                        <?php if ($left_cnt !== null): ?>
                            <?= __('未完了') ?>: <?= h($left_cnt) ?>
                        <?php endif; ?>
                    </small>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
            <?= $no_record ?? '' ?>
            </ul>
        </div>
    </div>
</div>