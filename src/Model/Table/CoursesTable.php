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

class CoursesTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ib_courses');
        $this->addBehavior('Timestamp');
        $this->setPrimaryKey('id');

        $this->hasMany('Contents', [
            'foreignKey' => 'course_id',
        ]);

        $this->belongsToMany('Groups', [
            'joinTable'        => 'ib_groups_courses',
            'foreignKey'       => 'course_id',
            'targetForeignKey' => 'group_id',
            'saveStrategy'     => 'replace',
        ]);

        $this->belongsToMany('Users', [
            'joinTable'        => 'ib_users_courses',
            'foreignKey'       => 'course_id',
            'targetForeignKey' => 'user_id',
            'saveStrategy'     => 'replace',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('title');

        $validator
            ->integer('sort_no');

        return $validator;
    }

    /**
     * デフォルトのソート順を適用するFinder
     * CakePHP2の $order = "Course.sort_no" に相当。
     * 使用例: $this->Courses->find('sorted')
     */
    public function findSorted(SelectQuery $query): SelectQuery
    {
        return $query->orderByAsc('sort_no');
    }

    /**
     * コースの並べ替え
     *
     * @param array $id_list コースのIDリスト（並び順）
     */
    public function setOrder(array $id_list): void
    {
        foreach ($id_list as $i => $id) {
            $sql = 'UPDATE ib_courses SET sort_no = :sort_no WHERE id = :id';
            $this->rawQuery($sql, ['sort_no' => ($i + 1), 'id' => $id]);
        }
    }

    /**
     * コースへのアクセス権限チェック
     *
     * @param int $user_id   アクセス者のユーザID
     * @param int $course_id アクセス先のコースのID
     * @return bool true: アクセス可能, false: アクセス不可
     */
    public function hasRight(int $user_id, int $course_id): bool
    {
        $conn = $this->getConnection();

        // ユーザーに直接割り当てられたコースを確認
        $stmt = $conn->execute(
            'SELECT COUNT(*) as cnt FROM ib_users_courses WHERE user_id = ? AND course_id = ?',
            [$user_id, $course_id]
        );
        $row = $stmt->fetch('assoc');
        if ((int)($row['cnt'] ?? 0) > 0) {
            return true;
        }

        // グループ経由のアクセス権を確認
        $stmt = $conn->execute(
            'SELECT COUNT(*) as cnt FROM ib_groups_courses gc'
            . ' INNER JOIN ib_users_groups ug ON gc.group_id = ug.group_id AND ug.user_id = ?'
            . ' WHERE gc.course_id = ?',
            [$user_id, $course_id]
        );
        $row = $stmt->fetch('assoc');

        return (int)($row['cnt'] ?? 0) > 0;
    }

    /**
     * コースの削除（関連レコードも含めて削除）
     *
     * @param int $course_id 削除するコースのID
     */
    public function deleteCourse(int $course_id): void
    {
        $params = ['course_id' => $course_id];

        $this->rawQuery(
            'DELETE FROM ib_contents_questions WHERE content_id IN (SELECT id FROM ib_contents WHERE course_id = :course_id)',
            $params
        );
        $this->rawQuery('DELETE FROM ib_contents WHERE course_id = :course_id', $params);
        $this->rawQuery('DELETE FROM ib_courses WHERE id = :course_id', $params);
    }
}
