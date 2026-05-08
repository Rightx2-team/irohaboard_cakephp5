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

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Response;
use Cake\I18n\I18n;

/**
 * Application Controller
 * CakePHP2の AppController に相当。
 */
class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();

        // CakePHP5のコンポーネント読み込み
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication');

        // CakePHP5ではCookieはResponseオブジェクトで扱う
        // セッションは $this->request->getSession() で取得
    }

    /**
     * コールバック（アクション実行前）
     * CakePHP2の beforeFilter() に相当。
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        // 言語設定（セッションから取得、デフォルトは日本語）
        $lang = $this->request->getSession()->read('Config.language') ?? 'ja_JP';
        I18n::setLocale($lang);
        $this->set('currentLang', $lang);

        // ログインユーザ情報をViewへ渡す
        $this->set('loginedUser', $this->readAuthUser());

        // iframeへの埋め込み禁止ヘッダー
        $this->response = $this->response->withHeader('X-Frame-Options', 'SAMEORIGIN');

        // 他のサイトの設定が存在する場合、設定情報およびログイン情報をクリア
        $session = $this->request->getSession();

        if ($session->check('Setting')) {
            if ($session->read('Setting.app_dir') != APP) {
                $session->delete('Setting');
                if ($this->readAuthUser()) {
                    $this->redirect($this->Authentication->logout());
                    return;
                }
            }
        }

        // DBの設定情報をセッションにキャッシュ
        if (!$session->check('Setting')) {
            $settings = $this->fetchTable('Settings')->getSettings();
            $session->write('Setting.app_dir', APP);
            foreach ($settings as $key => $value) {
                $session->write('Setting.' . $key, $value);
            }
        }

        // 管理画面へのアクセス権限チェック
        if ($this->isAdminPage()) {
            $user = $this->readAuthUser();
            if ($user) {
                $role = $user['role'] ?? '';
                if (!in_array($role, ['admin', 'manager', 'editor', 'teacher'], true)) {
                    $this->Flash->error(__('管理画面へのアクセス権限がありません'));
                    $this->redirect($this->Authentication->logout());
                    return;
                }
            }
        }
    }

    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        $this->response = $this->response->withHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    // -------------------------------------------------------------------------
    // セッション操作
    // -------------------------------------------------------------------------

    protected function readSession(string $key): mixed
    {
        return $this->request->getSession()->read($key) ?? '';
    }

    protected function deleteSession(string $key): void
    {
        $this->request->getSession()->delete($key);
    }

    protected function hasSession(string $key): bool
    {
        return $this->request->getSession()->check($key);
    }

    protected function writeSession(string $key, mixed $value): void
    {
        $this->request->getSession()->write($key, $value);
    }

    // -------------------------------------------------------------------------
    // Cookie操作
    // CakePHP5ではCookieをResponseに付与する形に変わった。
    // 読み取りは request->getCookieCollection() で行う。
    // -------------------------------------------------------------------------

    protected function readCookie(string $key): mixed
    {
        return $this->request->getCookie($key) ?? '';
    }

    protected function deleteCookie(string $key): void
    {
        $cookie = (new Cookie($key))->withExpiry(new \DateTime('-1 year'));
        $this->response = $this->response->withCookie($cookie);
    }

    protected function hasCookie(string $key): bool
    {
        return $this->request->getCookie($key) !== null;
    }

    protected function writeCookie(string $key, mixed $value, bool $encrypt = true, string $expires = '+2 weeks'): void
    {
        $cookie = (new Cookie($key))
            ->withValue(is_array($value) ? json_encode($value) : (string)$value)
            ->withExpiry(new \DateTime($expires))
            ->withHttpOnly(true)
            ->withPath(ini_get('session.cookie_path') ?: '/');

        $this->response = $this->response->withCookie($cookie);
    }

    // -------------------------------------------------------------------------
    // 認証ユーザ情報
    // -------------------------------------------------------------------------

    /**
     * ログインユーザ情報の取得
     * CakePHP2の $this->Auth->user() に相当。
     *
     * @param string|null $key 取得するキー
     * @return mixed
     */
    protected function readAuthUser(?string $key = null): mixed
    {
        $identity = $this->Authentication->getIdentity();
        if ($identity === null) {
            return null;
        }
        $user = $identity->getOriginalData()->toArray();
        if ($key === null) {
            return $user;
        }
        return $user[$key] ?? null;
    }

    protected function isLogined(): bool
    {
        return $this->Authentication->getIdentity() !== null;
    }

    // -------------------------------------------------------------------------
    // リクエストデータ
    // -------------------------------------------------------------------------

    /**
     * クエリストリングの取得
     * CakePHP2の $this->request->query[$key] に相当。
     */
    protected function getQuery(string $key, mixed $default = ''): mixed
    {
        return $this->request->getQuery($key) ?? $default;
    }

    protected function hasQuery(string $key): bool
    {
        return $this->request->getQuery($key) !== null;
    }

    /**
     * ルートパラメータの取得
     * CakePHP2の $this->request->params[$key] に相当。
     */
    protected function getParam(string $key, mixed $default = ''): mixed
    {
        return $this->request->getParam($key) ?? $default;
    }

    /**
     * POSTデータの取得
     * CakePHP2の $this->request->data に相当。
     */
    protected function getData(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->request->getData() ?: $default;
        }
        return $this->request->getData($key) ?? $default;
    }

    // -------------------------------------------------------------------------
    // ページ種別チェック
    // -------------------------------------------------------------------------

    /**
     * 管理画面へのアクセスか確認
     * CakePHP5ではprefixルーティングで 'Admin' プレフィックスを使用する想定。
     */
    protected function isAdminPage(): bool
    {
        return $this->request->getParam('prefix') === 'Admin';
    }

    protected function isEditPage(): bool
    {
        $action = $this->request->getParam('action');
        return in_array($action, ['edit', 'adminEdit'], true);
    }

    protected function isRecordPage(): bool
    {
        $action = $this->request->getParam('action');
        return in_array($action, ['record', 'adminRecord'], true);
    }

    protected function isLoginPage(): bool
    {
        $action = $this->request->getParam('action');
        return in_array($action, ['login', 'adminLogin'], true);
    }

    protected function isHTTPS(): bool
    {
        return $this->request->is('https');
    }

    // -------------------------------------------------------------------------
    // ログ保存
    // -------------------------------------------------------------------------

    /**
     * ログの保存
     * CakePHP5ではEntityを使って保存する。
     */
    protected function writeLog(string $log_type, string $log_content): void
    {
        // ロードバランサー対応
        $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        if ($forwarded !== '') {
            $ips = explode(',', $forwarded);
            $ip  = trim($ips[0]);
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }

        $logs = $this->fetchTable('Logs');
        $log  = $logs->newEntity([
            'log_type'    => $log_type,
            'log_content' => $log_content,
            'user_id'     => $this->readAuthUser('id'),
            'user_ip'     => $ip,
            'user_agent'  => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
        $logs->save($log);
    }
}
