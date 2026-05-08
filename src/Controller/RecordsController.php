<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\Core\Configure;
use Cake\Http\Response;

class RecordsController extends AppController
{
    public function adminIndex(): ?Response
    {
        $recordsTable = $this->fetchTable('Records');

        // 検索条件
        $conditions = [];

        $course_id        = $this->request->getQuery('course_id', '');
        $content_title    = $this->request->getQuery('content_title', '');
        $username         = $this->request->getQuery('username', '');
        $name             = $this->request->getQuery('name', '');
        $group_id         = $this->request->getQuery('group_id', '');
        $content_category = $this->request->getQuery('content_category', '');

        if ($course_id !== '') {
            $conditions['Records.course_id'] = $course_id;
        }
        if ($content_title !== '') {
            $conditions['Contents.title LIKE'] = '%' . $content_title . '%';
        }
        if ($username !== '') {
            $conditions['Users.username LIKE'] = '%' . $username . '%';
        }
        if ($name !== '') {
            $conditions['Users.name LIKE'] = '%' . $name . '%';
        }
        if ($content_category === 'study') {
            $conditions['Contents.kind IN'] = ['text', 'html', 'movie', 'url'];
        } elseif ($content_category !== '') {
            $conditions['Contents.kind'] = $content_category;
        }

        // 日付範囲の安全な取得
        $from_date = $this->resolveDate($this->request->getQuery('from_date'), date('Y-m-d', strtotime('-1 month')));
        $to_date   = $this->resolveDate($this->request->getQuery('to_date'),   date('Y-m-d'));

        $conditions['Records.created >='] = $from_date . ' 00:00:00';
        $conditions['Records.created <='] = $to_date . ' 23:59:59';

        // 通常の一覧表示
        $query = $recordsTable->find()
            ->contain(['Users', 'Courses', 'Contents'])
            ->where($conditions)
            ->orderBy(['Records.created' => 'DESC']);

        $records = $this->paginate($query, ['limit' => 20]);

        $groups  = $this->fetchTable('Groups')->find('list', keyField: 'id', valueField: 'title')->toArray();
        $courses = $this->fetchTable('Courses')->find('list', keyField: 'id', valueField: 'title')->toArray();

        $this->set(compact('records', 'groups', 'group_id', 'courses', 'content_category', 'from_date', 'to_date'));
        return null;
    }

    /**
     * クエリパラメータを日付文字列に解決する
     */
    private function resolveDate($raw, string $default): string
    {
        if (is_array($raw) && !empty($raw)) {
            $str = implode('-', array_filter($raw));
            return strtotime($str) !== false ? $str : $default;
        }
        if (is_string($raw) && $raw !== '' && strtotime($raw) !== false) {
            return $raw;
        }
        return $default;
    }

    /**
     * 学習履歴を追加（受講者がコンテンツ学習後に呼び出される）
     *
     * @param int $content_id コンテンツID
     */
    public function add(int $content_id): ?Response
    {
        $this->autoRender = false;
        $this->request->allowMethod('post');

        $contentsTable = $this->fetchTable('Contents');
        if (!$contentsTable->exists(['id' => $content_id])) {
            throw new NotFoundException(__('Invalid content'));
        }

        $content = $contentsTable->get($content_id);

        $identity = $this->Authentication->getIdentity();
        if (!$identity) {
            throw new NotFoundException(__('Invalid access'));
        }
        $user_id = $identity->getIdentifier();

        $data = $this->request->getData();

        $recordsTable = $this->fetchTable('Records');
        $record = $recordsTable->newEntity([
            'user_id'       => $user_id,
            'course_id'     => $content->course_id,
            'content_id'    => $content_id,
            'study_sec'     => (int)($data['study_sec'] ?? 0),
            'understanding' => isset($data['understanding']) ? (int)$data['understanding'] : null,
            'is_passed'     => -1,
            'is_complete'   => (int)($data['is_complete'] ?? 0),
        ]);

        if ($recordsTable->save($record)) {
            // Ajaxの場合は OK だけ返す。フォームPOSTの場合はリダイレクト
            if ($this->request->is('ajax')) {
                return $this->response->withType('text/plain')->withStringBody('OK');
            }
            return $this->redirect(['controller' => 'Contents', 'action' => 'index', $content->course_id]);
        }

        if ($this->request->is('ajax')) {
            return $this->response->withType('text/plain')->withStringBody('NG');
        }
        $this->Flash->error(__('学習履歴の保存に失敗しました'));
        return $this->redirect(['controller' => 'Contents', 'action' => 'index', $content->course_id]);
    }
}