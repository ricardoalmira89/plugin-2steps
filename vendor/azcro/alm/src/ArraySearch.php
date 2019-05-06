<?php
/**
 * Created by Ricardo Almira.
 * User: root
 * Date: 30/11/18
 * Time: 10:32 AM
 */

namespace Alm;

class ArraySearch
{

    const OPERATOR_EQ = '=';
    const OPERATOR_LIKE = 'like';
    const OPERATOR_NEQ = '!=';
    const OPERATOR_AND = '&&';

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function where($expr){

        $splitted = $this->splitMultiple($expr);

        foreach ($splitted as $innerExpr){

            $o = $this->getOperator($innerExpr);
            $kv = $this->getKeyValue($innerExpr);

            $result = [];
            foreach ($this->data as $elem){
                $val = AlmArray::get($elem, $kv['key']);

                if (!is_array($kv['value'])){

                    if ($o == self::OPERATOR_EQ && $val == $kv['value'])
                        $result[] = $elem;

                    if ($o == self::OPERATOR_NEQ && $val != $kv['value'])
                        $result[] = $elem;

                    if ($o == self::OPERATOR_LIKE && $this->like('aa','aa'))
                        throw new \Exception('Like no implementado');
                    //$result[] = $elem;
                }


            }

            $this->data = $result;

        }

        return $this->data;
    }

    /**
     * Obtiene
     * @param $expr
     * @return array[]|false|string[]
     */
    private function getKeyValue($expr){
        $data = preg_split("/(\s*\=\s*|\s*like\s*|\s*\!\=\s*)/", $expr);

        return array(
            'key'   => ltrim($data[0]),
            'value' => rtrim($data[1])
        );
    }

    /**
     * Obtiene el operador de la expresion
     * @param $expr
     * @return string
     */
    private function getOperator($expr){

        if (preg_match(sprintf('/\%s/', self::OPERATOR_NEQ), $expr))
            return self::OPERATOR_NEQ;

        if (preg_match(sprintf('/\%s/', self::OPERATOR_EQ), $expr))
            return self::OPERATOR_EQ;

        if (preg_match(sprintf('/%s/', self::OPERATOR_LIKE), $expr))
            return self::OPERATOR_LIKE;

    }

    /**
     * Determina si el query es multiple
     * Ex: 'prop == value && prop2 != value2'
     * @param $expr
     * @return false|int
     */
    private function isMultiple($expr){
        return  (preg_match(sprintf('/\%s/', self::OPERATOR_AND), $expr));
    }

    /**
     * Divide el query grande en querys pequeños
     * @param $expr
     * @return array[]|false|string[]
     */
    private function splitMultiple($expr){
        return preg_split('/\&\&/', $expr );
    }

    private function like($elemstr, $search){
        return true;
    }
}