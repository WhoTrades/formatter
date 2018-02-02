<?php
/**
 * @package whotrades\formatter
 */
namespace whotrades\formatter;

class Converter
{
    const XML_ARRAY_DOCUMENT = '#document';
    const XML_ARRAY_ATTRIBUTES = '@attributes';
    const XML_ARRAY_VALUE = '@value';
    const XML_ARRAY_PREFIX = '@prefix';
    const XML_ARRAY_NS = '@ns';
    const XML_ARRAY_SET = '@set';

    const XML_VALID_PREFIX = '_valid_prefix_';
    const XML_VALID_ROOT_NODE = '_root_';

    const XML_ATTRIBUTE_NAME_NS = 'xmlns';

    /**
     * @param \DOMNode $rootNode
     *
     * @return array
     */
    public static function dom2array(\DOMNode $rootNode)
    {
        if ($rootNode instanceof \DOMDocument) {
            return self::dom2array($rootNode->documentElement);
        }

        /** @var \DOMElement $rootNode */

        $result = array();

        $rootNodeName = $rootNode->localName;

        if ($rootNode->hasAttributes()) {
            foreach ($rootNode->attributes as $attr) {
                if ($attr->name == self::XML_ATTRIBUTE_NAME_NS && empty($attr->value)) {
                    continue;
                }

                $prefix = $attr->prefix ?? null;
                $qualifiedAttributeName = $prefix ? $prefix . ':' . $attr->name : $attr->name;

                $result[$rootNodeName][self::XML_ARRAY_ATTRIBUTES][$qualifiedAttributeName] = $attr->value;
            }
        }

        if ($prefix = $rootNode->prefix) {
            $result[$rootNodeName][self::XML_ARRAY_PREFIX] = $prefix;
        }

        if ($namespaceUri = $rootNode->namespaceURI) {
            $result[$rootNodeName][self::XML_ARRAY_NS] = $namespaceUri;
        }

        if ($rootNode->hasChildNodes()) {
            foreach ($rootNode->childNodes as $child) {
                if ($child->nodeType == XML_TEXT_NODE || $child->nodeType == XML_CDATA_SECTION_NODE) {
                    $result[$rootNodeName][self::XML_ARRAY_VALUE] = $rootNode->nodeValue;
                } else {
                    foreach ($childArray = self::dom2array($child) as $nodeName => $nodeValue) {
                        $nodeName = str_replace(self::XML_VALID_PREFIX, '', $nodeName);
                        if ($rootNode->getElementsByTagName($nodeName)->length > 1) {
                            $result[$rootNodeName][$nodeName][self::XML_ARRAY_SET] = true;
                            $result[$rootNodeName][$nodeName][] = $nodeValue;
                        } else {
                            $result[$rootNodeName][$nodeName] = $nodeValue;
                        }
                    }
                }
            }
        } else {
            $result[$rootNodeName] = '';
        }

        if ($rootNodeName === self::XML_VALID_ROOT_NODE) {
            $result = !empty($result[$rootNodeName]) ? $result[$rootNodeName] : [];
        } elseif (is_array($result[$rootNodeName]) && count($result[$rootNodeName]) == 1 && isset($result[$rootNodeName][self::XML_ARRAY_VALUE])) {
            $result[$rootNodeName] = $result[$rootNodeName][self::XML_ARRAY_VALUE];
        }

        if (isset($result[self::XML_ARRAY_DOCUMENT])) {
            $result = $result[self::XML_ARRAY_DOCUMENT];
        }

        return $result;
    }

    /**
     * @param array $array
     * @param \DOMNode | null $parentNode
     * @param \DOMDocument | null $dom
     *
     * @return \DOMDocument
     */
    public static function array2dom(array $array, \DOMNode $parentNode = null, \DOMDocument $dom = null)
    {
        if (!isset($dom)) {
            $dom = new \DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
        }

        if (!isset($parentNode)) {
            if (count($array) != 1) {
                $array = [self::XML_VALID_ROOT_NODE => $array];
            }
            $parentNode = $dom;
        }

        foreach ($array as $nodeName => $nodeValue) {
            $validNodeName = htmlentities($nodeName, ENT_IGNORE | ENT_XML1);
            $validNodeName = str_replace(' ', '_', $validNodeName);
            if (!preg_match('/^[\?\_A-Za-z]/', $validNodeName)) {
                $validNodeName = self::XML_VALID_PREFIX . $validNodeName;
            }

            if ($nodeName === self::XML_ARRAY_ATTRIBUTES && $parentNode instanceof \DOMElement) {
                foreach ($nodeValue as $attributeName => $attributeValue) {
                    $parentNode->setAttribute($attributeName, $attributeValue);
                }
            } elseif ($nodeName === self::XML_ARRAY_NS) {
                // ag: Skip this node because it's processed already
            } elseif ($nodeName === self::XML_ARRAY_PREFIX) {
                // ag: Skip this node because it's processed already
            } elseif ($nodeName === self::XML_ARRAY_VALUE) {
                $currentNode = $dom->createTextNode($nodeValue);
                $parentNode->appendChild($currentNode);
            } elseif (is_array($nodeValue)) {
                if (isset($nodeValue[self::XML_ARRAY_SET])) {
                    foreach ($nodeValue as $key => $setItem) {
                        if ($key === self::XML_ARRAY_SET) {
                            continue;
                        }
                        self::array2dom([$validNodeName => $setItem], $parentNode, $dom);
                    }
                } else {
                    $prefix = $nodeValue[self::XML_ARRAY_PREFIX] ?? null;
                    $qualifiedNodeName = $prefix ? $prefix . ':' . $validNodeName : $validNodeName;
                    $ns = $nodeValue[self::XML_ARRAY_NS] ?? null;

                    if ($ns && $prefix) {
                        $currentNode = $dom->createElementNS($ns, $qualifiedNodeName);
                    } else {
                        $currentNode = $dom->createElement($qualifiedNodeName);
                        if ($ns) {
                            $attributeNs = $dom->createAttribute(self::XML_ATTRIBUTE_NAME_NS);
                            $attributeNs->value = $ns;
                            $currentNode->appendChild($attributeNs);
                        }
                    }
                    self::array2dom($nodeValue, $currentNode, $dom);
                    $parentNode->appendChild($currentNode);
                }
            } else {
                $currentNode = $dom->createElement($validNodeName, $nodeValue);
                $parentNode->appendChild($currentNode);
            }
        }

        return $dom;
    }
}
