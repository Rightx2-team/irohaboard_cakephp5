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

class ContentsQuestionsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ib_contents_questions');
        $this->addBehavior('Timestamp');
        $this->setPrimaryKey('id');

        $this->belongsTo('Contents', [
            'foreignKey' => 'content_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('content_id');

        $validator
            ->notEmptyString('question_type');

        $validator
            ->notEmptyString('body');

        $validator
            ->add('score', 'validRange', [
                'rule' => ['range', -1, 101],
                'message' => '0-100の整数で入力して下さい。',
            ]);

        $validator
            ->integer('sort_no');

        return $validator;
    }

    /**
     * Reorder questions. / 問題の並べ替え
     *
     * @param array $id_list List of question IDs in sort order. / 問題のIDリスト（並び順）
     */
    public function setOrder(array $id_list): void
    {
        foreach ($id_list as $i => $id) {
            $sql = 'UPDATE ib_contents_questions SET sort_no = :sort_no WHERE id = :id';
            $this->rawQuery($sql, ['sort_no' => ($i + 1), 'id' => $id]);
        }
    }

    /**
     * Get the sort number for a new question. / 新規追加時の問題のソート番号を取得
     *
     * @param int $content_id ID of the content (test). / コンテンツ(テスト)のID
     * @return int Sort number. / ソート番号
     */
    public function getNextSortNo(int $content_id): int
    {
        $result = $this->find()
            ->select(['sort_no' => $this->find()->func()->max('sort_no')])
            ->where(['content_id' => $content_id])
            ->first();

        return (int)($result['sort_no'] ?? 0) + 1;
    }
}
