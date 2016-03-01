<?php

namespace Shuc324\Xml;

class XmlIO
{
    const ZERO = 0;

    const START = 1;

    const TABLE = "\t";

    const ENTER = "\r\n";

    const EQUAL_STR = "=";

    const EMPTY_STR = '';

    const SPACE_STR = ' ';

    const ENCODING = 'utf-8';

    const TYPE_ARRAY = 'array';

    const TYPE_STRING = 'string';

    const TYPE_OBJECT = 'object';

    const END_LEFT_LABEL = '</';

    const END_RIGHT_LABEL = '>';

    const START_LEFT_LABEL = '<';

    const START_RIGHT_LABEL = '>';

    #>>读取XML文件<<#
    # @access public
    # @param $originXml XML文件路径 OR XML字符串
    # @return array
    public function read($originXml)
    {
        $simpleXmlObject = is_file($originXml) ? call_user_func_array('simplexml_load_file', [$originXml, 'SimpleXMLElement', LIBXML_NOCDATA]) : call_user_func_array('simplexml_load_string', [$originXml, 'SimpleXMLElement', LIBXML_NOCDATA]);
        return [$simpleXmlObject->getName() => [self::ZERO => $this->objectToArray($simpleXmlObject)]];
    }

    # 对象转化为数组 #
    # @access private
    # @param $object 对象
    # @return array
    private function objectToArray($object)
    {
        $mixed = (array)$object;
        foreach ($mixed as $name => $value) {
            $mixed[$name] = in_array(gettype($value), [self::TYPE_OBJECT, self::TYPE_ARRAY]) ? (array)$this->objectToArray($value) : $value;
        }
        return $mixed;
    }

    #>>写XML文件<<#
    # @access public
    # @param $savePath XML保存路径
    # @param $array 数组
    # @param $topTag 顶级标签
    # @param $encoding 编码
    # @return null
    public function write($savePath, $array = array(), $topTag = self::EMPTY_STR, $encoding = self::ENCODING)
    {
        $handle = fopen($savePath, 'w+');
        $xmlContent = '<?xml version="1.0" encoding="' . $encoding . '"?>' . self::ENTER . $this->nodeContent($array, $topTag);
        fwrite($handle, $xmlContent);
        fclose($handle);
    }

    # 节点内容 #
    # @access private
    # @param $array 数组
    # @param $topTag 顶级标签
    # @param $level 层级
    # @return string
    private function nodeContent($array, $topTag, $level = self::ZERO)
    {
        $xmlNodeContent = self::EMPTY_STR; $enter = self::ENTER; $table = self::TABLE; $attributeStr = self::EMPTY_STR;
        $tables = $level > 0 ? implode(self::EMPTY_STR, array_fill(self::ZERO, $level, $table)) : self::EMPTY_STR;
        if (array_key_exists('@attributes', $array)) {
            foreach ($array['@attributes'] as $name => $value) {
                $attributeStr .= $name . self::EQUAL_STR . '"' . $value . '"' . self::SPACE_STR;
            }
            $attributeStr = self::SPACE_STR . rtrim($attributeStr);
            unset($array['@attributes']);
        }
        ++$level;
        foreach ($array as $tag => $value) {
            if (gettype($value) == self::TYPE_ARRAY) {
                $level == self::START && empty($topTag) && --$level;
                foreach ($value as $childTag => $childValue) {
                    $xmlNodeContent .= in_array(self::TYPE_STRING, array(gettype($childTag), gettype($childValue))) ? $this->nodeContent($value, $tag, $level) : $this->nodeContent($childValue, $tag, $level);
                }
            } else {
                $xmlNodeContent .= $tables . $table . self::START_LEFT_LABEL . $tag . self::START_RIGHT_LABEL . $value . self::END_LEFT_LABEL . $tag . self::END_RIGHT_LABEL . $enter;
            }
        }
        return empty($topTag) ? $xmlNodeContent : $tables . self::START_LEFT_LABEL . $topTag . (!empty($attributeStr) ? $attributeStr : null) . self::START_RIGHT_LABEL . $enter . $xmlNodeContent . $tables . self::END_LEFT_LABEL . $topTag . self::END_RIGHT_LABEL . $enter;
    }
}