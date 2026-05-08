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

class UsersGroupsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ib_users_groups');
        $this->addBehavior('Timestamp');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', ['foreignKey' => 'user_id']);
        $this->belongsTo('Groups', ['foreignKey' => 'group_id']);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator->integer('user_id');
        $validator->integer('group_id');

        return $validator;
    }
}
