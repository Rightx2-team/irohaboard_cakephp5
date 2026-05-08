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

namespace App\View;

use Cake\View\View;

/**
 * AppView - 全Viewの基底クラス
 * CakePHP5ではセッションは $this->request->getSession() で操作する。
 */
class AppView extends View
{
    public function initialize(): void
    {
        parent::initialize();

        // カスタムHelperの読み込み
        $this->loadHelper('AppForm');
    }

    // -------------------------------------------------------------------------
    // セッション操作
    // -------------------------------------------------------------------------

    public function readSession(string $key): mixed
    {
        return $this->request->getSession()->read($key) ?? '';
    }

    public function deleteSession(string $key): void
    {
        $this->request->getSession()->delete($key);
    }

    public function hasSession(string $key): bool
    {
        return $this->request->getSession()->check($key);
    }

    public function writeSession(string $key, mixed $value): void
    {
        $this->request->getSession()->write($key, $value);
    }

    // -------------------------------------------------------------------------
    // 認証ユーザ情報
    // CakePHP5ではViewからは $loginedUser 変数（Controllerでsetした値）を使う。
    // ただし互換性のためメソッドも残す。
    // -------------------------------------------------------------------------

    public function readAuthUser(?string $key = null): mixed
    {
        $user = $this->request->getSession()->read('Auth.User');

        if (!$user) {
            return null;
        }

        if ($key === null) {
            return $user;
        }

        return $user[$key] ?? null;
    }

    public function isLogined(): bool
    {
        return $this->readAuthUser() !== null;
    }

    // -------------------------------------------------------------------------
    // ページ種別チェック
    // -------------------------------------------------------------------------

    public function isAdminPage(): bool
    {
        return $this->request->getParam('prefix') === 'Admin';
    }

    public function isEditPage(): bool
    {
        return in_array($this->request->getParam('action'), ['edit', 'adminEdit'], true);
    }

    public function isRecordPage(): bool
    {
        return in_array($this->request->getParam('action'), ['record', 'adminRecord'], true);
    }

    public function isLoginPage(): bool
    {
        return in_array($this->request->getParam('action'), ['login', 'adminLogin'], true);
    }

    public function isHTTPS(): bool
    {
        return $this->request->is('https');
    }

    public function isLocalIP(): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        if ($ip === '::1') {
            return true;
        }

        return (bool) preg_match('/^(127\.|10\.|192\.168\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/', $ip);
    }
}
