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

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Content extends Entity
{
    /**
     * Fields allowed for mass assignment. / 一括代入を許可するフィールド
     * Must be explicitly specified in CakePHP5. / CakePHP5では明示的に指定が必要
     */
    protected array $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
