<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

class InfosController extends AppController
{
    public function adminIndex(): void
    {
        $infosTable = $this->fetchTable('Infos');
        $query = $infosTable->find()->orderBy(['Infos.created' => 'DESC']);
        $infos = $this->paginate($query, ['limit' => 20]);
        $this->set(compact('infos'));
    }

    public function adminAdd(): ?Response
    {
        $infosTable = $this->fetchTable('Infos');
        $info = $infosTable->newEmptyEntity();

        if ($this->request->is('post')) {
            $info = $infosTable->patchEntity($info, $this->request->getData());
            $identity = $this->Authentication->getIdentity();
            if ($identity) {
                $info->user_id = $identity->getIdentifier();
            }
            if ($infosTable->save($info)) {
                $this->Flash->success(__('お知らせを追加しました'));
                return $this->redirect(['action' => 'adminIndex']);
            }
            $this->Flash->error(__('保存に失敗しました'));
        }

        $groups = $this->fetchTable('Groups')->find('list', keyField: 'id', valueField: 'title')->toArray();
        $this->set(compact('info', 'groups'));
        $this->render('admin_edit');
        return null;
    }

    public function adminEdit(?int $info_id = null): ?Response
    {
        $infosTable = $this->fetchTable('Infos');

        if (!$info_id || !$infosTable->exists(['id' => $info_id])) {
            throw new NotFoundException(__('Invalid info'));
        }

        $info = $infosTable->get($info_id);

        if ($this->request->is(['post', 'put'])) {
            $info = $infosTable->patchEntity($info, $this->request->getData());
            if ($infosTable->save($info)) {
                $this->Flash->success(__('お知らせを更新しました'));
                return $this->redirect(['action' => 'adminIndex']);
            }
            $this->Flash->error(__('保存に失敗しました'));
        }

        $groups = $this->fetchTable('Groups')->find('list', keyField: 'id', valueField: 'title')->toArray();
        $this->set(compact('info', 'groups'));
        return null;
    }

    public function adminDelete(?int $info_id = null): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $infosTable = $this->fetchTable('Infos');

        if (!$info_id || !$infosTable->exists(['id' => $info_id])) {
            throw new NotFoundException(__('Invalid info'));
        }

        $info = $infosTable->get($info_id);
        if ($infosTable->delete($info)) {
            $this->Flash->success(__('お知らせを削除しました'));
        } else {
            $this->Flash->error(__('削除に失敗しました'));
        }
        return $this->redirect(['action' => 'adminIndex']);
    }

    public function index(): void
    {
        $infosTable = $this->fetchTable('Infos');
        $infos = $infosTable->find()->orderBy(['Infos.created' => 'DESC'])->all();
        $this->set(compact('infos'));
    }

    public function view(int $id): void
    {
        $infosTable = $this->fetchTable('Infos');
        $info = $infosTable->get($id);
        $this->set(compact('info'));
    }
}