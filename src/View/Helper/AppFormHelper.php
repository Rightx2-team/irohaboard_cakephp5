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

namespace App\View\Helper;

use Cake\View\Helper\FormHelper;

/**
 * AppFormHelper
 * CakePHP2の AppFormHelper / AppHelper を統合したカスタムFormHelper。
 * CakePHP5ではBootstrapのCSSクラスは直接HTMLに書くのが推奨だが、
 * 既存テンプレートとの互換性のためメソッドを残す。
 */
class AppFormHelper extends FormHelper
{
    /**
     * 説明付きテキストボックスを出力
     * CakePHP2の AppHelper::inputExp() に相当
     *
     * @param string $fieldName フィールド名
     * @param array  $options   input オプション
     * @param string $exp       説明テキスト
     * @return string HTML
     */
    public function inputExp(string $fieldName, array $options = [], string $exp = ''): string
    {
        $expHtml = '<div class="col col-sm-3"></div>'
            . '<div class="col col-sm-9 status-exp">' . h($exp) . '</div>';

        $options['templates']['formGroup'] =
            '{{label}}<div class="col col-sm-9">{{input}}' . $expHtml . '</div>';

        return $this->control($fieldName, $options);
    }

    /**
     * ラジオボタンを出力
     * CakePHP2の AppHelper::inputRadio() に相当
     *
     * @param string $fieldName フィールド名
     * @param array  $options   オプション
     * @param string $exp       説明テキスト
     * @return string HTML
     */
    public function inputRadio(string $fieldName, array $options = [], string $exp = ''): string
    {
        $options['type']      = 'radio';
        $options['legend']    = $options['legend'] ?? false;
        $options['separator'] = $options['separator'] ?? '　';

        if ($exp !== '') {
            $options['after'] = '<div class="col col-sm-3">&nbsp;</div>'
                . '<div class="col col-sm-9 col-exp status-exp">' . h($exp) . '</div>';
        }

        return $this->control($fieldName, $options);
    }

    /**
     * 検索フィールドを出力
     * CakePHP2の AppHelper::searchField() に相当
     *
     * @param string $fieldName フィールド名
     * @param array  $options   追加オプション
     * @return string HTML
     */
    public function searchField(string $fieldName, array $options = []): string
    {
        $options = array_merge(['class' => 'form-control', 'required' => false], $options);

        if (isset($options['label'])) {
            $options['label'] .= ' :';
        }

        return $this->control($fieldName, $options);
    }

    /**
     * 検索用日付フィールドを出力
     * CakePHP2の AppHelper::searchDate() に相当
     *
     * @param string $fieldName フィールド名
     * @param array  $options   追加オプション
     * @return string HTML
     */
    public function searchDate(string $fieldName, array $options = []): string
    {
        $defaults = [
            'type'  => 'date',
            'class' => 'form-control',
            'style' => 'width:initial; display: inline;',
        ];
        $options = array_merge($defaults, $options);

        if (isset($options['label']) && $options['label'] !== '～') {
            $options['label'] .= ' :';
        }

        return $this->control($fieldName, $options);
    }

    /**
     * 説明ブロックを出力（静的）
     * CakePHP2の AppHelper::block() に相当
     *
     * @param string $label       ラベル
     * @param string $content     内容
     * @param bool   $is_bold     太字にするか
     * @param string $block_class 追加CSSクラス
     * @return string HTML
     */
    public static function block(string $label, string $content, bool $is_bold = false, string $block_class = ''): string
    {
        $content = $is_bold ? '<h5>' . $content . '</h5>' : $content;

        return '<div class="form-group ' . h($block_class) . '">'
            . '<label class="col col-sm-3 control-label">' . h($label) . '</label>'
            . '<div class="col col-sm-9">' . $content . '</div>'
            . '</div>';
    }
}
