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
     * Finder that applies the default sort order. / デフォルトのソート順を適用するFinder
     * Equivalent to $order = "Course.sort_no" in CakePHP2. / CakePHP2の $order = "Course.sort_no" に相当。
     * Usage example: $this->Courses->find('sorted') / 使用例: $this->Courses->find('sorted')
     */
    public function findSorted(SelectQuery $query): SelectQuery
    {
        return $query->orderByAsc('sort_no');
    }

    /**
     * Reorder courses. / コースの並べ替え
     *
     * @param array $id_list List of course IDs in sort order. / コースのIDリスト（並び順）
     */
    public function setOrder(array $id_list): void
    {
        foreach ($id_list as $i => $id) {
            $sql = 'UPDATE ib_courses SET sort_no = :sort_no WHERE id = :id';
            $this->rawQuery($sql, ['sort_no' => ($i + 1), 'id' => $id]);
        }
    }

    /**
     * Check access permission to a course. / コースへのアクセス権限チェック
     *
     * @param int $user_id   User ID of the accessor. / アクセス者のユーザID
     * @param int $course_id ID of the target course. / アクセス先のコースのID
     * @return bool true: accessible, false: not accessible. / true: アクセス可能, false: アクセス不可
     */
    public function hasRight(int $user_id, int $course_id): bool
    {
        $conn = $this->getConnection();

        // Check courses directly assigned to the user. / ユーザーに直接割り当てられたコースを確認
        $stmt = $conn->execute(
            'SELECT COUNT(*) as cnt FROM ib_users_courses WHERE user_id = ? AND course_id = ?',
            [$user_id, $course_id]
        );
        $row = $stmt->fetch('assoc');
        if ((int)($row['cnt'] ?? 0) > 0) {
            return true;
        }

        // Check access permission via group. / グループ経由のアクセス権を確認
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
     * Delete a course (including related records). / コースの削除（関連レコードも含めて削除）
     *
     * @param int $course_id ID of the course to delete. / 削除するコースのID
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
