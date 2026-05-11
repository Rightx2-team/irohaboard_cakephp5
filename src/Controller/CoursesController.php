<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\Core\Configure;

class CoursesController extends AppController
{
    public function adminIndex(): void
    {
        $this->paginate = ['limit' => 20, 'order' => ['sort_no' => 'ASC']];
        $courses = $this->paginate($this->fetchTable('Courses'));
        $this->set(compact('courses'));
    }

    public function adminAdd(): ?Response
    {
        $coursesTable = $this->fetchTable('Courses');
        $course = $coursesTable->newEmptyEntity();

        if ($this->request->is('post')) {
            $course = $coursesTable->patchEntity($course, $this->request->getData());
            // Set creator ID / 作成者IDをセット
            $identity = $this->Authentication->getIdentity();
            if ($identity) {
                $course->user_id = $identity->getIdentifier();
            }
            if ($coursesTable->save($course)) {
                $this->Flash->success(__('コースを追加しました'));
                return $this->redirect(['action' => 'adminIndex']);
            }
            $this->Flash->error(__('保存に失敗しました'));
        }

        $groups = $this->fetchTable('Groups')->find('list', keyField: 'id', valueField: 'title')->toArray();
        $this->set(compact('course', 'groups'));
        $this->render('admin_edit');
        return null;
    }

    public function adminEdit(?int $course_id = null): ?Response
    {
        $coursesTable = $this->fetchTable('Courses');

        if (!$course_id || !$coursesTable->exists(['id' => $course_id])) {
            throw new NotFoundException(__('Invalid course'));
        }

        $course = $coursesTable->get($course_id, contain: ['Groups']);

        if ($this->request->is(['post', 'put'])) {
            $course = $coursesTable->patchEntity($course, $this->request->getData());
            if ($coursesTable->save($course)) {
                $this->Flash->success(__('コースを更新しました'));
                return $this->redirect(['action' => 'adminIndex']);
            }
            $this->Flash->error(__('保存に失敗しました'));
        }

        $groups = $this->fetchTable('Groups')->find('list', keyField: 'id', valueField: 'title')->toArray();
        $this->set(compact('course', 'groups'));
        return null;
    }

    public function adminDelete(?int $course_id = null): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        if (Configure::read('demo_mode')) {
            $this->Flash->error(__('デモモードでは削除できません'));
            return $this->redirect(['action' => 'adminIndex']);
        }

        $coursesTable = $this->fetchTable('Courses');
        if (!$course_id || !$coursesTable->exists(['id' => $course_id])) {
            throw new NotFoundException(__('Invalid course'));
        }

        if (method_exists($coursesTable, 'deleteCourse')) {
            $coursesTable->deleteCourse($course_id);
        } else {
            $course = $coursesTable->get($course_id);
            $coursesTable->delete($course);
        }

        $this->Flash->success(__('コースを削除しました'));
        return $this->redirect(['action' => 'adminIndex']);
    }

    public function adminOrder(): ?Response
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            $idList = $this->request->getData('id_list');
            $coursesTable = $this->fetchTable('Courses');
            if (method_exists($coursesTable, 'setOrder')) {
                $coursesTable->setOrder($idList);
            }
            $response = $this->response->withType('text/plain')->withStringBody('OK');
            return $response;
        }
        return null;
    }
}