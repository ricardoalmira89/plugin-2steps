<?php
/**
 * Created by Ricardo Almira.
 * User: dvelop
 * Date: 05/10/2016
 * Time: 18:03
 */

namespace Alm;


/**
 * Helper de manejo de arrays
 * Class AlmArray
 * @package Alm
 */
class AlmArray
{

    /**
     * Obtiene el valor de un elemento del array (anidado)
     * de forma segura.
     *
     * @param $data array El array al que se le obtienen los datos
     * @param $path string El path deseado separado por caracter ':'
     * @param $failReturn mixed El valor que se devuelve cuando falla la obtencion del valor deseado
     * @return mixed
     */
    public static function get($data, $path, $failReturn = null){
        $paths = AlmString::commaSplit($path, ':');

        $temp = $data;
        foreach ($paths as $_path){
            if (isset($temp[$_path]))
                $temp = $temp[$_path];
            else return $failReturn;
        }

        return $temp;
    }

    /**
     * Aplica static:get() al array pasado en $data, y se accede a cada uno de los paths separados por coma.
     * El valor devuelto es la concatenacion de todos estos valores.
     *
     * Ex: $paths = 'personas:ricardo:ci,personas:pepe:ci' devolveria '89012221065,89256699887'
     *
     * @param $data
     * @param $paths
     * @param string $concat_char
     * @return null|string
     */
    public static function getConcat($data, $paths, $concat_char = ','){

        $_paths = AlmString::commaSplit($paths);
        $result = [];
        foreach ($_paths as $path){
            $tempValue = static::get($data, $path);
            if ($tempValue) $result[] = $tempValue;
                else return null;
        }

        $str = '';
        for ($i = 0; $i<= count($result) - 1; $i++){
            $str .= ($i != count($result) - 1) ?  $result[$i] . $concat_char : $result[$i];
        }

        return $str;
    }

    /**
     * Dado un array data que contiene arrays TODOS con el mismo $key, se aplica un concat
     * al valor de cada uno de los arrays internos que tengan el key especificado
     *
     * @param $data
     * @param $key
     * @param string $concat_char
     * @return  string
     */
    public static function keyConcat($data, $key, $concat_char = ','){

        $result = null;
        if (is_array($data)){

            $arr_expr = [];
            for ($i = 0; $i <= count($data) - 1; $i++)
                $arr_expr[] = sprintf("%s:%s",$i, $key);

            $expr = implode(',',$arr_expr);
            $result = AlmArray::getConcat($data, $expr, $concat_char);
        }

        return $result;
    }

    /**
     * Hace un select sencillo al array pidiendo solo los valores
     * del key $key
     *
     * @param $data
     * @param $key
     * @return array|null
     */
    public static function select($data, $key){

        $result = null;
        if (is_array($data)){
            foreach ($data as $item){
                if (isset($item[$key])) $result[] = $item[$key];
            }
        }

        return $result;
    }

    /**
     * Guarda un array a un fichero
     *
     * @param $array
     * @param $filename
     */
    public static function saveToFile($array, $filename){
        $json = json_encode($array);
        $fh = fopen($filename, "w");
        fwrite($fh, $json);
        fclose($fh);
    }

    /**
     * Carga un array de un fichero
     *
     * @param $filename
     * @return mixed
     */
    public static function loadFromFile($filename){

        if (!file_exists($filename))
            return [];

        $fh = fopen($filename, "r");
        $json = fread($fh, filesize($filename));

        return json_decode($json, true);
    }

    /**
     * Dado un array con duplicados devuelve el mismo array
     * con los duplicados eliminados. El array puede ser multidimensional o no.
     *
     * @param $array
     * @return array
     */
    public static function unique($array)
    {
        $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

        foreach ($result as $key => $value){
            if ( is_array($value) )
               $result[$key] = static::unique($value);
        }

        return $result;
    }

    /**
     * Dado un $commakeys (llaves separadas por coma) , determina si todas y cada una de ellas
     * existen en el array.
     *
     * @param $data
     * @param $commaKeys
     * @return array
     */
    public static function existAll($data, $commaKeys){

        $keys = explode(',', $commaKeys);
        if (count($keys) == 0)
            return array(
            'valid'   => false,
            'missing' => []
        );;

        $missing = [];

        foreach ($keys as $key){
            if (!isset($data[$key]))
                $missing[] = $key;
        }

        return array(
            'valid'   => count($missing) == 0,
            'missing' => $missing
        );
    }

    public static function head($data = [], $size = 10){

        $i = 0;
        $result = [];
        foreach ($data as $key => $value){
            $result[$key] = $value;

            $i++;
            if ($i == $size)
                break;
        }

        return $result;
    }


}