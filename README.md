# XorEncryption(异或加密)

> XorEncryption(异或加密)

- 使用方法(1)

使用默认 key [opqnext] 进行加密解密。 

```
require 'vendor/autoload.php';

// 实例化类
$xor = new XorEncryption\XorEncryption();
// 要加密的字符串
$str = "opqnext";
// XOR加密
$secret = $xor->encode($str);
echo $secret."\n";
// XOR解密
$str = $xor->decode($secret);
echo $str."\n";
```

- 使用方法(2)

使用自定义 key 进行加密解密。

```
require 'vendor/autoload.php';

$key = "2fc2b4aaa8d087af4d6dc085f3316c0a";
// 实例化类
$xor = new XorEncryption\XorEncryption($key);
// 要加密的字符串
$str = "opqnext";
// XOR加密
$secret = $xor->encode($str);
echo $secret."\n";
// XOR解密
$str = $xor->decode($secret);
echo $str."\n";
```

