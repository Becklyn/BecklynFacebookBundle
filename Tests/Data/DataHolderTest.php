<?php

namespace Test\Becklyn\FacebookBundle\Data;

use Becklyn\FacebookBundle\Data\DataHolder;

class DataHolderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The test data structure
     *
     * @var array
     */
    private $data = array(
        "a" => 1,
        "b" => 2,
        "c" => array(
            "d" => 3,
            "e" => array(
                "f" => 4,
                "g" => array(1, 2, 3)
            )
        ),
        "y" => array(
            "z" => 1
        )
    );


    /**
     * @var DataHolder
     */
    private $dataHolder;



    /**
     *
     */
    function setUp ()
    {
        $this->dataHolder = new DataHolder($this->data);
    }



    /**
     * Provides the test cases for the test data getter
     */
    public function provider ()
    {
        return array(
            // not existing keys
            array("d", null),
            array("c.e.h", null),

            // invalid keys
            array(".a", null),
            array("a.", null),

            // existing keys in various levels
            array("a", 1),
            array("c.d", 3),
            array("c.e.f", 4),
            array("c.e.g", array(1, 2, 3)),
            array("y.z", 1),
            array("y", array("z" => 1))
        );
    }



    /**
     * Tests the data getter
     *
     * @dataProvider provider
     *
     * @param string $key
     * @param mixed $expectedValue
     */
    public function testDataGetter ($key, $expectedValue)
    {
        $actualValue = $this->dataHolder->get($key, null);
        $this->assertEquals($actualValue, $expectedValue, "Testing the key: {$key}");
    }
}