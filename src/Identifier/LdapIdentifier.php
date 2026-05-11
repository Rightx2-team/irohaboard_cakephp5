<?php
/**
 * iroha Board Project - LDAP/AD Identifier
 * Supports per-user switching between local DB and AD authentication.
 * ユーザーごとにローカルDB認証とAD認証を切り替えるIdentifier
 */

declare(strict_types=1);

namespace App\Identifier;

use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identifier\PasswordIdentifier;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Log\Log;

class LdapIdentifier extends AbstractIdentifier
{
    use LocatorAwareTrait;

    /**
     * Default configuration / デフォルト設定
     * - ldapServers: LDAP server host list (tried in order) / LDAPサーバーホスト一覧（順に試行）
     * - ldapPort:    LDAP port / LDAPポート
     * - ldapDomain:  AD domain (for UPN: user@domain) / ADドメイン（UPN: user@domain）
     * - baseDn:      LDAP base DN / LDAPベースDN
     */
    protected array $_defaultConfig = [
        'ldapServers' => ['172.19.1.1', '172.18.0.1'],
        'ldapPort'    => 389,
        'ldapDomain'  => 'daitetsu.local',
        'baseDn'      => 'DC=daitetsu,DC=local',
        'fields' => [
            'username' => 'username',
            'password' => 'password',
        ],
        'userModel' => 'Users',
    ];

    /**
     * Identify a user by username and password.
     * Checks auth_type in DB and routes to local or LDAP authentication.
     * ユーザーを識別する。DBのauth_typeを確認してローカルまたはLDAP認証に振り分ける。
     */
    public function identify(array $credentials): \ArrayAccess|array|null
    {
        $usernameField = $this->getConfig('fields.username');
        $passwordField = $this->getConfig('fields.password');

        $username = $credentials[$usernameField] ?? '';
        $password = $credentials[$passwordField] ?? '';

        if (empty($username) || empty($password)) {
            return null;
        }

        // Look up user in DB / DBでユーザーを検索
        $usersTable = $this->getTableLocator()->get($this->getConfig('userModel'));
        $user = $usersTable->find()
            ->where(['username' => $username])
            ->first();

        if (!$user) {
            return null;
        }

        // Route by auth_type / auth_typeで認証方法を振り分け
        if ($user->auth_type === 'ldap') {
            return $this->authenticateWithLdap($user, $password);
        }

        // Local DB authentication / ローカルDB認証
        return $this->authenticateWithPassword($user, $password);
    }

    /**
     * Authenticate against Active Directory via LDAP.
     * Tries each server in order; returns user entity on success, null on failure.
     * LDAPでAD認証を行う。サーバーを順に試行し、成功時はエンティティを返す。
     */
    private function authenticateWithLdap(\ArrayAccess|array|null $user, string $password): \ArrayAccess|array|null
    {
        $servers = $this->getConfig('ldapServers');
        $port    = $this->getConfig('ldapPort');
        $domain  = $this->getConfig('ldapDomain');

        // Try both UPN and NetBIOS formats for maximum AD compatibility
        // UPN形式とNetBIOS形式の両方を試行してAD互換性を最大化
        $bindFormats = [
            $user->username . '@' . $domain,          // UPN:     n-imajima@daitetsu.local
            strtoupper(explode('.', $domain)[0])
                . '\\' . $user->username,              // NetBIOS: DAITETSU\n-imajima
        ];

        foreach ($servers as $server) {
            $conn = @ldap_connect($server, $port);
            if (!$conn) {
                continue;
            }

            ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($conn, LDAP_OPT_NETWORK_TIMEOUT, 3);

            // Try each bind format in order / 各バインド形式を順に試行
            foreach ($bindFormats as $bindDn) {
                $bound = @ldap_bind($conn, $bindDn, $password);
                if ($bound) {
                    ldap_unbind($conn);
                    return $user;
                }
            }

            ldap_unbind($conn);
        }

        // All servers and formats failed / 全サーバー・全形式で認証失敗
        Log::warning("LDAP authentication failed for user: {$user->username}");
        return null;
    }

    /**
     * Authenticate using the local DB password hash (bcrypt).
     * ローカルDBのパスワードハッシュ（bcrypt）で認証する。
     */
    private function authenticateWithPassword(\ArrayAccess|array|null $user, string $password): \ArrayAccess|array|null
    {
        if (password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }
}
