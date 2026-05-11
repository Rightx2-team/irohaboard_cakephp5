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
 * Base class for all Table classes. / 全Tableクラスの基底クラス。
 * Equivalent to AppModel in CakePHP2. / CakePHP2のAppModelに相当。
 */
class AppTable extends Table
{
    /**
     * Alphanumeric check (multibyte compatible). / 英数字チェック（マルチバイト対応）
     * Equivalent to the alphaNumericMB validation method in CakePHP2. / CakePHP2の alphaNumericMB バリデーションメソッドに相当。
     * In CakePHP5, register and use as a validation rule. / CakePHP5ではバリデーションルールとして登録して使用する。
     *
     * @param mixed $value Value to check. / チェック対象
     * @return bool OK:true, NG:false
     */
    public function alphaNumericMB($value): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9]+$/', $value);
    }

    /**
     * Execute raw SQL and return results as an array. / 生SQLを実行し、結果を配列で返す
     * Equivalent to queryList() in CakePHP2. / CakePHP2の queryList() に相当。
     *
     * @param string $sql         SQL statement. / SQL文
     * @param array  $params      Bind parameters. / バインドパラメータ
     * @param string $field_name  Field name to retrieve. / 取得するフィールド名
     * @return array List of field values. / フィールド値のリスト
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
     * Execute raw SQL and return all rows as an associative array. / 生SQLを実行し、全行を連想配列で返す
     *
     * @param string $sql    SQL statement. / SQL文
     * @param array  $params Bind parameters. / バインドパラメータ
     * @return array Array of result rows. / 結果行の配列
     */
    public function rawQuery(string $sql, array $params = []): array
    {
        /** @var Connection $conn */
        $conn = $this->getConnection();
        $stmt = $conn->execute($sql, $params);
        return $stmt->fetchAll('assoc');
    }
}
