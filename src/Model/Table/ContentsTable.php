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

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

class ContentsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ib_contents');
        $this->addBehavior('Timestamp');
        $this->setPrimaryKey('id');

        // アソシエーション（CakePHP2の $belongsTo に相当）
        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
    }

    /**
     * バリデーションルール（CakePHP2の $validate に相当）
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('course_id')
            ->allowEmptyString('course_id');

        $validator
            ->integer('user_id')
            ->allowEmptyString('user_id');

        $validator
            ->notEmptyString('title');

        $validator
            ->notEmptyString('status');

        $validator
            ->range('timelimit', [0, 100])
            ->allowEmptyString('timelimit')
            ->add('timelimit', 'validRange', [
                'rule' => ['range', 0, 101],
                'message' => '1-100の整数で入力して下さい。',
            ]);

        $validator
            ->range('pass_rate', [0, 100])
            ->allowEmptyString('pass_rate')
            ->add('pass_rate', 'validRange', [
                'rule' => ['range', 0, 101],
                'message' => '1-100の整数で入力して下さい。',
            ]);

        $validator
            ->range('question_count', [0, 100])
            ->allowEmptyString('question_count')
            ->add('question_count', 'validRange', [
                'rule' => ['range', 0, 101],
                'message' => '1-100の整数で入力して下さい。',
            ]);

        $validator
            ->notEmptyString('kind');

        $validator
            ->integer('sort_no');

        return $validator;
    }

    /**
     * 学習履歴付きコンテンツ一覧を取得
     *
     * @param int    $user_id   取得対象のユーザID
     * @param int    $course_id 取得対象のコースID
     * @param string $role      取得者の権限（admin の場合、非公開コンテンツも取得）
     * @return array 学習履歴付きコンテンツ一覧
     */
    public function getContentRecord(int $user_id, int $course_id, string $role = 'user'): array
    {
        $sql = <<<EOF
 SELECT Content.*, first_date, last_date, record_id, Record.study_sec, Record.study_count,
       (SELECT understanding
          FROM ib_records h1
         WHERE h1.id = Record.record_id
         ORDER BY created
          DESC LIMIT 1) as understanding,
       (SELECT ifnull(is_passed, 0)
          FROM ib_records h2
         WHERE h2.id = Record.record_id
         ORDER BY created
          DESC LIMIT 1) as is_passed,
        CompleteRecord.is_complete
   FROM ib_contents Content
   LEFT OUTER JOIN
       (SELECT h.content_id, h.user_id,
               MAX(DATE_FORMAT(created, '%Y/%m/%d')) as last_date,
               MIN(DATE_FORMAT(created, '%Y/%m/%d')) as first_date,
               MAX(id) as record_id,
               SUM(ifnull(study_sec, 0)) as study_sec,
               COUNT(*) as study_count
          FROM ib_records h
         WHERE h.user_id    = :user_id
           AND h.course_id  = :course_id
         GROUP BY h.content_id) Record
     ON Record.content_id  = Content.id
   LEFT OUTER JOIN
       (SELECT r.content_id, 1 as is_complete
          FROM ib_records r
         INNER JOIN ib_contents c ON r.content_id = c.id AND r.course_id = c.course_id
         WHERE r.user_id    = :user_id
           AND r.course_id  = :course_id
           AND c.status = 1
           AND (
                 (c.kind != 'test' AND r.is_complete = 1) OR
                 (c.kind  = 'test' AND r.is_passed   = 1)
               )
         GROUP BY r.content_id) as CompleteRecord
     ON CompleteRecord.content_id = Content.id
  WHERE Content.course_id  = :course_id
    AND (status = 1 OR 'admin' = :role)
  ORDER BY Content.sort_no
EOF;

        $params = [
            'user_id'   => $user_id,
            'course_id' => $course_id,
            'role'      => $role,
        ];

        return $this->rawQuery($sql, $params);
    }

    /**
     * コンテンツの並べ替え
     *
     * @param array $id_list コンテンツのIDリスト（並び順）
     */
    public function setOrder(array $id_list): void
    {
        foreach ($id_list as $i => $id) {
            $sql = 'UPDATE ib_contents SET sort_no = :sort_no WHERE id = :id';
            $this->rawQuery($sql, ['sort_no' => ($i + 1), 'id' => $id]);
        }
    }

    /**
     * 新規追加時のコンテンツのソート番号を取得
     *
     * @param int $course_id コースID
     * @return int ソート番号
     */
    public function getNextSortNo(int $course_id): int
    {
        $result = $this->find()
            ->select(['sort_no' => $this->find()->func()->max('sort_no')])
            ->where(['course_id' => $course_id])
            ->first();

        return (int)($result['sort_no'] ?? 0) + 1;
    }
}
