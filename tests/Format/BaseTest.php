<?php

namespace tests\Format;

use \whotrades\formatter\Format;

class BaseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $data
     * @param array $keyList
     * @param array $result
     *
     * @dataProvider providerGeneric
     */
    public function testGetValueListByKey($data, $keyList, array $result)
    {
        $baseMock = $this->getBaseMock();

        $reflection = new \ReflectionClass($baseMock);
        $reflectionProperty = $reflection->getProperty('asArray');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($baseMock, $data);

        $this->assertEquals($result, $baseMock->getValueListByXPathList($keyList));
        $this->assertEquals($result[0], $baseMock->getValueFirstByXPathList($keyList));
    }

    /**
     * @return array
     */
    public function providerGeneric()
    {
        return [
            [
                'data' => [
                    'key1' => 'value11',
                    'key2' => 'value12',
                    'next_level' => [
                        'key1' => 'value21',
                        'key2' => 'value22',
                        'next_level' => [
                            'key1' => 'value31',
                            'key2' => 'value32',
                        ],
                    ],
                ],
                'key_list' => ['//key1'],
                'result' => [
                    'value11',
                    'value21',
                    'value31',
                ],
            ],
            [
                'data' => [
                    'next_level' => [
                        'next_level' => [
                            'key1' => 'value31',
                            'key2' => 'value32',
                        ],
                        'key1' => 'value21',
                        'key2' => 'value22',
                    ],
                    'key1' => 'value11',
                    'key2' => 'value12',
                ],
                'key_list' => ['//key1'],
                'result' => [
                    'value31',
                    'value21',
                    'value11',
                ],
            ],
            [
                'data' => [
                    'key1' => 'value11',
                    'key2' => 'value12',
                    'next_level' => [
                        'key1' => 'value21',
                        'key2' => 'value22',
                        'next_level' => [
                            'key1' => 'value31',
                            'key2' => 'value32',
                        ],
                    ],
                ],
                'key_list' => ['//key1', '//key2'],
                'result' => [
                    'value11',
                    'value21',
                    'value31',
                    'value12',
                    'value22',
                    'value32',
                ],
            ],
            [
                'data' => [
                    'method' => 'getInterestsSystem.getApi.bestTrades',
                    'params' => [],
                ],
                'key_list' => ['//method'],
                'result' => [
                    'getInterestsSystem.getApi.bestTrades',
                ],
            ],
            [
                'data' => [
                    'method' => 'getInterestsSystem.getApi.bestTrades',
                    'params' => [
                        'param1' => 'param1',
                        'param2' => 'param2',
                    ],
                ],
                'key_list' => ['//params'],
                'result' => [
                    '{"params":{"param1":"param1","param2":"param2"}}',
                ],
            ],
            [
                'data' => [
                    'method' => 'getPersonsSystem.getModelExternalForm.registrationConsultingUsa',
                    'params' => [
                        '@set' => true,
                        0 => [
                            'name' => 'TestFromDev1',
                            'lastname' => 'mail1',
                        ],
                        1 => [
                            'name' => 'TestFromDev2',
                            'lastname' => 'mail2',
                        ],
                    ],
                ],
                'key_list' => ['//params'],
                'result' => [
                    '{"params":{"name":"TestFromDev1","lastname":"mail1"}}',
                    '{"params":{"name":"TestFromDev2","lastname":"mail2"}}',
                ],
            ],
            [
                'data' => [
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
                'key_list' => ['//Guid', '//PersonGUID', '//NewGuid'],
                'result' => [
                    '6F211848-4CCC-4BE7-88B9-82A7DE661186',
                ],
            ],
        ];
    }

    /**
     * @return Format\Base
     */
    protected function getBaseMock()
    {
        return $this->getMockBuilder(Format\Base::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass();
    }
}
