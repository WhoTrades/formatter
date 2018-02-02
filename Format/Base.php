<?php
/**
 * @package whotrades\formatter\Format
 */
namespace whotrades\formatter\Format;

use \whotrades\formatter\Converter;

abstract class Base
{
    const MAX_NESTING_LEVEL = 50;

    const DATA_FORMAT_XML = 'xml';
    const DATA_FORMAT_JSON = 'json';
    const DATA_FORMAT_UNSUPPORTED = 'unsupported';

    protected $rawData;
    protected $asArray;
    protected $formatted;
    protected $errorList;

    /**
     * @param string $data
     */
    public function __construct($data)
    {
        $this->parse($data);

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (isset($this->errorList)) {
            return false;
        }

        return true;
    }

    /**
     * @return array | null
     */
    public function getErrorList()
    {
        return $this->errorList;
    }

    /**
     * @return string | null
     */
    public function getFormatted()
    {
        return $this->formatted;
    }

    /**
     * @return array
     */
    public function getAsArray()
    {
        return (array) $this->asArray;
    }

    /**
     * @param array $xPathList
     *
     * @return array[string]
     */
    public function getValueListByXPathList(array $xPathList)
    {
        $result = [];

        $domXPath = new \DOMXPath(Converter::array2dom($this->getAsArray()));

        foreach ($xPathList as $xPathQuery) {
            foreach ($domXPath->query($xPathQuery) as $resultNode) {
                if ($resultNode->childNodes->length > 1) {
                    // ag: Convert data constructions into JSON string
                    $result[] = json_encode(Converter::dom2array($resultNode));
                } else {
                    $result[] = (string) $resultNode->nodeValue;
                }
            };
        }

        return $result;
    }

    /**
     * @param array $xPathList
     *
     * @return string | null
     */
    public function getValueFirstByXPathList(array $xPathList)
    {
        return $this->getValueListByXPathList($xPathList)[0] ?? null;
    }

    /**
     * @return string
     */
    abstract public function getFormatName();

    /**
     * @param string $data
     *
     * @return void
     */
    abstract protected function parse($data);
}
