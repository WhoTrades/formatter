<?php
/**
 * @package whotrades\formatter\Format
 */
namespace whotrades\formatter\Format;

class Json extends Base
{
    /**
     * @param string $data
     * @param bool | null $forceFormat
     */
    protected function parse($data, $forceFormat = null)
    {
        $jsonDecodedResponse = json_decode($data, true);
        if ($jsonDecodedResponse !== null) {
            $this->asArray = $jsonDecodedResponse;
        } else {
            $this->errorList[] = json_last_error_msg();
        }
    }

    /**
     * @return string
     */
    public function getFormatted()
    {
        if ($this->formatted === null) {
            $this->formatted = print_r($this->asArray, true);
        }

        return parent::getFormatted();
    }

    /**
     * @return string
     */
    public function getFormatName()
    {
        return self::DATA_FORMAT_JSON;
    }
}
