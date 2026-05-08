<?php
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes): void {
    $routes->setRouteClass(DashedRoute::class);

    // ===== 管理画面 /admin/* を App\Controller\Xxx::adminYyy にマッピング =====
    // プレフィックスを使わず、URLベースでマッピング

    // Users（管理）- /admin/users を先に定義してページャの逆引きURLを正しく生成させる
    $routes->connect('/admin/users',               ['controller' => 'Users', 'action' => 'adminIndex']);
    $routes->connect('/admin',                     ['controller' => 'Users', 'action' => 'adminIndex']);
    $routes->connect('/admin/',                    ['controller' => 'Users', 'action' => 'adminIndex']);
    $routes->connect('/admin/login',               ['controller' => 'Users', 'action' => 'adminLogin']);
    $routes->connect('/admin/users/login',         ['controller' => 'Users', 'action' => 'adminLogin']);
    $routes->connect('/admin/users/logout',        ['controller' => 'Users', 'action' => 'adminLogout']);
    $routes->connect('/admin/users/add',           ['controller' => 'Users', 'action' => 'adminAdd']);
    $routes->connect('/admin/users/edit/{id}',     ['controller' => 'Users', 'action' => 'adminEdit'],   ['pass' => ['id']]);
    $routes->connect('/admin/users/delete/{id}',   ['controller' => 'Users', 'action' => 'adminDelete'], ['pass' => ['id']]);
    $routes->connect('/admin/users/clear/{id}',    ['controller' => 'Users', 'action' => 'adminClear'],  ['pass' => ['id']]);
    $routes->connect('/admin/users/import',        ['controller' => 'Users', 'action' => 'adminImport']);
    $routes->connect('/admin/users/setting',       ['controller' => 'Users', 'action' => 'adminSetting']);

    // Groups
    $routes->connect('/admin/groups',              ['controller' => 'Groups', 'action' => 'adminIndex']);
    $routes->connect('/admin/groups/add',          ['controller' => 'Groups', 'action' => 'adminAdd']);
    $routes->connect('/admin/groups/edit/{id}',    ['controller' => 'Groups', 'action' => 'adminEdit'],   ['pass' => ['id']]);
    $routes->connect('/admin/groups/delete/{id}',  ['controller' => 'Groups', 'action' => 'adminDelete'], ['pass' => ['id']]);

    // Courses
    $routes->connect('/admin/courses',             ['controller' => 'Courses', 'action' => 'adminIndex']);
    $routes->connect('/admin/courses/add',         ['controller' => 'Courses', 'action' => 'adminAdd']);
    $routes->connect('/admin/courses/edit/{id}',   ['controller' => 'Courses', 'action' => 'adminEdit'],   ['pass' => ['id']]);
    $routes->connect('/admin/courses/delete/{id}', ['controller' => 'Courses', 'action' => 'adminDelete'], ['pass' => ['id']]);
    $routes->connect('/admin/courses/order',       ['controller' => 'Courses', 'action' => 'adminOrder']);

    // Contents / アップロード（動的ルートより前に配置）
    $routes->connect('/admin/contents/upload/{file_type}',            ['controller' => 'Contents', 'action' => 'adminUpload'],  ['pass' => ['file_type']]);
    $routes->connect('/admin/contents/upload_image',                  ['controller' => 'Contents', 'action' => 'adminUploadImage']);
    $routes->connect('/contents/file_download/{content_id}',          ['controller' => 'Contents', 'action' => 'fileDownload'], ['pass' => ['content_id']]);
    $routes->connect('/contents/file_movie/{content_id}',             ['controller' => 'Contents', 'action' => 'fileMovie'],    ['pass' => ['content_id']]);
    $routes->connect('/contents/file_image/{file_name}',              ['controller' => 'Contents', 'action' => 'fileImage'],    ['pass' => ['file_name']]);

    // Contents
    $routes->connect('/admin/contents/add/{course_id}',               ['controller' => 'Contents', 'action' => 'adminAdd'],    ['pass' => ['course_id']]);
    $routes->connect('/admin/contents/edit/{course_id}/{content_id}', ['controller' => 'Contents', 'action' => 'adminEdit'],   ['pass' => ['course_id', 'content_id']]);
    $routes->connect('/admin/contents/delete/{content_id}',           ['controller' => 'Contents', 'action' => 'adminDelete'], ['pass' => ['content_id']]);
    $routes->connect('/admin/contents/{course_id}',                   ['controller' => 'Contents', 'action' => 'adminIndex'],  ['pass' => ['course_id'], 'course_id' => '\d+']);

    // Records
    $routes->connect('/admin/records', ['controller' => 'Records', 'action' => 'adminIndex']);

    // Infos
    $routes->connect('/admin/infos',             ['controller' => 'Infos', 'action' => 'adminIndex']);
    $routes->connect('/admin/infos/add',         ['controller' => 'Infos', 'action' => 'adminAdd']);
    $routes->connect('/admin/infos/edit/{id}',   ['controller' => 'Infos', 'action' => 'adminEdit'],   ['pass' => ['id']]);
    $routes->connect('/admin/infos/delete/{id}', ['controller' => 'Infos', 'action' => 'adminDelete'], ['pass' => ['id']]);

    // Settings
    $routes->connect('/admin/settings', ['controller' => 'Settings', 'action' => 'adminIndex']);

    // ===== 受講者画面 =====
    $routes->connect('/', ['controller' => 'UsersCourses', 'action' => 'index']);
    $routes->connect('/users/login',   ['controller' => 'Users', 'action' => 'login']);
    $routes->connect('/users/logout',  ['controller' => 'Users', 'action' => 'logout']);
    $routes->connect('/users/setting', ['controller' => 'Users', 'action' => 'setting']);
    $routes->connect('/users_courses', ['controller' => 'UsersCourses', 'action' => 'index']);

    // ContentsQuestions (テスト)
    $routes->connect('/contents_questions/{content_id}',                          ['controller' => 'ContentsQuestions', 'action' => 'index'],       ['pass' => ['content_id']]);
    $routes->connect('/contents_questions/{content_id}/{record_id}',              ['controller' => 'ContentsQuestions', 'action' => 'index'],       ['pass' => ['content_id', 'record_id']]);
    $routes->connect('/admin/contents_questions/{content_id}',                    ['controller' => 'ContentsQuestions', 'action' => 'adminIndex'],  ['pass' => ['content_id']]);
    $routes->connect('/admin/contents_questions/add/{content_id}',                ['controller' => 'ContentsQuestions', 'action' => 'adminAdd'],    ['pass' => ['content_id']]);
    $routes->connect('/admin/contents_questions/edit/{content_id}',               ['controller' => 'ContentsQuestions', 'action' => 'adminEdit'],   ['pass' => ['content_id']]);
    $routes->connect('/admin/contents_questions/edit/{content_id}/{question_id}', ['controller' => 'ContentsQuestions', 'action' => 'adminEdit'],   ['pass' => ['content_id', 'question_id']]);
    $routes->connect('/admin/contents_questions/delete/{question_id}',            ['controller' => 'ContentsQuestions', 'action' => 'adminDelete'], ['pass' => ['question_id']]);
    $routes->connect('/admin/contents_questions/order',                           ['controller' => 'ContentsQuestions', 'action' => 'adminOrder']);
    $routes->connect('/admin/contents_questions/record/{content_id}/{record_id}', ['controller' => 'ContentsQuestions', 'action' => 'adminRecord'], ['pass' => ['content_id', 'record_id']]);

    // EnquetesQuestions (アンケート)
    $routes->connect('/enquetes_questions/{content_id}',                          ['controller' => 'EnquetesQuestions', 'action' => 'index'],       ['pass' => ['content_id']]);
    $routes->connect('/enquetes_questions/{content_id}/{record_id}',              ['controller' => 'EnquetesQuestions', 'action' => 'index'],       ['pass' => ['content_id', 'record_id']]);
    $routes->connect('/admin/enquetes_questions/{content_id}',                    ['controller' => 'EnquetesQuestions', 'action' => 'adminIndex'],  ['pass' => ['content_id']]);
    $routes->connect('/admin/enquetes_questions/add/{content_id}',                ['controller' => 'EnquetesQuestions', 'action' => 'adminAdd'],    ['pass' => ['content_id']]);
    $routes->connect('/admin/enquetes_questions/edit/{content_id}',               ['controller' => 'EnquetesQuestions', 'action' => 'adminEdit'],   ['pass' => ['content_id']]);
    $routes->connect('/admin/enquetes_questions/edit/{content_id}/{question_id}', ['controller' => 'EnquetesQuestions', 'action' => 'adminEdit'],   ['pass' => ['content_id', 'question_id']]);
    $routes->connect('/admin/enquetes_questions/delete/{question_id}',            ['controller' => 'EnquetesQuestions', 'action' => 'adminDelete'], ['pass' => ['question_id']]);
    $routes->connect('/admin/enquetes_questions/order',                           ['controller' => 'EnquetesQuestions', 'action' => 'adminOrder']);

    // Records (採点結果保存)
    $routes->connect('/records/add/{content_id}', ['controller' => 'Records', 'action' => 'add'], ['pass' => ['content_id']]);

    // 言語切替
    $routes->connect('/language/{lang}', ['controller' => 'Language', 'action' => 'switch'], ['pass' => ['lang']]);

    // Install / Update
    $routes->connect('/install',          ['controller' => 'Install', 'action' => 'index']);
    $routes->connect('/install/complete', ['controller' => 'Install', 'action' => 'complete']);
    $routes->connect('/install/error',    ['controller' => 'Install', 'action' => 'error']);
    $routes->connect('/update',           ['controller' => 'Update',  'action' => 'index']);

    // Contents / 学習履歴詳細（管理者ポップアップ）
    $routes->connect('/admin/contents/record/{course_id}/{user_id}', ['controller' => 'Contents', 'action' => 'adminRecord'], ['pass' => ['course_id', 'user_id']]);

    $routes->fallbacks(DashedRoute::class);
};