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

class RecordsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ib_records');
        $this->addBehavior('Timestamp');
        $this->setPrimaryKey('id');

        $this->hasMany('RecordsQuestions', [
            'foreignKey' => 'record_id',
            'order'      => 'RecordsQuestions.id',
        ]);

        $this->belongsTo('Courses', [
            'foreignKey'  => 'course_id',
            'joinType'    => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey'  => 'user_id',
            'joinType'    => 'INNER',
        ]);
        $this->belongsTo('Contents', [
            'foreignKey'  => 'content_id',
            'joinType'    => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator->integer('course_id');
        $validator->integer('user_id');
        $validator->integer('content_id');

        return $validator;
    }
}
