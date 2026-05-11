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
 * Custom FormHelper integrating AppFormHelper / AppHelper from CakePHP2. / CakePHP2の AppFormHelper / AppHelper を統合したカスタムFormHelper。
 * In CakePHP5, it is recommended to write Bootstrap CSS classes directly in HTML, / CakePHP5ではBootstrapのCSSクラスは直接HTMLに書くのが推奨だが、
 * but methods are kept for compatibility with existing templates. / 既存テンプレートとの互換性のためメソッドを残す。
 */
class AppFormHelper extends FormHelper
{
    /**
     * Output a text box with description. / 説明付きテキストボックスを出力
     * Equivalent to AppHelper::inputExp() in CakePHP2. / CakePHP2の AppHelper::inputExp() に相当
     *
     * @param string $fieldName Field name. / フィールド名
     * @param array  $options   Input options. / input オプション
     * @param string $exp       Description text. / 説明テキスト
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
     * Output radio buttons. / ラジオボタンを出力
     * Equivalent to AppHelper::inputRadio() in CakePHP2. / CakePHP2の AppHelper::inputRadio() に相当
     *
     * @param string $fieldName Field name. / フィールド名
     * @param array  $options   Options. / オプション
     * @param string $exp       Description text. / 説明テキスト
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
     * Output a search field. / 検索フィールドを出力
     * Equivalent to AppHelper::searchField() in CakePHP2. / CakePHP2の AppHelper::searchField() に相当
     *
     * @param string $fieldName Field name. / フィールド名
     * @param array  $options   Additional options. / 追加オプション
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
     * Output a date field for search. / 検索用日付フィールドを出力
     * Equivalent to AppHelper::searchDate() in CakePHP2. / CakePHP2の AppHelper::searchDate() に相当
     *
     * @param string $fieldName Field name. / フィールド名
     * @param array  $options   Additional options. / 追加オプション
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
     * Output a description block (static). / 説明ブロックを出力（静的）
     * Equivalent to AppHelper::block() in CakePHP2. / CakePHP2の AppHelper::block() に相当
     *
     * @param string $label       Label. / ラベル
     * @param string $content     Content. / 内容
     * @param bool   $is_bold     Whether to bold. / 太字にするか
     * @param string $block_class Additional CSS class. / 追加CSSクラス
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
