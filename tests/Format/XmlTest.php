<?php

namespace tests\Format;

use \whotrades\formatter\Format;

class XmlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $data
     * @param bool $result
     *
     * @dataProvider providerIsValid
     */
    public function testIsValid($data, $result)
    {
        $this->assertEquals($result, (new Format\Xml($data))->isValid());
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
                'result' => true,
            ],
            [
                'data' => '{"json":{"node1":"node1"}}',
                'result' => false,
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
        $this->assertEquals($result, (new Format\Xml($data))->getAsArray());
    }

    /**
     * @return array
     */
    public function providerGetAsArray()
    {
        return [
            [
                'data' => '<xml><node1><node2>node2</node2></node1></xml>',
                'result' => ['xml' => ['node1' => ['node2' => 'node2']]],
            ],
            [
                'data' => '<?xml version="1.0" encoding="utf-8"?>
<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
    <s:Header>
        <Action s:mustUnderstand="1" xmlns="http://schemas.microsoft.com/ws/2005/05/addressing/none">EventAccountCreate</Action>
    </s:Header>
    <s:Body>
        <Account>
            <Guid>6F211848-4CCC-4BE7-88B9-82A7DE661186</Guid>
            <ID>WT1487251471</ID>
            <Agreement>
                <Number>App81582</Number>
            </Agreement>
        </Account>
    </s:Body>
</s:Envelope>',
                'result' => [
                    'Envelope' => [
                        '@prefix' => 's',
                        '@ns' => 'http://schemas.xmlsoap.org/soap/envelope/',
                        'Header' => [
                            '@prefix' => 's',
                            '@ns' => 'http://schemas.xmlsoap.org/soap/envelope/',
                            'Action' => [
                                '@attributes' => [
                                    's:mustUnderstand' => '1',
                                ],
                                '@value' => 'EventAccountCreate',
                                '@ns' => 'http://schemas.microsoft.com/ws/2005/05/addressing/none',
                            ],
                        ],
                        'Body' => [
                            '@prefix' => 's',
                            '@ns' => 'http://schemas.xmlsoap.org/soap/envelope/',
                            'Account' => [
                                'Guid' => '6F211848-4CCC-4BE7-88B9-82A7DE661186',
                                'ID' => 'WT1487251471',
                                'Agreement' => [
                                    'Number' => 'App81582',
                                ],
                            ],
                        ],
                    ],
                ],
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
        $this->assertEquals($result, (new Format\Xml($data))->getFormatted());
    }

    /**
     * @return array
     */
    public function providerGetFormatted()
    {
        return [
            [
                'data' => '<xml><node1><node2>node2</node2></node1></xml>',
                'result' => <<<STRING
<xml>
  <node1>
    <node2>node2</node2>
  </node1>
</xml>
STRING
                ,
            ],
        ];
    }
}
