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

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;

class UpdateController extends AppController
{
    private string $err_msg = '';

    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['index', 'error']);
    }

    public function beforeFilter(EventInterface $event): void
    {
        // 意図的に親のbeforeFilterをスキップ
    }

    public function index(): void
    {
        try {
            /** @var \Cake\Database\Connection $db */
            $db = ConnectionManager::get('default');

            $err_update = $this->executeSQLScript($db, CONFIG . 'Schema' . DS . 'update.sql');

            $custom_path = ROOT . DS . 'app' . DS . 'Custom' . DS . 'Config' . DS . 'custom.sql';
            if (file_exists($custom_path)) {
                $err_custom     = $this->executeSQLScript($db, $custom_path);
                $err_statements = array_merge($err_update, $err_custom);
            } else {
                $err_statements = $err_update;
            }

            if (count($err_statements) > 0) {
                $this->err_msg = 'クエリの実行中にエラーが発生しました。';
                $this->log(implode("\n", $err_statements));
                $this->set('body', $this->err_msg);
                $this->render('error');
                return;
            }
        } catch (\Exception $e) {
            $this->err_msg = 'データベースへの接続に失敗しました。config/app_local.php をご確認ください。';
            $this->set('body', $this->err_msg);
            $this->render('error');
        }
    }

    public function error(): void
    {
        $this->set('loginedUser', $this->readAuthUser());
        $this->set('body', $this->err_msg);
    }

    private function executeSQLScript(\Cake\Database\Connection $db, string $path): array
    {
        $statements     = explode(';', (string)file_get_contents($path));
        $err_statements = [];

        foreach ($statements as $statement) {
            if (trim($statement) === '') {
                continue;
            }

            $statement = str_replace('%salt%', Configure::read('Security.salt'), $statement);

            try {
                $db->execute($statement);
            } catch (\Exception $e) {
                $code = $e->getCode();
                if (in_array($code, ['23000', '42S21', '42S01', '42000'], true)) {
                    continue;
                }
                $err_statements[] = sprintf("[%s] %s\nSQL: %s", $code, $e->getMessage(), $statement);
            }
        }

        return $err_statements;
    }
}
