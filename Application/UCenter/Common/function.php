<?php
/**
 * Created by PhpStorm.
 * User: d8q8
 * Date: 2016/11/21
 * Time: 14:45
 */
//====== UC公用函数 拷贝uc_client目录中client.php文件中的uc_authcode函数 ===============================================
/**
 * UC加密与解密
 * @param string $string 提供需要加密的字符串
 * @param string $operation 加密方式，ENCODE是加密，DECODE是解密
 * @param string $key 密钥，在整合程序时填写的密钥
 * @param int $expiry
 * @return string
 */
function _uc_authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;

    $key = md5($key ? $key : UC_KEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}

/**
 * 清理反斜框
 * @param $string
 * @return array|string
 */
function _uc_stripslashes($string) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = _uc_stripslashes($val);
        }
    } else {
        $string = stripslashes($string);
    }

    return $string;
}
//======uc_client目录中lib中的xml.class.php文件========================================================================
/**
 * xml反序列化
 * @param $xml
 * @param bool $isnormal
 * @return array|string
 */
function xml_unserialize(&$xml, $isnormal = false) {
    $xml_parser = new XML($isnormal);
    $data       = $xml_parser->parse($xml);
    $xml_parser->destruct();

    return $data;
}

/**
 * xml序列化
 * @param $arr
 * @param bool $htmlon
 * @param bool $isnormal
 * @param int $level
 * @return mixed|string
 */
function xml_serialize($arr, $htmlon = false, $isnormal = false, $level = 1) {
    $s     = $level == 1 ? "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n<root>\r\n" : '';
    $space = str_repeat("\t", $level);
    foreach ($arr as $k => $v) {
        if (!is_array($v)) {
            $s .= $space . "<item id=\"$k\">" . ($htmlon ? '<![CDATA[' : '') . $v . ($htmlon ? ']]>' : '') . "</item>\r\n";
        } else {
            $s .= $space . "<item id=\"$k\">\r\n" . xml_serialize($v, $htmlon, $isnormal, $level + 1) . $space . "</item>\r\n";
        }
    }
    $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);

    return $level == 1 ? $s . "</root>" : $s;
}

/**
 * xml处理类
 * Class XML
 */
class XML {

    var $parser; //XML解析器
    var $document; //dom文档
    var $stack; //堆栈数组
    var $data; //数据
    var $last_opened_tag;//最后打开标签
    var $isnormal;//是否普通
    var $attrs = array();//属性
    var $failed = false;//失败

    /**
     * XML constructor.
     * @param $isnormal
     */
    function __construct($isnormal) {
        $this->XML($isnormal);
    }

    /**
     * 初始化
     * @param $isnormal
     */
    function XML($isnormal) {
        $this->isnormal = $isnormal;
        $this->parser   = xml_parser_create('ISO-8859-1');
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, 'open', 'close');
        xml_set_character_data_handler($this->parser, 'data');
    }

    /**
     * 释放指定的 XML 解析器
     */
    function destruct() {
        xml_parser_free($this->parser);
    }

    /**
     * 开始解析一个 XML 文档
     * @param $data
     * @return array|string
     */
    function parse(&$data) {
        $this->document = array();
        $this->stack    = array();

        return xml_parse($this->parser, $data, true) && !$this->failed ? $this->document : '';
    }

    /**
     * 打开解析 XML 文档
     * @param $parser
     * @param $tag
     * @param $attributes
     */
    function open(&$parser, $tag, $attributes) {
        $this->data   = '';
        $this->failed = false;
        if (!$this->isnormal) {
            if (isset($attributes['id']) && !is_string($this->document[$attributes['id']])) {
                $this->document = &$this->document[$attributes['id']];
            } else {
                $this->failed = true;
            }
        } else {
            if (!isset($this->document[$tag]) || !is_string($this->document[$tag])) {
                $this->document = &$this->document[$tag];
            } else {
                $this->failed = true;
            }
        }
        $this->stack[]         = &$this->document;
        $this->last_opened_tag = $tag;
        $this->attrs           = $attributes;
    }

    /**
     * 获取解析 XML 数据
     * @param $parser
     * @param $data
     */
    function data(&$parser, $data) {
        if ($this->last_opened_tag != null) {
            $this->data .= $data;
        }
    }

    /**
     * 关闭
     * @param $parser
     * @param $tag
     */
    function close(&$parser, $tag) {
        if ($this->last_opened_tag == $tag) {
            $this->document        = $this->data;
            $this->last_opened_tag = null;
        }
        array_pop($this->stack);
        if ($this->stack) {
            $this->document = &$this->stack[count($this->stack) - 1];
        }
    }

}