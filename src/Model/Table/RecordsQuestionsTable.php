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

class RecordsQuestionsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // Map to the ib_-prefixed table name / ib_プレフィックス付きのテーブル名にマッピング
        $this->setTable('ib_records_questions');
        $this->addBehavior('Timestamp');
        $this->setPrimaryKey('id');

        $this->belongsTo('Records', [
            'foreignKey' => 'record_id',
        ]);
        $this->belongsTo('ContentsQuestions', [
            'foreignKey' => 'question_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator->integer('record_id');
        $validator->integer('question_id');

        return $validator;
    }
}
