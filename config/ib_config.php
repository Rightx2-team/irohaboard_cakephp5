<?php
use Cake\Core\Configure;

Configure::write('group_status',   ['1' => 'public', '0' => 'private']);
Configure::write('course_status',  ['1' => 'enabled', '0' => 'disabled']);
Configure::write('content_status', ['1' => 'public', '0' => 'private']);

Configure::write('content_kind', [
    'label'   => 'ラベル',
    'html'    => 'HTML',
    'movie'   => '動画',
    'url'     => 'URL',
    'file'    => '配布資料',
    'test'    => 'テスト',
    'enquete' => 'アンケート',
]);

Configure::write('content_kind_comment', [
    'label'   => 'ラベル(章題)',
    'html'    => 'HTML',
    'movie'   => '動画',
    'url'     => 'URL',
    'file'    => '配布資料',
    'test'    => 'テスト',
    'enquete' => 'アンケート',
]);

Configure::write('content_status', [
    '1' => '公開',
    '0' => '非公開',
]);

Configure::write('content_category', [
    'study' => '学習',
    'test'  => 'テスト',
]);

Configure::write('record_result', [
    '-1' => '',
    '1'  => '合格',
    '0'  => '不合格',
    '2'  => '回答済',
]);

Configure::write('record_understanding', [
    '0' => '中断',
    '1' => '終了',
    '2' => '×',
    '3' => '△',
    '4' => '〇',
    '5' => '◎',
]);

Configure::write('record_understanding_pc', [
    '2' => '理解できなかった',
    '3' => 'あまり理解できなかった',
    '4' => 'だいたい理解できた',
    '5' => 'よく理解できた',
]);

Configure::write('record_understanding_spn', [
    '2' => '×',
    '3' => '△',
    '4' => '〇',
    '5' => '◎',
]);

Configure::write('user_role', [
    'admin' => '管理者',
    'user'  => '一般ユーザ',
]);

Configure::write('content_category', ['study' => 'study', 'test' => 'test']);
Configure::write('question_type',    ['single' => 'single', 'text' => 'text']);
Configure::write('wrong_mode', ['0' => 'no_display', '1' => 'show_correct', '2' => 'explain_only']);

Configure::write('record_result',        ['-1' => '', '1' => 'pass', '0' => 'fail', '2' => 'answered']);
Configure::write('record_complete',      ['1' => 'done', '0' => 'not_done']);
Configure::write('is_correct',           ['1' => 'correct', '0' => 'wrong']);






Configure::write('user_role', ['admin' => 'admin', 'user' => 'user']);

Configure::write('upload_extensions', [
    '.png', '.gif', '.jpg', '.jpeg', '.pdf', '.zip',
    '.ppt', '.pptx', '.pps', '.ppsx',
    '.doc', '.docx', '.xls', '.xlsx', '.txt',
    '.mov', '.mp4', '.wmv', '.asx', '.mp3', '.wma', '.m4a',
]);
Configure::write('upload_image_extensions', ['.png', '.gif', '.jpg', '.jpeg']);
Configure::write('upload_movie_extensions', ['.mov', '.mp4', '.wmv', '.asx']);

Configure::write('upload_maxsize',       1024 * 1024 * 100);
Configure::write('upload_image_maxsize', 1024 * 1024 *  2);
Configure::write('upload_movie_maxsize', 1024 * 1024 * 500);

Configure::write('close_on_select',       true);
Configure::write('use_upload_image',      true);
Configure::write('show_admin_link',       false);
Configure::write('open_link_same_window', false);

Configure::write('demo_mode',     false);
Configure::write('demo_login_id', 'demo001');
Configure::write('demo_password', 'pass');

Configure::write('form_defaults',        ['class' => 'form-horizontal']);
Configure::write('form_submit_defaults', ['class' => 'btn btn-primary']);
Configure::write('form_submit_before',   '<div class="form-group"><div class="col col-sm-9 col-sm-offset-3">');
Configure::write('form_submit_after',    '</div></div>');

Configure::write('theme_colors', [
    '#337ab7' => 'default',
    '#003f8e' => 'ink blue',
    '#4169e1' => 'royal blue',
    '#006888' => 'marine blue',
    '#00bfff' => 'deep sky blue',
    '#483d8b' => 'dark slate blue',
    '#00a960' => 'green',
    '#006948' => 'holly green',
    '#288c66' => 'forest green',
    '#556b2f' => 'dark olive green',
    '#8b0000' => 'dark red',
    '#d84450' => 'poppy red',
    '#c71585' => 'medium violet red',
    '#a52a2a' => 'brown',
    '#ee7800' => 'orange',
    '#fcc800' => 'chrome yellow',
    '#7d7d7d' => 'gray',
    '#696969' => 'dim gray',
    '#2f4f4f' => 'dark slate gray',
    '#000000' => 'black',
]);

Configure::write('import_group_count',  10);
Configure::write('import_course_count', 20);

if (!defined('APP_NAME')) {
    define('APP_NAME', 'iroha Board');
}