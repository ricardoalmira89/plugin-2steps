<?php
/**
 * Created by PhpStorm.
 * User: dvelop
 * Date: 05/10/2016
 * Time: 18:03
 */

namespace Alm;


/**
 * Helper de manejo de strings
 * Class AlmString
 * @package Alm
 */
class AlmString
{
    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];

    /**
     * Elimina todos los espacios de una cadena
     * @param string $value
     * @return string
     */
    public static function trimSpaces($value){
        if ($value == null) return null;
        return str_replace(' ','',$value);
    }

    /**
     * Devuelve null si el string es "" ó " "
     *
     * @param $value
     * @return null
     */
    public static function parseNull($value){
        return ($value == "" || $value == " " ) ? null : $value;
    }

    /**
     * Modifica un string cuando este tiene mas de 2 caracteres
     * espacio juntos, devuelve en su lugar el mismo string pero
     * solo con 1 espacio en el lugar donde habian mas de 2 juntos
     *
     * @param $value
     * @return mixed
     */
    public static function trimLongSpaces($value){
        return preg_replace('/\s{2,}/',' ',$value);
    }

    /**
     * Dada una oracion:
     * 1- elimina los 'espacios largos'
     * 2- elimina los espacios al final y al inicio de la cadena.
     * 3- agrega punto final de no tenerlo
     * @param $value
     * @return mixed|string
     */
    public static function sentenceTrim($value){
        if ($value == "") return $value;

        $value = AlmString::trimLongSpaces($value);
        $value = trim($value);

        if ($value[strlen($value) - 1] != '.') $value .='.';

        return $value;
    }

    /**
     * @param $value
     * @return  array
     */
    public static function newLineTrim($value){
        return preg_split('/\r\n/',$value);
    }

    /**
     * El inverso de newLineTrime
     */
    public static function arrayToText($data){
        $text = '';
        foreach($data as $item){
            $text .= $item . "\r\n";
        }

        return $text;
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convierte el texto a una oracion con la primera letra mayuscula
     * @param $text
     */
    public static function title($text){
        return mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');
    }

    public static function upper($text){
        return strtoupper($text);
    }

    public static function lower($text){
        return strtolower($text);
    }

    public static function random($length = 16)
    {
        $string = '';

        while (($len = static::length($string)) < $length) {
            $size = $length - $len;

            $bytes = static::randomBytes($size);

            $string .= static::substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     *
     * @param  string  $string
     * @param  int  $start
     * @param  int|null  $length
     * @return string
     */
    public static function substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Generate a more truly "random" bytes.
     *
     * @param  int  $length
     * @return string
     */
    public static function randomBytes($length = 16)
    {
        return random_bytes($length);
    }

    /**
     * Return the length of the given string.
     *
     * @param  string  $value
     * @return int
     */
    public static function length($value)
    {
        return mb_strlen($value);
    }

    /**
     * Dado un string al estilo 'sort' (nombre.asc,simbolo.desc) devuelve el array de hash correspondiente
     * Ex: ['nombre' => 'asc', 'simbolo' => 'desc']
     * @param $text
     * @return array
     */
    public static function sortArray($text){
        $result = [];
        $elems = preg_split('/,/',$text);
        foreach($elems as $elem){
            $hash = preg_split('/\./',$elem);

            if (isset($hash[1]) && in_array($hash[1],['asc', 'desc']))
                $result[$hash[0]] = $hash[1];
        }

        return $result;
    }

    /**
     * Dado un string separado por comma, devuelve un array con los elementos que lo componen.
     * El separador se puede cambiar a cualquiera, especificando $separator,
     * pero por default es el caracter comma ','
     */
    public static function commaSplit($text, $separator = ','){

        $elems = explode($separator, $text);
        return $elems;

    }

    /**
     * Unique Array Multidimensional
     * @param  array $array Array a Ordenar
     * @param  string $key   Llave unica
     * @return array        Array Unico
     */
    public static function unique_multi_array($array, $key)
    {
        $temp_array = [];
        $i = 0;
        $key_array = [];

        foreach ( $array as $val )
        {
            if ( !in_array($val[$key], $key_array) )
            {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    public static function studly($value)
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * Reemplaza el texto $search por el texto $replace en el string $text
     *
     * @param $pattern
     * @param $replace
     * @param $text
     * @return mixed
     */
    public static function replace($pattern, $replace, $text){
        return preg_replace($pattern,$replace, $text);
    }

    /**
     * Devuelve un string unico que puede ser usado como SKU
     * @return string
     */
    public static function amzUnique(){
        return strtoupper('AMZ'. uniqid());
    }

    public static function platformUnique($iniciales){
        return strtoupper($iniciales. uniqid());
    }

    /**
     * Genera un SKU para cuanologik basado en un upc
     *
     * @param $upc
     * @return string
     */
    public static function cuanoSku($upc){
        return sprintf("CUA%sEK", $upc);
    }

    public static function hash($text){
        return hash('sha1', $text);
    }

    public static function trimEmotes($str){

        $clean = preg_replace("/&#?[a-z0-9]{2,8};/i","",$str);
        $clean2 = preg_replace('/[^\PC\s]/u', '', $clean);
        $clean3 = preg_replace('/[[:^print:]]/', '', $clean2);

        return $clean3;
    }

    public static function getSlug($string) {

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
            chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
            chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
            chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
            chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
            chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
            chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
            chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
            chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
            chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
            chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
            chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
            chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
            chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
            chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
            chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
            chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
            chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
            chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
            chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
            chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
            chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
            chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
            chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
            chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
            chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
            chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
            chr(195).chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
            chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
            chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
            chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
            chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
            chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
            chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
            chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
            chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
            chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
            chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
            chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
            chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
            chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
            chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
            chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
            chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
            chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
            chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
            chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
            chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
            chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
            chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
            chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
            chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
            chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
            chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
            chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
            chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
            chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
            chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
            chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
            chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
            chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
            chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
            chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
            chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
            chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
            chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
            chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
            chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
            chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
            chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
            chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
            chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
            chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
            chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
            chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
            chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
            chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
            chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
            chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
            chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
            chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
            chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
            chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
            chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
            chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
            chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
            chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
            chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
            chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
            chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
            chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
        );

        $string = strtr($string, $chars);
        $string = str_replace('&', 'y', $string);

        for($i = 0; $i < 3; $i++){
            $string = strtolower(
                preg_replace(
                    array('#[\\s-]+#','#[^A-Za-z0-9 -]+#'),
                    array('-', ''),
                    static::cleanString(
                        urldecode($string)
                    )
                )
            );
        }

        return urlencode(trim($string));
    }

    public static function cleanString($text){
        $trans = get_html_translation_table(HTML_ENTITIES);
        $trans[chr(130)] = '&sbquo;';     // Single Low-9 Quotation Mark
        $trans[chr(131)] = '&fnof;';      // Latin Small Letter F With Hook
        $trans[chr(132)] = '&bdquo;';     // Double Low-9 Quotation Mark
        $trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
        $trans[chr(134)] = '&dagger;';    // Dagger
        $trans[chr(135)] = '&Dagger;';    // Double Dagger
        $trans[chr(136)] = '&circ;';      // Modifier Letter Circumflex Accent
        $trans[chr(137)] = '&permil;';    // Per Mille Sign
        $trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
        $trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
        $trans[chr(140)] = '&OElig;';     // Latin Capital Ligature OE
        $trans[chr(145)] = '&lsquo;';     // Left Single Quotation Mark
        $trans[chr(146)] = '&rsquo;';     // Right Single Quotation Mark
        $trans[chr(147)] = '&ldquo;';     // Left Double Quotation Mark
        $trans[chr(148)] = '&rdquo;';     // Right Double Quotation Mark
        $trans[chr(149)] = '&bull;';      // Bullet
        $trans[chr(150)] = '&ndash;';     // En Dash
        $trans[chr(151)] = '&mdash;';     // Em Dash
        $trans[chr(152)] = '&tilde;';     // Small Tilde
        $trans[chr(153)] = '&trade;';     // Trade Mark Sign
        $trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
        $trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
        $trans[chr(156)] = '&oelig;';     // Latin Small Ligature OE
        $trans[chr(159)] = '&Yuml;';      // Latin Capital Letter Y With Diaeresis
        $trans['euro'] = '&euro;';        // euro currency symbol
        ksort($trans);

        foreach ($trans as $k => $v) {
            $text = str_replace($v, $k, $text);
        }

        return $text;
    }

}