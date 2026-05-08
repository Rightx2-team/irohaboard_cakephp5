<?php
/**
 * @author        Kotaro Miura
 * @copyright     2015-2021 iroha Soft, Inc. (https://irohasoft.jp)
 */

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Database\Connection;

/**
 * Application table for CakePHP5.
 * 全Tableクラスの基底クラス。
 * CakePHP2のAppModelに相当。
 */
class AppTable extends Table
{
    /**
     * 英数字チェック（マルチバイト対応）
     * CakePHP2の alphaNumericMB バリデーションメソッドに相当。
     * CakePHP5ではバリデーションルールとして登録して使用する。
     *
     * @param mixed $value チェック対象
     * @return bool OK:true, NG:false
     */
    public function alphaNumericMB($value): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9]+$/', $value);
    }

    /**
     * 生SQLを実行し、結果を配列で返す
     * CakePHP2の queryList() に相当。
     *
     * @param string $sql         SQL文
     * @param array  $params      バインドパラメータ
     * @param string $field_name  取得するフィールド名
     * @return array フィールド値のリスト
     */
    public function queryList(string $sql, array $params, string $field_name): array
    {
        /** @var Connection $conn */
        $conn = $this->getConnection();
        $stmt = $conn->execute($sql, $params);
        $rows = $stmt->fetchAll('assoc');

        $list = [];
        foreach ($rows as $row) {
            $list[] = $row[$field_name];
        }

        return $list;
    }

    /**
     * 生SQLを実行し、全行を連想配列で返す
     *
     * @param string $sql    SQL文
     * @param array  $params バインドパラメータ
     * @return array 結果行の配列
     */
    public function rawQuery(string $sql, array $params = []): array
    {
        /** @var Connection $conn */
        $conn = $this->getConnection();
        $stmt = $conn->execute($sql, $params);
        return $stmt->fetchAll('assoc');
    }
}
