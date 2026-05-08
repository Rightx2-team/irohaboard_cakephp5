<?php
declare(strict_types=1);

namespace App\Model\Table;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Event\EventInterface;
use Cake\ORM\Entity;
use Cake\ORM\Query\SelectQuery;
use Cake\Validation\Validator;

class UsersTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('ib_users');
        $this->addBehavior('Timestamp');
        $this->setPrimaryKey('id');

        $this->belongsToMany('Courses', [
            'joinTable'        => 'ib_users_courses',
            'foreignKey'       => 'user_id',
            'targetForeignKey' => 'course_id',
            'saveStrategy'     => 'replace',
        ]);
        $this->belongsToMany('Groups', [
            'joinTable'        => 'ib_users_groups',
            'foreignKey'       => 'user_id',
            'targetForeignKey' => 'group_id',
            'saveStrategy'     => 'replace',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->add('username', [
                'isUnique' => [
                    'rule'     => 'validateUnique',
                    'provider' => 'table',
                    'message'  => 'Login ID is duplicated',
                ],
                'alphaNumericMB' => [
                    'rule'    => function ($value) {
                        return $this->alphaNumericMB($value);
                    },
                    'message' => 'Login ID must be alphanumeric',
                ],
                'between' => [
                    'rule'    => ['lengthBetween', 4, 32],
                    'message' => 'Login ID must be 4 to 32 characters',
                ],
            ]);

        $validator->notEmptyString('name', 'Name is required');
        $validator->notEmptyString('role', 'Role is required');

        $validator
            ->add('password', [
                'alphaNumericMB' => [
                    'rule'    => function ($value) {
                        return $this->alphaNumericMB($value);
                    },
                    'message' => 'Password must be alphanumeric',
                ],
                'between' => [
                    'rule'    => ['lengthBetween', 4, 32],
                    'message' => 'Password must be 4 to 32 characters',
                ],
            ]);

        $validator
            ->allowEmptyString('new_password')
            ->add('new_password', [
                'alphaNumericMB' => [
                    'rule'    => function ($value) {
                        return $this->alphaNumericMB($value);
                    },
                    'message' => 'Password must be alphanumeric',
                    'on'      => function ($context) {
                        return !empty($context['data']['new_password']);
                    },
                ],
                'between' => [
                    'rule'    => ['lengthBetween', 4, 32],
                    'message' => 'Password must be 4 to 32 characters',
                    'on'      => function ($context) {
                        return !empty($context['data']['new_password']);
                    },
                ],
            ]);

        return $validator;
    }

    public function findSorted(SelectQuery $query): SelectQuery
    {
        return $query->orderByAsc('name');
    }

    public function beforeSave(EventInterface $event, Entity $entity, \ArrayObject $options): void
    {
        if ($entity->isDirty('password') && $entity->password !== null) {
            $entity->password = (new DefaultPasswordHasher())->hash($entity->password);
        }
    }

    public function deleteUserRecords(int $user_id): void
    {
        $params = ['user_id' => $user_id];

        $this->rawQuery(
            'DELETE FROM ib_records_questions WHERE record_id IN (SELECT id FROM ib_records WHERE user_id = :user_id)',
            $params
        );
        $this->rawQuery('DELETE FROM ib_records WHERE user_id = :user_id', $params);
    }
}