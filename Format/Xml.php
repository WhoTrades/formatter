<?php
/**
 * @package whotrades\formatter\Format
 */
namespace whotrades\formatter\Format;

use \whotrades\formatter\Converter;

class Xml extends Base
{
    /**
     * @var \DOMDocument
     */
    protected $domDocument;

    /**
     * @param string $data
     * @param bool | null $forceFormat
     */
    protected function parse($data, $forceFormat = null)
    {
        $this->rawData = $data;

        $data = (string) $data;

        if ($data === '') {
            $this->errorList[] = 'Empty string supplied as input';

            return;
        }

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $libXmlInternalErrors = libxml_use_internal_errors(true);
        if ($dom->loadXML($data)) {
            $this->domDocument = $dom;
        } else {
            $this->errorList = libxml_get_errors();
        }
        libxml_use_internal_errors($libXmlInternalErrors);
    }

    /**
     * @return string
     */
    public function getAsArray()
    {
        if ($this->asArray === null) {
            $this->asArray = Converter::dom2array($this->domDocument);
        }

        return parent::getAsArray();
    }

    /**
     * @return string
     */
    public function getFormatted()
    {
        if ($this->formatted === null) {
            $this->formatted = $this->domDocument->saveXML($this->domDocument->documentElement);
        }

        return parent::getFormatted();
    }

    /**
     * @return string
     */
    public function getFormatName()
    {
        return self::DATA_FORMAT_XML;
    }
}
