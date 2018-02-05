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
    public function testGeneric($data, $forceFormat, array $result)
    {
        $dataFormatItem = formatter\Formatter::factory($data, $forceFormat);
        $this->assertEquals($result['format_name'], $dataFormatItem->getFormatName());
        $this->assertEquals($result['errors'], (bool) $dataFormatItem->getErrorList());
    }

    /**
     * @return array
     */
    public function providerGeneric()
    {
        return [
            // ag: Errors
            [
                'data' => '',
                'forceFormat' => null,
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_UNSUPPORTED,
                    'errors' => true,
                ],
            ],
            [
                'data' => 'bad_data',
                'forceFormat' => null,
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_UNSUPPORTED,
                    'errors' => true,
                ],
            ],
            // ag: Valid data
            [
                'data' => '<xml><node1><node2>node2</node2></node1></xml>',
                'forceFormat' => null,
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_XML,
                    'errors' => false,
                ],
            ],
            [
                'data' => '{"json":{"node1":"node1"}}',
                'forceFormat' => null,
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_JSON,
                    'errors' => false,
                ],
            ],
            [
                'data' => 'node1:
    - node2
    - node2',
                'forceFormat' => null,
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_YAML,
                    'errors' => false,
                ],
            ],
            [
                'data' => '---
- name: deploy gwserver-conf (dev)
  hosts: \'{{ target }}\'
  roles:
    - { role: gwserver-conf, dld: dev,   upstream: "test-server:80", listen_opt: default_server }
    - { role: gwserver-conf, dld: un,    upstream: "192.168.34.23:8080",            listen_opt: "", ssh_port: 2345, rds_login: \'user_name\' }     # user_name
',
                'forceFormat' => null,
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_YAML,
                    'errors' => false,
                ],
            ],
            // ag: One line YAML
            [
                'data' => '',
                'forceFormat' => 'yaml',
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_YAML,
                    'errors' => false,
                ],
            ],
            [
                'data' => 'one_line_yaml',
                'forceFormat' => 'yaml',
                'result' => [
                    'format_name' => Format\Base::DATA_FORMAT_YAML,
                    'errors' => false,
                ],
            ],
        ];
    }
}
