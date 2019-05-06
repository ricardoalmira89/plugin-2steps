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
class AlmValidator
{

    public static function validate($data, $validations = [], $trows = true){
        if (count($validations) == 0)
            return true;

        $errors = [];

        foreach ($validations as $field => $validation){
            $filters = static::getFilters($validation);

            if (count($filters) == 0)
                throw new ValidatorException("Se debe especificar al menos un filtro para la validacion");

            foreach ($filters as $filter){
                $guessed = static::guessFilter($filter);

                if ($guessed == Validator::REQUIRED){
                    $validation = Validator::requiredFilter($data, $field);

                    if (!$validation)
                        $errors[] = sprintf('El campo %s es requerido', $field);
                }

                if ($guessed == Validator::NUMERIC){
                    $validation = Validator::numericFilter($data, $field);
                    if (!$validation)
                        $errors[] = sprintf('El campo %s debe ser numerico', $field);
                }

                if ($guessed == Validator::MAXIMUM_STRING_LENGTH){
                    $validation = Validator::maxLenFilter($data, $field, $filter);
                    if (!$validation['valid']){

                        foreach ($validation['failed'] as $v)
                            $errors[] = sprintf('El campo %s debe tener un maximo de %s caracteres', $v['key'], $v['max']);

                    }
                }

                if ($guessed == Validator::MINIMUM_VALUE){
                    $validation = Validator::minFilter($data, $field, $filter);
                    if (!$validation)
                        $errors[] = sprintf('El campo %s no esta en el rango especificado %s', $field, $filter);
                }

                if ($guessed == Validator::MULTIPLE){
                    $validation = Validator::multipleFilter($data, $field, $filter);
                    if (!$validation){
                        $missing = AlmArray::existAll($data, $field)['missing'];

                        $tokens = preg_split('/\s+/', $filter);

                        /**
                         * Construyendo mensaje de error personalizado
                         */
                        foreach ($missing as $m){
                            $errors[] = sprintf('El campo %s es requerido cuando %s %s %s',
                                $m,
                                $tokens[2],
                                $tokens[3],
                                $tokens[4]
                            );
                        }

                    }
                }
            }

        }

        if (count($errors) > 0){

            if ($trows)
                throw new ValidatorException(AlmString::arrayToText($errors));

            return false;

        }

        return true;
    }

    private static function getFilters($validation){
        return AlmString::commaSplit($validation, '|');
    }

    private static function guessFilter($filter){

        $params = AlmString::commaSplit($filter, ':');
        $filter = $params[0];

        if ($filter == 'req')
            return Validator::REQUIRED;

        if ($filter == 'num')
            return Validator::NUMERIC;

        if ($filter == 'min'){
            return Validator::MINIMUM_VALUE;
        }

        if ($filter == 'maxlen'){
            return Validator::MAXIMUM_STRING_LENGTH;
        }

        if (preg_match('/req when/', $filter, $matches)){
            return Validator::MULTIPLE;
        }


        return null;
    }

}

class Validator {
    const REQUIRED = 'req';
    const NUMERIC = 'num';
    const MINIMUM_VALUE = 'min';
    const MAXIMUM_VALUE = 'max';
    const MAXIMUM_STRING_LENGTH = 'maxlen';
    const MULTIPLE = 'when';

    public static function requiredFilter($data, $key){
        return AlmArray::get($data, $key) != null;
    }

    public static function numericFilter($data, $key){
        $value = AlmArray::get($data, $key);

        if ($value)
            return is_numeric($value);

        return true;
    }

    public static function minFilter($data, $key, $filter){
        $value = AlmArray::get($data, $key);

        $min = preg_replace('/min\:/', '', $filter);

        if (is_numeric($min)){
            if ($value){
                $value = (integer)$value;
                return $value >= $min;
            }
        } else throw new ValidatorException("Filtro Invalido min");

        return true;
    }

    public static function maxLenFilter($data, $commaKey, $filter){
        $max = preg_replace('/maxlen\:/', '', $filter);

        $keys = AlmString::commaSplit($commaKey);
        $failed = [];

        foreach ($keys as $key){
            $value = AlmArray::get($data, $key);
            if ($value){
                if (strlen($value) > $max)
                    $failed[] = ['key' => $key, 'max' => $max];
            }
        }

        return array(
            'valid'   => count($failed) == 0,
            'failed' => $failed
        );
      }

    public static function multipleFilter($data, $key, $filter){
        $filter = preg_replace('/req when /', '', $filter);
        $operators = ['==','>','<','>=','<='];

        $tokens = preg_split('/\s+/', $filter);
        if (count($tokens) != 3)
            throw new ValidatorException('Invalid Arguments');

        $operator = $tokens[1];
        if (!in_array($operator, $operators))
            throw new ValidatorException(sprintf("Invalid Operator '%s'.", $operator));


        switch ($operator) {
            case '==':
                $valid = AlmArray::get($data, $tokens[0]) == $tokens[2];
                return ($valid)
                    ? AlmArray::existAll($data, $key)['valid']
                    : true;
                break;
            case '<':
                $valid = AlmArray::get($data, $tokens[0]) < $tokens[2];
                return ($valid)
                    ? AlmArray::existAll($data, $key)['valid']
                    : true;
                break;
            case '>':
                $valid = AlmArray::get($data, $tokens[0]) > $tokens[2];
                return ($valid)
                    ? AlmArray::existAll($data, $key)['valid']
                    : true;
                break;
            case '>=':
                $valid = AlmArray::get($data, $tokens[0]) >= $tokens[2];
                return ($valid)
                    ? AlmArray::existAll($data, $key)['valid']
                    : true;
                break;
            case '<=':
                $valid = AlmArray::get($data, $tokens[0]) <= $tokens[2];
                return ($valid)
                    ? AlmArray::existAll($data, $key)['valid']
                    : true;
                break;
        }

        return false;
    }

}

class ValidatorException extends \Exception{

}