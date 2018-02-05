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
     * @param string | null $forceFormat
     *
     * @return Format\Base
     */
    public static function factory($data, $forceFormat = null)
    {
        $errorList = [];

        if ($forceFormat) {
            switch ($forceFormat) {
                case Format\Base::DATA_FORMAT_XML:
                    $formatObject = new Format\Xml($data, (bool) $forceFormat);
                    break;
                case Format\Base::DATA_FORMAT_JSON:
                    $formatObject = new Format\Json($data, (bool) $forceFormat);
                    break;
                case Format\Base::DATA_FORMAT_YAML:
                    $formatObject = new Format\Yaml($data, (bool) $forceFormat);
                    break;
                default:
                    $formatObject = new Format\Unsupported($data);
            }
        } else {
            switch (true) {
                case !$errorList['xml'] = ($formatObject = new Format\Xml($data))->getErrorList():
                    break;
                case !$errorList['json'] = ($formatObject = new Format\Json($data))->getErrorList():
                    break;
                case !$errorList['yaml'] = ($formatObject = new Format\Yaml($data))->getErrorList():
                    break;
                default:
                    $formatObject = new Format\Unsupported($data);
                    $formatObject->setErrorList($errorList);
            }
        }

        return $formatObject;
    }
}
