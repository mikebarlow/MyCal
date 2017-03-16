<?php
namespace Snscripts\MyCal\Tests\Traits;

class AccessibleTest extends \PHPUnit_Framework_TestCase
{
    public function testWeCanSetAndGetData()
    {
        $Obj = new MyObject;

        $Obj->test = 'foobar';
        $Obj->test2 = 'bar foo';

        $this->assertSame(
            'foobar',
            $Obj->test
        );

        $this->assertSame(
            'bar foo',
            $Obj->test2
        );
    }

    public function testCustomSettersSetAndGetData()
    {
        $Obj = new MyObject;
        $Obj->foo_bar = 'string transform';
        $Obj->bar_foo = 'snake_case_to_camel';

        $this->assertSame(
            'STRINGTRANSFORM',
            $Obj->foo_bar
        );

        $this->assertSame(
            'SnakeCaseToCamel',
            $Obj->bar_foo
        );
    }

    public function testSetAllDataSetsCorrectly()
    {
        $Obj = new MyObject;

        $Obj->setAllData([
            'foo_bar' => 'string transform',
            'bar_foo' => 'snake_case_to_camel',
            'test' => 'foobar'
        ]);

        $this->assertSame(
            'STRINGTRANSFORM',
            $Obj->foo_bar
        );

        $this->assertSame(
            'SnakeCaseToCamel',
            $Obj->bar_foo
        );

        $this->assertSame(
            'foobar',
            $Obj->test
        );
    }
}

class MyObject
{
    use \Snscripts\MyCal\Traits\Accessible;

    public function setFooBarAttr($value)
    {
        $this->data['foo_bar'] = str_replace(' ', '', strtoupper($value));
    }

    public function getBarFooAttr()
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($this->data['bar_foo']))));
    }
}