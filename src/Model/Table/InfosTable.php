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

use Cake\ORM\Query\SelectQuery;
use Cake\Validation\Validator;

class InfosTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ib_infos');
        $this->addBehavior('Timestamp');
        $this->setPrimaryKey('id');

        $this->belongsToMany('Groups', [
            'joinTable'        => 'ib_infos_groups',
            'foreignKey'       => 'info_id',
            'targetForeignKey' => 'group_id',
            'saveStrategy'     => 'replace',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator->notEmptyString('title');
        $validator->integer('user_id');

        return $validator;
    }

    /**
     * Get the list of announcements. / お知らせ一覧を取得
     *
     * @param int      $user_id User ID. / ユーザID
     * @param int|null $limit   Number of records to retrieve. / 取得件数
     * @return array List of announcements. / お知らせ一覧
     */
    public function getInfos(int $user_id, ?int $limit = null): array
    {
        $info_id_list = $this->getInfoIdList($user_id, $limit);

        $query = $this->find()
            ->select(['id', 'title', 'created'])
            ->where(['id IN' => $info_id_list])
            ->orderByDesc('created');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->toArray();
    }

    /**
     * Check access permission to an announcement. / お知らせへのアクセス権限チェック
     *
     * @param int $user_id User ID. / ユーザID
     * @param int $info_id Announcement ID. / お知らせID
     * @return bool true: accessible, false: not accessible. / true: アクセス可能, false: アクセス不可
     */
    public function hasRight(int $user_id, int $info_id): bool
    {
        $info_id_list = $this->getInfoIdList($user_id);

        return in_array($info_id, $info_id_list, true);
    }

    /**
     * Get the list of viewable announcement IDs. / 閲覧可能なお知らせのIDリストを取得
     *
     * @param int      $user_id User ID. / ユーザID
     * @param int|null $limit   Number of records to retrieve. / 取得件数
     * @return array List of announcement IDs. / お知らせIDリスト
     */
    private function getInfoIdList(int $user_id, ?int $limit = null): array
    {
        $sql = <<<EOF
	SELECT
		Info.id
	FROM
		ib_infos AS Info
		LEFT OUTER JOIN ib_infos_groups AS InfoGroup ON ( Info.id = InfoGroup.info_id )
	WHERE
		InfoGroup.group_id IS NULL
		OR InfoGroup.group_id IN ( SELECT group_id FROM ib_users_groups WHERE user_id = :user_id )
	GROUP BY
		Info.id
	ORDER BY Info.created desc
EOF;

        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int)$limit;
        }

        $rows = $this->rawQuery($sql, ['user_id' => $user_id]);

        $info_id_list = array_column($rows, 'id');

        // If no matching announcement IDs exist, add a dummy ID to prevent errors. / 該当するお知らせIDが1件も存在しない場合、エラー防止のためダミーIDを追加
        if (count($info_id_list) === 0) {
            $info_id_list[] = 0;
        }

        return $info_id_list;
    }
}
