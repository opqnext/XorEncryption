<?php
/**
 * XorEncryption(异或加密)
 * @author: opqnext
 * @website: https://www.opqnext.com
 * @date: 2017-08-18
 */

namespace XorEncryption;

class XorEncryption {

    public $sourceID = "XOR";
    public $secret = "yJadUPf2";
    private $disturbLen = 6;
    private $key;
    private $lkey;
    private $rkey;
    private $ckey;

    /**
     * @param string $key 16位md5加密
     */
    public function __construct($key = '')
    {
        if (strlen($key) == 16 && preg_match("/^[a-z]/i", $key)) {  //16位md5加密过的字符串首字符如果是字母
            $this->key = sha1($key . $this->sourceID);
        } elseif (strlen($key) == 16 && preg_match("/^[0-9]/i", $key)) {  //16位md5加密过的字符串首字符如果是数字
            $this->key = sha1($key . $this->secret);
        } else {
            $this->key = sha1(md5($this->sourceID . $this->secret));
        }
        $this->lkey = sha1(substr($this->key, 0, 20));
        $this->rkey = sha1(substr($this->key, 20, 20));
    }

    public function encode($string, $expire = 0)
    {
        $res = $this->algorithm($string, "ENCODE", $expire);
        return $this->ckey . rtrim(strtr(base64_encode($res), '+/', '-_'), '=');

    }

    public function decode($string)
    {
        $res = $this->algorithm($string, "DECODE");
        if ((substr($res, 0, 10) == 0 || substr($res, 0, 10) - time() > 0) && substr($res, 10, 20) == substr(sha1(substr($res, 30) . $this->rkey), 0, 20)) {
            return substr($res, 30);
        } else {
            return '';
        }

    }

    private function algorithm($string, $action, $expire=0)
    {
        $this->ckey = $action == 'DECODE' ? substr($string, 0, $this->disturbLen) : substr(md5(microtime()), -$this->disturbLen);
        $keyStore = $this->lkey . sha1($this->lkey . $this->ckey);
        $store_length = strlen($keyStore);

        if ($action == "ENCODE") {
            $string = sprintf('%010d', $expire ? $expire + time() : 0) . substr(sha1($string . $this->rkey), 0, 20) . $string;
        } else {
            $string = base64_decode(strtr(substr($string, $this->disturbLen), '-_', '+/'));
        }

        $stringLen = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($keyStore[$i % $store_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $stringLen; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        return $result;
    }
} 