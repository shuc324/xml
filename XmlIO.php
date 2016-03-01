<?php

namespace Shuc324\Xml;

class XmlIO
{
    #>>读取XML文件<<#
    # @access public
    # @param $originXml XML文件路径 OR XML字符串
    # @return array
    public function read($originXml)
    {
        return $this->objectToArray(is_file($originXml) ? call_user_func_array('simplexml_load_file', array($originXml, 'SimpleXMLElement', LIBXML_NOCDATA)) : call_user_func_array('simplexml_load_string', array($originXml, 'SimpleXMLElement', LIBXML_NOCDATA)));
    }

    # 对象转化为数组 #
    # @access private
    # @param $object 对象
    # @return array
    private function objectToArray($object)
    {
        $mixed = (array)$object;
        foreach ($mixed as $name => $value) {
            $mixed[$name] = in_array(gettype($value), array('object', 'array')) ? (array)$this->objectToArray($value) : $value;
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
    public function write($savePath, $array = array(), $topTag = 'datas', $encoding = 'utf-8')
    {
        $handle = fopen($savePath, 'w+');
        $xmlContent = '<?xml version="1.0" encoding="' . $encoding . '"?>' . "\r\n" . $this->nodeContent($array, $topTag);
        fwrite($handle, $xmlContent);
        fclose($handle);
    }

    # 节点内容 #
    # @access private
    # @param $array 数组
    # @param $topTag 顶级标签
    # @param $level 层级
    # @return string
    private function nodeContent($array, $topTag, $level = 0)
    {
        $xmlNodeContent = ''; $enter = "\r\n"; $table = "\t"; $attributeStr = '';
        $tables = $level > 0 ? implode('', array_fill(0, $level, $table)) : '';
        if (array_key_exists('@attributes', $array)) {
            foreach ($array['@attributes'] as $name => $value) {
                $attributeStr .= $name . '="' . $value . '" ';
            }
            $attributeStr = ' ' . rtrim($attributeStr);
            unset($array['@attributes']);
        }
        ++$level;
        foreach ($array as $tag => $value) {
            if (gettype($value) == 'array') {
                foreach ($value as $childTag => $childValue) {
                    $xmlNodeContent .= in_array('string', array(gettype($childTag), gettype($childValue))) ? (gettype($childValue) == 'string' && gettype($childTag) != 'string') ? $tables . $table . '<' . $tag . '>' . $childValue . '</' . $tag . '>' . $enter : $this->nodeContent($value, $tag, $level) : $this->nodeContent($childValue, $tag, $level);
                }
            } else {
                $xmlNodeContent .= $tables . $table . '<' . $tag . '>' . $value . '</' . $tag . '>' . $enter;
            }
        }
        return $tables . '<' . $topTag . (!empty($attributeStr) ? $attributeStr : null) . '>' . $enter . $xmlNodeContent . $tables . '</' . $topTag . '>' . $enter;
    }
}