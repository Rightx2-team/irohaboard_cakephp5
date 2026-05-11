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

class ContentsQuestionsController extends AppController
{
    /**
     * Present questions / grade test / 問題を出題 / テスト採点
     */
    public function index(int $content_id, ?int $record_id = null): void
    {
        $content = $this->fetchTable('Contents')->get($content_id, ['contain' => ['Courses']]);

        // Permission check / 権限チェック
        if (!$this->isAdminPage()) {
            if (!$this->fetchTable('Courses')->hasRight($this->readAuthUser('id'), $content->course_id)) {
                throw new NotFoundException(__('Invalid access'));
            }
        }

        if ($this->readAuthUser('role') !== 'admin' && $content->status != 1) {
            throw new NotFoundException(__('Invalid access'));
        }

        $questions = $this->fetchTable('ContentsQuestions');
        $records   = $this->fetchTable('Records');
        $record    = null;

        if ($record_id !== null) {
            // Test result display mode / テスト結果表示モード
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

        } elseif ($this->readSession('Iroha.RondomQuestions.' . $content_id . '.id_list') !== '') {
            // Random question info exists in session / セッションにランダム出題情報あり
            $question_id_list  = $this->readSession('Iroha.RondomQuestions.' . $content_id . '.id_list');
            $contentsQuestions = $questions->find()
                ->where(['content_id' => $content_id, 'id IN' => $question_id_list])
                ->orderByAsc('FIELD(id,' . implode(',', $question_id_list) . ')')
                ->all();

        } elseif ($content->question_count > 0) {
            // Random question presentation / ランダム出題
            $contentsQuestions = $questions->find()
                ->where(['content_id' => $content_id])
                ->limit($content->question_count)
                ->orderByAsc('RAND()')
                ->all();

            $question_id_list = [];
            foreach ($contentsQuestions as $q) {
                $question_id_list[] = $q->id;
            }
            $this->writeSession('Iroha.RondomQuestions.' . $content_id . '.id_list', $question_id_list);

        } else {
            // Standard question presentation / 通常出題
            $contentsQuestions = $questions->find()
                ->where(['content_id' => $content_id])
                ->orderByAsc('sort_no')
                ->all();
        }

        // Grading process / 採点処理
        if ($this->request->is('post')) {
            $details    = [];
            $full_score = 0;
            $pass_score = 0;
            $my_score   = 0;
            $pass_rate  = $content->pass_rate;

            foreach ($contentsQuestions as $q) {
                $question_id = $q->id;
                $answer      = $this->getData('answer_' . $question_id);
                $correct     = $q->correct;
                $corrects    = explode(',', $correct);
                $score       = $q->score;

                if (count($corrects) > 1) {
                    $is_correct = $this->isMultiCorrect($answer, $corrects) ? 1 : 0;
                    $answer     = is_array($answer) ? implode(',', $answer) : null;
                } else {
                    $is_correct = ($answer == $correct) ? 1 : 0;
                }

                $full_score += $score;
                if ($is_correct == 1) {
                    $my_score += $score;
                }

                $details[] = [
                    'question_id' => $question_id,
                    'answer'      => $answer,
                    'correct'     => $correct,
                    'is_correct'  => $is_correct,
                    'score'       => $score,
                ];
            }

            $pass_score = ($full_score * $pass_rate) / 100;
            $is_passed  = ($my_score >= $pass_score) ? 1 : 0;
            $study_sec  = $this->getData('ContentsQuestion')['study_sec'] ?? 0;

            $recordEntity = $records->newEntity([
                'user_id'     => $this->readAuthUser('id'),
                'course_id'   => $content->course->id,
                'content_id'  => $content_id,
                'full_score'  => $full_score,
                'pass_score'  => $pass_score,
                'score'       => $my_score,
                'is_passed'   => $is_passed,
                'study_sec'   => $study_sec,
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
                $this->Flash->success(__('問題が保存されました'));
                $this->redirect(['controller' => 'ContentsQuestions', 'action' => 'adminIndex', $content_id]);
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
            $this->Flash->success(__('問題が削除されました'));
            $this->redirect(['controller' => 'ContentsQuestions', 'action' => 'adminIndex', $question->content_id]);
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

    private function isMultiCorrect(mixed $answers, array $corrects): bool
    {
        if (!isset($answers) || $answers === null) {
            return false;
        }
        if (count($answers) !== count($corrects)) {
            return false;
        }
        foreach ($answers as $a) {
            if (!in_array($a, $corrects, true)) {
                return false;
            }
        }
        return true;
    }
}
