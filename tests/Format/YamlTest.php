<?php

namespace tests\Format;

use \whotrades\formatter\Format;

class YamlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $data
     * @param bool $result
     *
     * @dataProvider providerIsValid
     */
    public function testIsValid($data, $result)
    {
        $this->assertEquals($result, (new Format\Yaml($data))->isValid());
    }

    /**
     * @return array
     */
    public function providerIsValid()
    {
        return [
            [
                'data' => '<xml><node1><node2>node2</node2></node1></xml>',
                'result' => false,
            ],
            [
                'data' => '{"json":{"node1":"node1"}}',
                'result' => false,
            ],
            [
                'data' => 'node1:
    - node2
    - node2',
                'result' => true,
            ],
        ];
    }
}
