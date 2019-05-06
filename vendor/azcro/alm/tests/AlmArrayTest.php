<?php

use PHPUnit\Framework\TestCase;
use Alm\AlmArray;

final class AlmArrayTest extends TestCase
{
    private $data = array(
        'valor' => 1,
        'dato2' => 596,
        'inner' => array(
            'cosa1' => 2
        )
    );

    public function testGet(){
        $this->assertEquals(AlmArray::get($this->data, 'inner:cosa1'), 2, 'El get no funciona bien');
    }

}
