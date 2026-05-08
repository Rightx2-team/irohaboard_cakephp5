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

class GroupsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ib_groups');
        $this->addBehavior('Timestamp');
        $this->setPrimaryKey('id');

        // CakePHP2の hasAndBelongsToMany に相当
        $this->belongsToMany('Courses', [
            'joinTable'          => 'ib_groups_courses',
            'foreignKey'         => 'group_id',
            'targetForeignKey'   => 'course_id',
            'saveStrategy'       => 'replace',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('title');

        $validator
            ->integer('status');

        return $validator;
    }

    /**
     * デフォルトのソート順を適用するFinder
     * CakePHP2の $order = "Group.title" に相当。
     */
    public function findSorted(SelectQuery $query): SelectQuery
    {
        return $query->orderByAsc('title');
    }

    /**
     * 指定したグループに所属するユーザIDリストを取得
     *
     * @param int $group_id グループID
     * @return array ユーザIDリスト
     */
    public function getUserIdByGroupID(int $group_id): array
    {
        $sql    = 'SELECT user_id FROM ib_users_groups WHERE group_id = :group_id';
        $params = ['group_id' => $group_id];

        return $this->queryList($sql, $params, 'user_id');
    }
}
