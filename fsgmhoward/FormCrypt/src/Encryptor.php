<?php
/**
 * This file is a part of FormCrypt.
 * Published under MIT License.
 */

namespace IXNetwork\FormCrypt;

/**
 * Class Encryptor
 * @package IXNetwork\FormCrypt
 */
class Encryptor
{
    /**
     * @var Encryptor
     */
    protected static $instance;
    
    /**
     * @var string
     */
    protected $length;
    
    /**
     * @var string
     */
    protected $config;

    /**
     * Encryptor actual constructor
     *
     * @param $length
     * @param $config
     */
    protected function __construct($length, $config)
    {
        @session_start();
        $this->length = $length;
        $this->config = $config;
    }

    /**
     * Disable cloning
     */
    protected function __clone()
    {
    }

    /**
     * Construction function, enforce single construction
     *
     * @param int $length
     * @param string $config
     * @return Encryptor
     */
    public static function construct($length = 2048, $config = '')
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($length, $config);
        }
        return self::$instance;
    }

    /**
     * Generate RSA key pair
     *
     * @return string
     */
    public function generateKey()
    {
        $configArgs = [
            'private_key_bits' => $this->length,
            'private_key_type' => OPENSSL_KEYTYPE_RSA
        ];
        if (!empty($config)) {
            $configArgs['config'] = $this->config;
        }

        $pkGenerate = openssl_pkey_new($configArgs);
        openssl_pkey_export($pkGenerate, $privateKey, "Codes change the world", $configArgs);

        $pkGenerateDetails = openssl_pkey_get_details($pkGenerate);
        $publicKey = $pkGenerateDetails['key'];

        $_SESSION['FormCrypt-privateKey'] = $privateKey;

        // Remove /r when generating key by server running on Windows
        return str_replace("\r", "", $publicKey);
    }

    /**
     * Generate Javascript code block
     *
     * @param string $functionName
     * @param array $names
     * @return string
     */
    public function generateJavascript($names = ['password'], $functionName = 'encryptData')
    {
        $publicKey = str_replace("\n", "\\\n", $this->generateKey());

        $nameBlock = '';
        foreach ($names as $name) {
            $nameBlock .= <<<EOX
        $name=document.getElementById('$name');
        $name.value=RSA.encrypt($name.value, key);\n
EOX;
        }

        return <<<EOT
    function $functionName(){
        var pem = "$publicKey";
        var key = RSA.getPublicKey(pem);
$nameBlock
        submit  =document.getElementById('submit');
        submit.innerHTML="Encrypting...";
    }
EOT;
    }

    /**
     * Generate HTML Code block
     *
     * @param string $jsLibFolder
     * @param string $functionName
     * @param array $names
     * @return string
     */
    public function generateHTML($jsLibFolder = 'source/js', $names = ['password'], $functionName = 'encryptData')
    {
        $javascript = $this->generateJavascript($names, $functionName);
        return <<<EOT
<script language="JavaScript" type="text/javascript" src="$jsLibFolder/jsbn.js"></script>
<script language="JavaScript" type="text/javascript" src="$jsLibFolder/rsa.js"></script>
<script language="JavaScript" type="text/javascript" src="$jsLibFolder/jsbn2.js"></script>
<script language="JavaScript">
$javascript
</script>
EOT;
    }
}
