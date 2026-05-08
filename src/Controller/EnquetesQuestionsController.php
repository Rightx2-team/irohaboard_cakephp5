<?php
/**
 * iroha Board Project
 *
 * @author        Kotaro Miura
 * @copyright     2015-2021 iroha Soft, Inc. (https://irohasoft.jp)
 * @link          https://irohaboard.irohasoft.jp
 * @license       https://www.gnu.org/licenses/gpl-3.0.en.html GPL License
 */

declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;

/**
 * アンケートコントローラ
 * ContentsQuestionsController とほぼ同構成。採点なしで保存する点が異なる。
 */
class EnquetesQuestionsController extends AppController
{
    public function index(int $content_id, ?int $record_id = null): void
    {
        $content   = $this->fetchTable('Contents')->get($content_id, ['contain' => ['Courses']]);
        $questions = $this->fetchTable('ContentsQuestions');
        $records   = $this->fetchTable('Records');
        $record    = null;

        if (!$this->isAdminPage()) {
            if (!$this->fetchTable('Courses')->hasRight($this->readAuthUser('id'), $content->course_id)) {
                throw new NotFoundException(__('Invalid access'));
            }
        }

        if ($this->readAuthUser('role') !== 'admin' && $content->status != 1) {
            throw new NotFoundException(__('Invalid access'));
        }

        if ($record_id !== null) {
            $record = $records->get($record_id, ['contain' => ['RecordsQuestions']]);

            if (!$this->isAdminPage() && $this->isRecordPage() && $record->user_id != $this->readAuthUser('id')) {
                throw new NotFoundException(__('Invalid access'));
            }

            $question_id_list = [0];
            foreach ($record->records_questions as $q) {
                $question_id_list[] = $q->question_id;
            }

            $contentsQuestions = $questions->find()
                ->where(['content_id' => $content_id, 'id IN' => $question_id_list])
                ->orderByAsc('FIELD(id,' . implode(',', $question_id_list) . ')')
                ->all();
        } else {
            $contentsQuestions = $questions->find()
                ->where(['content_id' => $content_id])
                ->orderByAsc('sort_no')
                ->all();
        }

        if ($this->request->is('post')) {
            $details   = [];
            $study_sec = $this->getData('ContentsQuestion')['study_sec'] ?? 0;

            foreach ($contentsQuestions as $q) {
                $details[] = [
                    'question_id' => $q->id,
                    'answer'      => $this->getData('answer_' . $q->id),
                    'is_correct'  => -1,
                ];
            }

            $recordEntity = $records->newEntity([
                'user_id'     => $this->readAuthUser('id'),
                'course_id'   => $content->course->id,
                'content_id'  => $content_id,
                'study_sec'   => $study_sec,
                'is_passed'   => 2,
                'is_complete' => 1,
            ]);

            if ($records->save($recordEntity)) {
                $new_record_id = $recordEntity->id;
                $rq            = $this->fetchTable('RecordsQuestions');

                foreach ($details as $detail) {
                    $detail['record_id'] = $new_record_id;
                    $rq->save($rq->newEntity($detail));
                }

                $this->deleteSession('Iroha.RondomQuestions.' . $content_id . '.id_list');
                $this->Flash->success(__('回答内容を送信しました'));
                $this->redirect(['action' => 'record', $content_id, $new_record_id]);
                return;
            }
        }

        $is_record       = $this->isRecordPage();
        $is_admin_record = $this->isAdminPage() && $this->isRecordPage();

        $this->set(compact('content', 'contentsQuestions', 'record', 'is_record', 'is_admin_record'));
    }

    public function record(int $content_id, int $record_id): void
    {
        $this->index($content_id, $record_id);
        $this->render('index');
    }

    public function adminRecord(int $content_id, int $record_id): void
    {
        $this->record($content_id, $record_id);
    }

    public function adminIndex(int $content_id): void
    {
        $contentsQuestions = $this->fetchTable('ContentsQuestions')->find()
            ->where(['content_id' => $content_id])
            ->orderByAsc('sort_no')
            ->all();

        $content = $this->fetchTable('Contents')->get($content_id);
        $this->set(compact('content', 'contentsQuestions'));
    }

    public function adminAdd(int $content_id): void
    {
        $this->adminEdit($content_id);
        $this->render('admin_edit');
    }

    public function adminEdit(int $content_id, ?int $question_id = null): void
    {
        $questions = $this->fetchTable('ContentsQuestions');

        if ($this->isEditPage() && !$questions->exists(['id' => $question_id])) {
            throw new NotFoundException(__('Invalid contents question'));
        }

        $content = $this->fetchTable('Contents')->get($content_id);

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if ($question_id === null) {
                $data['user_id']    = $this->readAuthUser('id');
                $data['content_id'] = $content_id;
                $data['sort_no']    = $questions->getNextSortNo($content_id);
            }

            $entity = $question_id ? $questions->get($question_id) : $questions->newEmptyEntity();
            $entity = $questions->patchEntity($entity, $data);

            if ($questions->save($entity)) {
                $this->Flash->success(__('質問が保存されました'));
                $this->redirect(['controller' => 'EnquetesQuestions', 'action' => 'adminIndex', $content_id]);
                return;
            }
            $this->Flash->error(__('The contents question could not be saved. Please, try again.'));
        } else {
            $entity = $question_id ? $questions->get($question_id) : $questions->newEmptyEntity();
        }

        $this->set(compact('content', 'entity'));
    }

    public function adminDelete(?int $question_id = null): void
    {
        $questions = $this->fetchTable('ContentsQuestions');

        if (!$questions->exists(['id' => $question_id])) {
            throw new NotFoundException(__('Invalid contents question'));
        }

        $this->request->allowMethod(['post', 'delete']);
        $question = $questions->get($question_id);

        if ($questions->delete($question)) {
            $this->Flash->success(__('質問が削除されました'));
            $this->redirect(['controller' => 'EnquetesQuestions', 'action' => 'adminIndex', $question->content_id]);
            return;
        }

        $this->Flash->error(__('The contents question could not be deleted. Please, try again.'));
        $this->redirect(['action' => 'adminIndex']);
    }

    public function adminOrder(): void
    {
        $this->autoRender = false;

        if ($this->request->is('ajax')) {
            $this->fetchTable('ContentsQuestions')->setOrder($this->getData('id_list'));
            echo 'OK';
        }
    }
}
