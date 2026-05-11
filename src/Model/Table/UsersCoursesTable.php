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

class UsersCoursesTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ib_users_courses');
        $this->addBehavior('Timestamp');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', ['foreignKey' => 'user_id']);
        $this->belongsTo('Courses', ['foreignKey' => 'course_id']);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator->integer('user_id');
        $validator->integer('course_id');

        return $validator;
    }

    /**
     * Get the list of enrolled courses with study history. / 学習履歴付き受講コース一覧を取得
     *
     * @param int $user_id User ID. / ユーザのID
     * @return array List of enrolled courses. / 受講コース一覧
     */
    public function getCourseRecord(int $user_id): array
    {
        $sql = <<<EOF
 SELECT Course.*, Record.first_date, Record.last_date,
       (ifnull(ContentCount.content_cnt, 0) - ifnull(CompleteCount.complete_cnt, 0)) as left_cnt
   FROM ib_courses Course
   LEFT OUTER JOIN
       (SELECT h.course_id, h.user_id,
               MAX(DATE_FORMAT(created, '%Y/%m/%d')) as last_date,
               MIN(DATE_FORMAT(created, '%Y/%m/%d')) as first_date
          FROM ib_records h
         WHERE h.user_id = :user_id
         GROUP BY h.course_id, h.user_id) Record
     ON Record.course_id = Course.id
    AND Record.user_id   = :user_id
   LEFT OUTER JOIN
        (SELECT course_id, COUNT(*) as complete_cnt
           FROM
            (SELECT r.course_id, r.content_id, COUNT(*) as cnt
               FROM ib_records r
              INNER JOIN ib_contents c ON r.content_id = c.id AND r.course_id = c.course_id
              WHERE r.user_id = :user_id
                AND c.status = 1
                AND (
                      (c.kind != 'test' AND r.is_complete = 1) OR
                      (c.kind  = 'test' AND r.is_passed   = 1)
                    )
              GROUP BY r.course_id, r.content_id) as c
          GROUP BY course_id) CompleteCount
     ON CompleteCount.course_id = Course.id
   LEFT OUTER JOIN
        (SELECT course_id, COUNT(*) as content_cnt
           FROM ib_contents
          WHERE kind NOT IN ('label', 'file')
            AND status = 1
          GROUP BY course_id) ContentCount
     ON ContentCount.course_id = Course.id
  WHERE id IN (SELECT course_id FROM ib_users_groups ug INNER JOIN ib_groups_courses gc ON ug.group_id = gc.group_id WHERE user_id = :user_id)
     OR id IN (SELECT course_id FROM ib_users_courses WHERE user_id = :user_id)
  ORDER BY Course.sort_no asc
EOF;

        return $this->rawQuery($sql, ['user_id' => $user_id]);
    }
}
