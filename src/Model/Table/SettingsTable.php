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

namespace App\Model\Table;

use Cake\Validation\Validator;

class SettingsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ib_settings');
        $this->addBehavior('Timestamp');
        $this->setPrimaryKey('id');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator->notEmptyString('setting_key');
        $validator->notEmptyString('setting_value');

        return $validator;
    }

    /**
     * システム設定の値のリストを取得
     *
     * @return array 設定値リスト（連想配列）
     */
    public function getSettings(): array
    {
        $rows = $this->rawQuery('SELECT setting_key, setting_value FROM ib_settings');

        $result = [];
        foreach ($rows as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
        }

        return $result;
    }

    /**
     * システム設定を保存
     *
     * @param array $settings 保存する設定値リスト（連想配列）
     */
    public function setSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->rawQuery(
                'UPDATE ib_settings SET setting_value = :setting_value WHERE setting_key = :setting_key',
                ['setting_key' => $key, 'setting_value' => $value]
            );
        }
    }
}
