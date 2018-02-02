<?php
/**
 * @package whotrades\formatter
 */
namespace whotrades\formatter;

use whotrades\formatter\Format;

class Formatter
{
    /**
     * @param string $data
     *
     * @return Format\Base
     */
    public static function factory($data)
    {
        $errorList = [];
        switch (true) {
            case !$errorList['xml'] = ($formatObject = new Format\Xml($data))->getErrorList():
                break;
            case !$errorList['json'] = ($formatObject = new Format\Json($data))->getErrorList():
                break;
            default:
                $formatObject = new Format\Unsupported($data);
                $formatObject->setErrorList($errorList);
        }

        return $formatObject;
    }
}
