<?php
/**
 * @package whotrades\formatter\Format
 */
namespace whotrades\formatter\Format;

class Unsupported extends Base
{
    /**
     * @param array $errorList
     */
    public function setErrorList(array $errorList)
    {
        $this->errorList = $errorList;
    }

    /**
     * @param string $data
     */
    protected function parse($data)
    {
    }

    /**
     * @return string
     */
    public function getFormatName()
    {
        return self::DATA_FORMAT_UNSUPPORTED;
    }
}
