# Form Crypt
## Encrypt your HTML form data using unbreakable RSA

## How to install
Install via composer: execute the following commands in your project's folder!
```
$ curl -o composer.phar https://getcomposer.org/composer.phar
$ #Skip the above step if you have already installed composer
$ php composer.phar require ix-network/form-crypt
```

Then require() the class autoloader in your app
```
require "vendor/autoload.php";
```

At last copy the js files in the folder js to your project's frontend js folder. The default folder is `source/js`.

## How to use
```
use IXNetwork/FormCrypt/Encryptor;
use IXNetwork/FormCrypt/Decryptor;

# Init encryptor
$encryptor = Encryptor::construct($keyLength = 2048, $openSSLConfigFile = '');

# Generate key pair. Private key will be automatically stored in $_SESSION['FormCrypt-privateKey']
$publicKey = $encryptor->generateKey();

# Generate Javascript code block, including only the encryptor function
$javascript = $encryptor->generateJavascript($inputFieldNames = ['password'], $functionName = 'encryptData');

# Generate a HTML code block which can be directly insert into the `header` section of your HTML output
$html = $encryptor->generateHTML($javascriptLibraryFolder = 'source/js', $inputFieldNames = ['password'], $functionName = 'encryptData');

# Init decryptor: presence of $_SESSION['FormCrypt-privateKey'] is required for init
$decryptor = Decryptor::construct();

# Decrypt encrypted form data
$decryptedData = $decryptor->decrypt($EncryptedData);
```

## Open Source License
JS BN Library - MIT Licensed. Copyright (c) 2003-2009 Tom Wu

JS RSA Library - MIT Licensed. Copyright 2013 Ziyan Zhou <zhou@ziyan.info>

PHP Library - MIT Licensed. Copyright 2016 Howard Liu <howard@ixnet.work>

Distributed by Packagist network.