<?php

class CCompatible
{
    /**
     * PHP8.1からビルトイン関数の型が厳格になったため、特にstr_replace()について許容的なラップ関数を定義
     * 
     * @param string|array|int|null|mixed $search
     * @param string|array|int|null|mixed $replace
     * @param string|array|int|null|mixed $subject
     * @param ?int &$count
     * @return string|array
     * @see https://www.php.net/manual/ja/migration81.deprecated.php
     */
    public static function str_replace_allowed_arg ($search, $replace, $subject, &$count = null) {
        $allowedValueOfSearch = CCompatible::cast_for_str_replace_allowed_arg($search);
        $allowedValueOfReplace = CCompatible::cast_for_str_replace_allowed_arg($replace);
        $allowedValueOfSubject = CCompatible::cast_for_str_replace_allowed_arg($subject);
        return str_replace($allowedValueOfSearch, $allowedValueOfReplace, $allowedValueOfSubject, $count);
    }

    /**
     * PHP8.1以前のビルトイン関数str_replace()の引数の暗黙的なキャストを再現する
     * 
     * @param string|array|int|null|mixed $str_replace_arg
     * @return string|array|mixed
     */
    private static function cast_for_str_replace_allowed_arg ($str_replace_arg) {
        if (is_string($str_replace_arg) || is_array($str_replace_arg)) {
            return $str_replace_arg;
        } else if (is_integer($str_replace_arg)) {
            return (string)$str_replace_arg;
        } else if (is_null($str_replace_arg)) {
            return '';
        } else {
            // 想定するエラー以外はデフォルトで返す
            // 本件でエラーが出た場合は都度修正する
            return $str_replace_arg;
        }
    }
}
