<?php

namespace tests\Format;

use \whotrades\formatter\Format;

class JsonTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $data
     * @param bool $result
     *
     * @dataProvider providerIsValid
     */
    public function testIsValid($data, $result)
    {
        $this->assertEquals($result, (new Format\Json($data))->isValid());
    }

    /**
     * @return array
     */
    public function providerIsValid()
    {
        return [
            [
                'data' => '',
                'result' => false,
            ],
            [
                'data' => 'bad_data',
                'result' => false,
            ],
            [
                'data' => '<xml><node1><node2>node2</node2></node1></xml>',
                'result' => false,
            ],
            [
                'data' => '{"json":{"node1":"node1"}}',
                'result' => true,
            ],
        ];
    }

    /**
     * @param string $data
     * @param array $result
     *
     * @dataProvider providerGetAsArray
     */
    public function testGetAsArray($data, $result)
    {
        $this->assertEquals($result, (new Format\Json($data))->getAsArray());
    }

    /**
     * @return array
     */
    public function providerGetAsArray()
    {
        return [
            [
                'data' => '{"json":{"node1":"node1"}}',
                'result' => ['json' => ['node1' => 'node1']],
            ],
        ];
    }
    /**
     * @param string $data
     * @param string $result
     *
     * @dataProvider providerGetFormatted
     */
    public function testGetFormatted($data, $result)
    {
        $this->assertEquals($result, (new Format\Json($data))->getFormatted());
    }

    /**
     * @return array
     */
    public function providerGetFormatted()
    {
        return [
            [
                'data' => '{"json":{"node1":"node1"}}',
                'result' => <<<STRING
Array
(
    [json] => Array
        (
            [node1] => node1
        )

)

STRING
                ,
            ],
        ];
    }
}
