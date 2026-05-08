<?php
/**
 * Utils - CakePHP5用ユーティリティクラス
 * CakePHP2のVendor/Utils.phpの互換クラス
 */
class Utils
{
    /**
     * 秒数を HH:MM:SS 形式に変換
     */
    public static function getHNSBySec(?int $sec): string
    {
        if ($sec === null || $sec < 0) return '';
        $h = floor($sec / 3600);
        $m = floor(($sec % 3600) / 60);
        $s = $sec % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    /**
     * 日時を Y/m/d H:i 形式に変換
     */
    public static function getYMDHN(mixed $date): string
    {
        if (empty($date)) return '';
        if ($date instanceof \Cake\I18n\DateTime) {
            return $date->format('Y/m/d H:i');
        }
        if (is_string($date)) {
            $ts = strtotime($date);
            return $ts ? date('Y/m/d H:i', $ts) : '';
        }
        return '';
    }

    /**
     * 日時を Y/m/d 形式に変換
     */
    public static function getYMD(mixed $date): string
    {
        if (empty($date)) return '';
        if ($date instanceof \Cake\I18n\DateTime) {
            return $date->format('Y/m/d');
        }
        if (is_string($date)) {
            $ts = strtotime($date);
            return $ts ? date('Y/m/d', $ts) : '';
        }
        return '';
    }

    /**
     * 日時を Y年m月d日 形式に変換
     */
    public static function getYMDJP(mixed $date): string
    {
        if (empty($date)) return '';
        if ($date instanceof \Cake\I18n\DateTime) {
            return $date->format('Y年m月d日');
        }
        if (is_string($date)) {
            $ts = strtotime($date);
            return $ts ? date('Y年m月d日', $ts) : '';
        }
        return '';
    }
}