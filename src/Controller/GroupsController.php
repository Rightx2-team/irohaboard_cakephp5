<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

class GroupsController extends AppController
{
    public function adminIndex(): void
    {
        $groupsTable = $this->fetchTable('Groups');

        $query = $groupsTable->find()->orderBy(['Groups.created' => 'DESC']);
        $groups = $this->paginate($query, ['limit' => 20]);

        $this->set(compact('groups'));
    }

    public function adminAdd(): ?Response
    {
        $groupsTable = $this->fetchTable('Groups');
        $group = $groupsTable->newEmptyEntity();

        if ($this->request->is('post')) {
            $group = $groupsTable->patchEntity($group, $this->request->getData());
            if ($groupsTable->save($group)) {
                $this->Flash->success(__('グループを追加しました'));
                return $this->redirect(['action' => 'adminIndex']);
            }
            $this->Flash->error(__('保存に失敗しました'));
        }

        $courses = $this->fetchTable('Courses')->find('list', keyField: 'id', valueField: 'title')->toArray();
        $this->set(compact('group', 'courses'));
        $this->render('admin_edit');
        return null;
    }

    public function adminEdit(?int $group_id = null): ?Response
    {
        $groupsTable = $this->fetchTable('Groups');

        if (!$group_id || !$groupsTable->exists(['id' => $group_id])) {
            throw new NotFoundException(__('Invalid group'));
        }

        $group = $groupsTable->get($group_id, contain: ['Courses']);

        if ($this->request->is(['post', 'put'])) {
            $group = $groupsTable->patchEntity($group, $this->request->getData());
            if ($groupsTable->save($group)) {
                $this->Flash->success(__('グループを更新しました'));
                return $this->redirect(['action' => 'adminIndex']);
            }
            $this->Flash->error(__('保存に失敗しました'));
        }

        $courses = $this->fetchTable('Courses')->find('list', keyField: 'id', valueField: 'title')->toArray();
        $this->set(compact('group', 'courses'));
        return null;
    }

    public function adminDelete(?int $group_id = null): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $groupsTable = $this->fetchTable('Groups');

        if (!$group_id || !$groupsTable->exists(['id' => $group_id])) {
            throw new NotFoundException(__('Invalid group'));
        }

        $group = $groupsTable->get($group_id);
        if ($groupsTable->delete($group)) {
            $this->Flash->success(__('グループを削除しました'));
        } else {
            $this->Flash->error(__('削除に失敗しました'));
        }
        return $this->redirect(['action' => 'adminIndex']);
    }
}