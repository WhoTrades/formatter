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
     * @param bool | null $forceFormat
     */
    protected function parse($data, $forceFormat = null)
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
