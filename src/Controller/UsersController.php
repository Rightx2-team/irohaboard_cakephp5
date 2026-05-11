<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;

class UsersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['login', 'adminLogin']);
    }

    public function index(): ?Response
    {
        return $this->redirect('/users_courses');
    }

    public function login(): ?Response
    {
        if ($this->request->is('post')) {
            $result = $this->Authentication->getResult();
            if ($result && $result->isValid()) {
                $path = $this->request->getUri()->getPath();
                $redirect = $this->Authentication->getLoginRedirect();
                if (!$redirect) {
                    $redirect = str_contains($path, '/admin') ? '/admin/users' : '/users_courses';
                }
                return $this->redirect($redirect);
            }
            $this->Flash->error(__('ログインIDまたはパスワードが正しくありません'));
        }
        $this->set('username', '');
        $this->set('password', '');
        return null;
    }

    public function adminLogin(): ?Response
    {
        return $this->login();
    }

    public function logout(): ?Response
    {
        $target = $this->Authentication->logout() ?? '/users/login';
        return $this->redirect($target);
    }

    public function adminLogout(): ?Response
    {
        $target = $this->Authentication->logout() ?? '/admin/users/login';
        return $this->redirect($target);
    }

    // ==================== Admin screen / 管理画面 ====================

    public function adminIndex(): void
    {
        $usersTable = $this->fetchTable('Users');

        $query = $usersTable->find();

        $username = $this->request->getQuery('username', '');
        $name     = $this->request->getQuery('name', '');
        $group_id = $this->request->getQuery('group_id', '');

        if ($username !== '') {
            $query->where(['username LIKE' => '%' . $username . '%']);
        }
        if ($name !== '') {
            $query->where(['name LIKE' => '%' . $name . '%']);
        }

        $query->orderBy(['Users.created' => 'DESC']);

        $this->paginate = ['limit' => 20];
        $users = $this->paginate($query);

        $groupsTable = $this->fetchTable('Groups');
        $groups = $groupsTable->find('list', keyField: 'id', valueField: 'title')->toArray();

        $this->set(compact('users', 'groups', 'username', 'name', 'group_id'));
    }

    public function adminAdd(): ?Response
    {
        $usersTable = $this->fetchTable('Users');
        $user = $usersTable->newEmptyEntity();

        if ($this->request->is('post')) {
            $userData = $this->request->getData();

            if (($userData['new_password'] ?? '') !== ($userData['new_password2'] ?? '')) {
                $this->Flash->error(__('パスワードが一致しません'));
            } else {
                if (!empty($userData['new_password'])) {
                    $userData['password'] = $userData['new_password'];
                }

                $user = $usersTable->patchEntity($user, $userData, [
                    'associated' => ['Groups', 'Courses'],
                ]);
                if ($usersTable->save($user)) {
                    $this->Flash->success(__('ユーザを追加しました'));
                    return $this->redirect(['action' => 'adminIndex']);
                }
                $this->Flash->error(__('保存に失敗しました'));
            }
        }

        $groups  = $this->fetchTable('Groups')->find('list', keyField: 'id', valueField: 'title')->toArray();
        $courses = $this->fetchTable('Courses')->find('list', keyField: 'id', valueField: 'title')->toArray();
        $this->set(compact('user', 'groups', 'courses'));
        $this->set('username', '');
        $this->render('admin_edit');
        return null;
    }

    public function adminEdit(int $id): ?Response
    {
        $usersTable = $this->fetchTable('Users');
        $user = $usersTable->get($id, contain: ['Groups', 'Courses']);

        if ($this->request->is(['post', 'put'])) {
            $userData = $this->request->getData();

            if (!empty($userData['new_password'])) {
                $userData['password'] = $userData['new_password'];
            } else {
                unset($userData['password']);
            }

            $user = $usersTable->patchEntity($user, $userData, [
                'associated' => ['Groups', 'Courses'],
            ]);
            if ($usersTable->save($user)) {
                $this->Flash->success(__('ユーザを更新しました'));
                return $this->redirect(['action' => 'adminIndex']);
            }
            $this->Flash->error(__('保存に失敗しました'));
        }

        $groups  = $this->fetchTable('Groups')->find('list', keyField: 'id', valueField: 'title')->toArray();
        $courses = $this->fetchTable('Courses')->find('list', keyField: 'id', valueField: 'title')->toArray();
        $this->set(compact('user', 'groups', 'courses'));
        $this->set('username', $user->username);
        return null;
    }

    public function adminDelete(int $id): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);
        $usersTable = $this->fetchTable('Users');
        $user = $usersTable->get($id);

        if ($usersTable->delete($user)) {
            $this->Flash->success(__('ユーザを削除しました'));
        } else {
            $this->Flash->error(__('削除に失敗しました'));
        }
        return $this->redirect(['action' => 'adminIndex']);
    }

    public function adminClear(int $id): ?Response
    {
        $this->request->allowMethod(['post']);
        $usersTable = $this->fetchTable('Users');
        if (method_exists($usersTable, 'deleteUserRecords')) {
            $usersTable->deleteUserRecords($id);
        }
        $this->Flash->success(__('学習履歴を削除しました'));
        return $this->redirect(['action' => 'adminEdit', $id]);
    }

    public function adminImport(): void
    {
        $this->set('err_msg', '');
    }

    public function adminSetting(): ?Response
    {
        $identity = $this->Authentication->getIdentity();
        if (!$identity) {
            return $this->redirect(['action' => 'adminLogin']);
        }

        $usersTable = $this->fetchTable('Users');
        $user = $usersTable->get($identity->getIdentifier());

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            $userData = $data['data']['User'] ?? $data['User'] ?? $data;

            if (!empty($userData['new_password'])) {
                if (($userData['new_password'] ?? '') !== ($userData['new_password2'] ?? '')) {
                    $this->Flash->error(__('パスワードが一致しません'));
                } else {
                    $user = $usersTable->patchEntity($user, ['password' => $userData['new_password']]);
                    if ($usersTable->save($user)) {
                        $this->Flash->success(__('パスワードを変更しました'));
                        return $this->redirect(['action' => 'adminSetting']);
                    }
                    $this->Flash->error(__('保存に失敗しました'));
                }
            }
        }

        $this->set(compact('user'));
        return null;
    }

    // ==================== Learner screen / 受講者画面 ====================

    public function setting(): ?Response
    {
        return $this->adminSetting();
    }
}