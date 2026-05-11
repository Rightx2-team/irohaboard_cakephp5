<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Http\Response;

class InstallController extends AppController
{
    private string $err_msg = '';

    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['index', 'installed', 'complete', 'error']);
    }

    public function beforeFilter(EventInterface $event): void
    {
    }

    public function index(): ?Response
    {
        try {
            if (function_exists('apache_get_modules')) {
                if (!$this->apacheModuleLoaded('mod_rewrite')) {
                    $this->err_msg = 'Apache mod_rewrite is not loaded';
                    $this->set('body', $this->err_msg);
                    $this->render('error');
                    return null;
                }
            }

            if ($this->getRequest()->is('post')) {
                $data = $this->getRequest()->getData();

                // Sent in the data[User][...] format / data[User][...] 形式で送られてくる
                $userData = $data['data']['User'] ?? [];
                $username  = $userData['username']  ?? '';
                $password  = $userData['password']  ?? '';
                $password2 = $userData['password2'] ?? '';

                if (!preg_match('/^[a-zA-Z0-9]{4,32}$/', $username)) {
                    $this->Flash->error('ログインIDは4-32文字の英数字で入力してください');
                    $this->set('username', $username);
                    return null;
                }
                if (!preg_match('/^[a-zA-Z0-9]{4,32}$/', $password)) {
                    $this->Flash->error('パスワードは4-32文字の英数字で入力してください');
                    $this->set('username', $username);
                    return null;
                }
                if ($password !== $password2) {
                    $this->Flash->error('パスワードが一致しません');
                    $this->set('username', $username);
                    return null;
                }

                // Execute schema / スキーマ実行
                $sqlFile = ROOT . DS . 'config' . DS . 'Schema' . DS . 'app.sql';
                if (!file_exists($sqlFile)) {
                    $sqlFile = dirname(ROOT) . DS . 'cakephp2_app' . DS . 'app' . DS . 'Config' . DS . 'Schema' . DS . 'app.sql';
                }

                if (!file_exists($sqlFile)) {
                    $this->err_msg = 'Schema file not found: ' . $sqlFile;
                    $this->set('body', $this->err_msg);
                    $this->render('error');
                    return null;
                }

                $conn = ConnectionManager::get('default');
                $sqlContent = file_get_contents($sqlFile);
                $statements = array_filter(array_map('trim', explode(';', $sqlContent)));

                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        $conn->execute($statement);
                    }
                }

                // Create admin user / 管理者作成
                $usersTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Users');
                $user = $usersTable->newEmptyEntity();
                $user->username = $username;
                $user->password = (new \Authentication\PasswordHasher\DefaultPasswordHasher())->hash($password);
                $user->name = $username;
                $user->role = 'admin';
                $user->email = '';
                $user->comment = '';
                $usersTable->save($user);

                return $this->redirect(['action' => 'complete']);
            }

            $this->set('username', '');
            return null;
        } catch (\Throwable $e) {
            $this->err_msg = $e->getMessage();
            $this->set('body', $this->err_msg);
            $this->render('error');
            return null;
        }
    }

    public function complete(): void
    {
    }

    public function error(): void
    {
    }

    public function installed(): void
    {
    }

    private function apacheModuleLoaded(string $module): bool
    {
        if (!function_exists('apache_get_modules')) {
            return true;
        }
        return in_array($module, apache_get_modules(), true);
    }
}