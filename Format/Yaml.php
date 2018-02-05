<?php
/**
 * @package whotrades\formatter\Format
 */
namespace whotrades\formatter\Format;

use Symfony\Component\Yaml\Yaml as SymfonyYaml;

class Yaml extends Base
{
    /**
     * @param string $data
     * @param bool | null $forceFormat
     */
    protected function parse($data, $forceFormat = null)
    {
        if (!$forceFormat && !preg_match('/\n/', $data, $matches)) {
            $this->errorList[] = 'For parsing one line yaml use option forceFormat';
        } else {
            try {
                $yamlDecodedResponse = SymfonyYaml::parse($data);
                $this->asArray = $yamlDecodedResponse;
            } catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
                $this->errorList[] = $e->getMessage();
            }
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
        return self::DATA_FORMAT_YAML;
    }
}
