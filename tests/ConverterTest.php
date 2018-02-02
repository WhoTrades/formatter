<?php

namespace tests;

use \whotrades\formatter;
use \whotrades\formatter\Format;

class ConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $data
     * @param array $result
     *
     * @dataProvider providerDom2array
     */
    public function testDom2array($data, array $result)
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($data);

        $this->assertEquals($result, formatter\Converter::dom2array($dom));
    }

    /**
     * @return array
     */
    public function providerDom2array()
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
            [
                'data' => '
<xml>
    <node1>Node11</node1>
    <node1>Node12</node1>
    <node1>Node13</node1>
</xml>',
                'result' => [
                    'xml' => [
                        'node1' => [
                            '@set' => true,
                            0 => 'Node11',
                            1 => 'Node12',
                            2 => 'Node13',
                        ],
                    ],
                ],
            ],
            [
                'data' => '<Person>
  <Contacts>
    <Contact>
      <Code>CONTACTS:TYPES:EMAIL</Code>
      <Value>khayrullin@corp.whotrades.eu</Value>
    </Contact>
    <Contact>
      <Code>CONTACTS:TYPES:PHONES:CELLULAR</Code>
      <Value>+79645751573</Value>
    </Contact>
  </Contacts>
</Person>',
                'result' => [
                    'Person' => [
                        'Contacts' => [
                            'Contact' => [
                                '@set' => true,
                                0 => [
                                    'Code' => 'CONTACTS:TYPES:EMAIL',
                                    'Value' => 'khayrullin@corp.whotrades.eu',
                                ],
                                1 => [
                                    'Code' => 'CONTACTS:TYPES:PHONES:CELLULAR',
                                    'Value' => '+79645751573',
                                ],
                            ],
                        ],
                    ],
                ],

            ],
        ];
    }


    /**
     * @param array $data
     * @param string $result
     *
     * @dataProvider providerArray2dom
     */
    public function testArray2dom(array $data, $result)
    {
        $dom = formatter\Converter::array2dom($data);

        $this->assertXmlStringEqualsXmlString(
            $result,
            $dom->saveXML($dom->documentElement)
        );
    }

    /**
     * @return array
     */
    public function providerArray2dom()
    {
        return [
            [
                'data' => [1, 2, 0, '', 'string'],
                'result' => '<_root_>
<_valid_prefix_0>1</_valid_prefix_0>
<_valid_prefix_1>2</_valid_prefix_1>
<_valid_prefix_2>0</_valid_prefix_2>
<_valid_prefix_3></_valid_prefix_3>
<_valid_prefix_4>string</_valid_prefix_4>
</_root_>',
            ],

            [
                'data' => [
                    'xml' => [
                        'node1' => [
                            'node2' => 'node2',
                        ],
                    ],
                ],
                'result' => '<xml><node1><node2>node2</node2></node1></xml>',
            ],
            [
                'data' => [
                    0,
                    1,
                    2,
                    3,
                    '',
                    'string',
                ],
                'result' => '
<_root_>
    <_valid_prefix_0>0</_valid_prefix_0>
    <_valid_prefix_1>1</_valid_prefix_1>
    <_valid_prefix_2>2</_valid_prefix_2>
    <_valid_prefix_3>3</_valid_prefix_3>
    <_valid_prefix_4></_valid_prefix_4>
    <_valid_prefix_5>string</_valid_prefix_5>
</_root_>',
            ],
            [
                'data' => [
                    'xml' => [
                        'node1 with space' => [
                            'node2 with space' => 'node2',
                        ],
                    ],
                ],
                'result' => '<xml><node1_with_space><node2_with_space>node2</node2_with_space></node1_with_space></xml>',
            ],
        ];
    }

    /**
     * @param string $data
     *
     * @dataProvider providerXml2xml
     */
    public function testXml2xml($data)
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($data);

        $this->assertXmlStringEqualsXmlString($data, formatter\Converter::array2dom(formatter\Converter::dom2array($dom))->saveXML());
    }

    /**
     * @return array
     */
    public function providerXml2xml()
    {
        return [
            [
                'data' => '<xml><node1><node2>node2</node2></node1></xml>',
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
            ],
            [
                'data' => '
<xml>
    <node1>Node11</node1>
    <node1>Node12</node1>
    <node1>Node13</node1>
</xml>',
            ],
        ];
    }

    /**
     * @param array $data
     *
     * @dataProvider providerArray2array
     */
    public function testArray2array(array $data)
    {
        $this->assertEquals($data, formatter\Converter::dom2array(formatter\Converter::array2dom($data)));
    }

    /**
     * @return array
     */
    public function providerArray2array()
    {
        return [
            [
                'data' => [],
            ],
            [
                'data' => [
                    1,
                    2,
                    3,
                    'string',
                ],
            ],
            [
                'data' => [
                    'xml' => [
                        'node1' => [
                            'node2' => 'node2',
                        ],
                    ],
                ],
            ],
            [
                'data' => [
                    0,
                    1,
                    2,
                    3,
                    '',
                    'string',
                ],
            ],
        ];
    }
}
