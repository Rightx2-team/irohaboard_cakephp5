-- Migration: Add auth_type column to ib_users
-- Run this on existing installations to support AD/LDAP authentication.
-- 既存環境への適用: AD/LDAP認証を有効にするためのマイグレーション
--
-- auth_type values / 値:
--   'local' - local DB password authentication (default) / ローカルDBパスワード認証（デフォルト）
--   'ldap'  - Active Directory / LDAP authentication / AD/LDAP認証

ALTER TABLE `ib_users`
  ADD COLUMN `auth_type` varchar(10) NOT NULL DEFAULT 'local'
  COMMENT 'Authentication type: local or ldap'
  AFTER `role`;
