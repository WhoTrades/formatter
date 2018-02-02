<?php

namespace tests;

use whotrades\formatter;
use whotrades\formatter\Format;

class FormatterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $data
     * @param array $result
     *
     * @dataProvider providerGeneric
     */
    public function testGeneric($data, array $result)
    {
        $dataFormatItem = formatter\Formatter::factory($data);
        $this->assertEquals($result['format_name'], $dataFormatItem->getFormatName());
        $this->assertEquals($result['errors'], (bool) $dataFormatItem->getErrorList());
    }

    /**
     * @return array
     */
    public function providerGeneric()
    {
        return [
            [
                'data' => '',
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_UNSUPPORTED,
                    'errors' => true,
                ],
            ],
            [
                'data' => 'bad_data',
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_UNSUPPORTED,
                    'errors' => true,
                ],
            ],
            [
                'data' => '<xml><node1><node2>node2</node2></node1></xml>',
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_XML,
                    'errors' => false,
                ],
            ],
            [
                'data' => '{"json":{"node1":"node1"}}',
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_JSON,
                    'errors' => false,
                ],
            ],
        ];
    }
}
