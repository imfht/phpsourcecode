<?php
//php_md5("字符串")
define("BITS_TO_A_BYTE",8);
define("BYTES_TO_A_WORD",4);
define("BITS_TO_A_WORD",32);
$m_lOnBits=array(30);
$m_l2Power=array(30);

function LShift($lValue,$iShiftBits)
{
if ($iShiftBits==0) return $lValue;
if ($iShiftBits==31)
{
if ($lValue&1) { return 0x80000000; }
else { return 0; }
}
if ($iShiftBits < 0 || $iShiftBits > 31) { }
if (($lValue&$GLOBALS[31-$iShiftBits]))
{ $tmpstr=(($lValue&$GLOBALS[31-($iShiftBits+1)])*$GLOBALS[$iShiftBits])|0x80000000; }
else
{ $tmpstr=(($lValue&$GLOBALS[31-$iShiftBits])*$GLOBALS[$iShiftBits]); }
return $tmpstr;
}

function RShift($lValue,$iShiftBits)
{
if ($iShiftBits==0)return $lValue;
if ($iShiftBits==31)
{
if ($lValue&0x80000000) { return 1; }
else { return 0; }
}
if ($iShiftBits<0 || $iShiftBits>31) { }
$tmpstr=floor(($lValue&0x7FFFFFFE)/$GLOBALS[$iShiftBits]);
if ($lValue&0x80000000) { $tmpstr=$tmpstr|floor(0x40000000/$GLOBALS[$iShiftBits-1]); }
return $tmpstr;
}

function RotateLeft($lValue,$iShiftBits)
{
return LShift($lValue,$iShiftBits)|RShift($lValue,(32-$iShiftBits));
}

function AddUnsigned($lX,$lY)
{
$lX8=$lX&0x80000000;
$lY8=$lY&0x80000000;
$lX4=$lX&0x40000000;
$lY4=$lY&0x40000000;
$lResult=($lX&0x3FFFFFFF)+($lY&0x3FFFFFFF);

if ($lX4&$lY4) { $lResult=$lResult^0x80000000^$lX8^$lY8; }
if ($lX4|$lY4)
{
if ($lResult&0x40000000)
{ $lResult=$lResult^0xC0000000^$lX8^$lY8; }
else
{ $lResult=$lResult^0x40000000^$lX8^$lY8; }
}
else
{ $lResult=$lResult^$lX8^$lY8; }
return $lResult;
}

function md5_F($x,$y,$z)
{
return ($x&$y)|((~$x)&$z);
}

function md5_G($x,$y,$z)
{
return ($x&$z)|($y&(~$z));
}

function md5_H($x,$y,$z)
{
return ($x^$y^$z);
}

function md5_I($x,$y,$z)
{
return ($y^($x|(~$z)));
}

function md5_FF(&$a,$b,$c,$d,$x,$s,$ac)
{
$a=AddUnsigned($a,AddUnsigned(AddUnsigned(md5_F($b,$c,$d),$x),$ac));
$a=RotateLeft($a,$s);
$a=AddUnsigned($a,$b);
}

function md5_GG(&$a,$b,$c,$d,$x,$s,$ac)
{
$a=AddUnsigned($a,AddUnsigned(AddUnsigned(md5_G($b,$c,$d),$x),$ac));
$a=RotateLeft($a,$s);
$a=AddUnsigned($a,$b);
}

function md5_HH(&$a,$b,$c,$d,$x,$s,$ac)
{
$a=AddUnsigned($a,AddUnsigned(AddUnsigned(md5_H($b,$c,$d),$x),$ac));
$a=RotateLeft($a,$s);
$a=AddUnsigned($a,$b);
}

function md5_II(&$a,$b,$c,$d,$x,$s,$ac)
{
$a=AddUnsigned($a,AddUnsigned(AddUnsigned(md5_I($b,$c,$d),$x),$ac));
$a=RotateLeft($a,$s);
$a=AddUnsigned($a,$b);
}

function ConvertToWordArray($sMessage)
{
$lWordArray=array();
$MODULUS_BITS=512;
$CONGRUENT_BITS=448;
$lMessageLength=strlen($sMessage);
$lNumberOfWords=(floor(($lMessageLength+floor(($MODULUS_BITS-$CONGRUENT_BITS)/BITS_TO_A_BYTE))/floor($MODULUS_BITS/BITS_TO_A_BYTE))+1)*floor($MODULUS_BITS/BITS_TO_A_WORD);
$lBytePosition=0;
$lByteCount=0;
while(!($lByteCount>=$lMessageLength))
{
$lWordCount=floor($lByteCount/BYTES_TO_A_WORD);
$lBytePosition=($lByteCount%BYTES_TO_A_WORD)*BITS_TO_A_BYTE;
$lWordArray[$lWordCount]=$lWordArray[$lWordCount]|LShift(ord(substr($sMessage,$lByteCount+1-1,1)),$lBytePosition);
$lByteCount=$lByteCount+1;
}
$lWordCount=floor($lByteCount/BYTES_TO_A_WORD);
$lBytePosition=($lByteCount%BYTES_TO_A_WORD)*BITS_TO_A_BYTE;
$lWordArray[$lWordCount]=$lWordArray[$lWordCount]|LShift(0x80,$lBytePosition);
$lWordArray[$lNumberOfWords-2]=LShift($lMessageLength,3);
$lWordArray[$lNumberOfWords-1]=RShift($lMessageLength,29);
return $lWordArray;
}

function WordToHex($lValue)
{
$tmpstr="";
for ($lCount=0; $lCount<=3; $lCount++)
{
$lByte=RShift($lValue,$lCount*BITS_TO_A_BYTE)&$GLOBALS[BITS_TO_A_BYTE-1];
$tmpstr=$tmpstr.(substr("0".dechex($lByte),strlen("0".dechex($lByte))-2));//这行可能有问题
}
return $tmpstr;
}
function php_MD5($sMessage)
{
$GLOBALS[0]=intval(1);
$GLOBALS[1]=intval(3);
$GLOBALS[2]=intval(7);
$GLOBALS[3]=intval(15);
$GLOBALS[4]=intval(31);
$GLOBALS[5]=intval(63);
$GLOBALS[6]=intval(127);
$GLOBALS[7]=intval(255);
$GLOBALS[8]=intval(511);
$GLOBALS[9]=intval(1023);
$GLOBALS[10]=intval(2047);
$GLOBALS[11]=intval(4095);
$GLOBALS[12]=intval(8191);
$GLOBALS[13]=intval(16383);
$GLOBALS[14]=intval(32767);
$GLOBALS[15]=intval(65535);
$GLOBALS[16]=intval(131071);
$GLOBALS[17]=intval(262143);
$GLOBALS[18]=intval(524287);
$GLOBALS[19]=intval(1048575);
$GLOBALS[20]=intval(2097151);
$GLOBALS[21]=intval(4194303);
$GLOBALS[22]=intval(8388607);
$GLOBALS[23]=intval(16777215);
$GLOBALS[24]=intval(33554431);
$GLOBALS[25]=intval(67108863);
$GLOBALS[26]=intval(134217727);
$GLOBALS[27]=intval(268435455);
$GLOBALS[28]=intval(536870911);
$GLOBALS[29]=intval(1073741823);
$GLOBALS[30]=intval(2147483647);

$GLOBALS[0]=intval(1);
$GLOBALS[1]=intval(2);
$GLOBALS[2]=intval(4);
$GLOBALS[3]=intval(8);
$GLOBALS[4]=intval(16);
$GLOBALS[5]=intval(32);
$GLOBALS[6]=intval(64);
$GLOBALS[7]=intval(128);
$GLOBALS[8]=intval(256);
$GLOBALS[9]=intval(512);
$GLOBALS[10]=intval(1024);
$GLOBALS[11]=intval(2048);
$GLOBALS[12]=intval(4096);
$GLOBALS[13]=intval(8192);
$GLOBALS[14]=intval(16384);
$GLOBALS[15]=intval(32768);
$GLOBALS[16]=intval(65536);
$GLOBALS[17]=intval(131072);
$GLOBALS[18]=intval(262144);
$GLOBALS[19]=intval(524288);
$GLOBALS[20]=intval(1048576);
$GLOBALS[21]=intval(2097152);
$GLOBALS[22]=intval(4194304);
$GLOBALS[23]=intval(8388608);
$GLOBALS[24]=intval(16777216);
$GLOBALS[25]=intval(33554432);
$GLOBALS[26]=intval(67108864);
$GLOBALS[27]=intval(134217728);
$GLOBALS[28]=intval(268435456);
$GLOBALS[29]=intval(536870912);
$GLOBALS[30]=intval(1073741824);

$S11=7;
$S12=12;
$S13=17;
$S14=22;
$S21=5;
$S22=9;
$S23=14;
$S24=20;
$S31=4;
$S32=11;
$S33=16;
$S34=23;
$S41=6;
$S42=10;
$S43=15;
$S44=21;

$x=ConvertToWordArray($sMessage);

$a=0x67452301;
$b=0xEFCDAB89;
$c=0x98BADCFE;
$d=0x10325476;

for ($k=0; $k<=count($x); $k=$k+16)
{
$AA=$a;
$BB=$b;
$CC=$c;
$DD=$d;
md5_FF($a,$b,$c,$d,$x[$k+0],$S11,0xD76AA478);
md5_FF($d,$a,$b,$c,$x[$k+1],$S12,0xE8C7B756);
md5_FF($c,$d,$a,$b,$x[$k+2],$S13,0x242070DB);
md5_FF($b,$c,$d,$a,$x[$k+3],$S14,0xC1BDCEEE);
md5_FF($a,$b,$c,$d,$x[$k+4],$S11,0xF57C0FAF);
md5_FF($d,$a,$b,$c,$x[$k+5],$S12,0x4787C62A);
md5_FF($c,$d,$a,$b,$x[$k+6],$S13,0xA8304613);
md5_FF($b,$c,$d,$a,$x[$k+7],$S14,0xFD469501);
md5_FF($a,$b,$c,$d,$x[$k+8],$S11,0x698098D8);
md5_FF($d,$a,$b,$c,$x[$k+9],$S12,0x8B44F7AF);
md5_FF($c,$d,$a,$b,$x[$k+10],$S13,0xFFFF5BB1);
md5_FF($b,$c,$d,$a,$x[$k+11],$S14,0x895CD7BE);
md5_FF($a,$b,$c,$d,$x[$k+12],$S11,0x6B901122);
md5_FF($d,$a,$b,$c,$x[$k+13],$S12,0xFD987193);
md5_FF($c,$d,$a,$b,$x[$k+14],$S13,0xA679438E);
md5_FF($b,$c,$d,$a,$x[$k+15],$S14,0x49B40821);

md5_GG($a,$b,$c,$d,$x[$k+1],$S21,0xF61E2562);
md5_GG($d,$a,$b,$c,$x[$k+6],$S22,0xC040B340);
md5_GG($c,$d,$a,$b,$x[$k+11],$S23,0x265E5A51);
md5_GG($b,$c,$d,$a,$x[$k+0],$S24,0xE9B6C7AA);
md5_GG($a,$b,$c,$d,$x[$k+5],$S21,0xD62F105D);
md5_GG($d,$a,$b,$c,$x[$k+10],$S22,0x2441453);
md5_GG($c,$d,$a,$b,$x[$k+15],$S23,0xD8A1E681);
md5_GG($b,$c,$d,$a,$x[$k+4],$S24,0xE7D3FBC8);
md5_GG($a,$b,$c,$d,$x[$k+9],$S21,0x21E1CDE6);
md5_GG($d,$a,$b,$c,$x[$k+14],$S22,0xC33707D6);
md5_GG($c,$d,$a,$b,$x[$k+3],$S23,0xF4D50D87);
md5_GG($b,$c,$d,$a,$x[$k+8],$S24,0x455A14ED);
md5_GG($a,$b,$c,$d,$x[$k+13],$S21,0xA9E3E905);
md5_GG($d,$a,$b,$c,$x[$k+2],$S22,0xFCEFA3F8);
md5_GG($c,$d,$a,$b,$x[$k+7],$S23,0x676F02D9);
md5_GG($b,$c,$d,$a,$x[$k+12],$S24,0x8D2A4C8A);

md5_HH($a,$b,$c,$d,$x[$k+5],$S31,0xFFFA3942);
md5_HH($d,$a,$b,$c,$x[$k+8],$S32,0x8771F681);
md5_HH($c,$d,$a,$b,$x[$k+11],$S33,0x6D9D6122);
md5_HH($b,$c,$d,$a,$x[$k+14],$S34,0xFDE5380C);
md5_HH($a,$b,$c,$d,$x[$k+1],$S31,0xA4BEEA44);
md5_HH($d,$a,$b,$c,$x[$k+4],$S32,0x4BDECFA9);
md5_HH($c,$d,$a,$b,$x[$k+7],$S33,0xF6BB4B60);
md5_HH($b,$c,$d,$a,$x[$k+10],$S34,0xBEBFBC70);
md5_HH($a,$b,$c,$d,$x[$k+13],$S31,0x289B7EC6);
md5_HH($d,$a,$b,$c,$x[$k+0],$S32,0xEAA127FA);
md5_HH($c,$d,$a,$b,$x[$k+3],$S33,0xD4EF3085);
md5_HH($b,$c,$d,$a,$x[$k+6],$S34,0x4881D05);
md5_HH($a,$b,$c,$d,$x[$k+9],$S31,0xD9D4D039);
md5_HH($d,$a,$b,$c,$x[$k+12],$S32,0xE6DB99E5);
md5_HH($c,$d,$a,$b,$x[$k+15],$S33,0x1FA27CF8);
md5_HH($b,$c,$d,$a,$x[$k+2],$S34,0xC4AC5665);

md5_II($a,$b,$c,$d,$x[$k+0],$S41,0xF4292244);
md5_II($d,$a,$b,$c,$x[$k+7],$S42,0x432AFF97);
md5_II($c,$d,$a,$b,$x[$k+14],$S43,0xAB9423A7);
md5_II($b,$c,$d,$a,$x[$k+5],$S44,0xFC93A039);
md5_II($a,$b,$c,$d,$x[$k+12],$S41,0x655B59C3);
md5_II($d,$a,$b,$c,$x[$k+3],$S42,0x8F0CCC92);
md5_II($c,$d,$a,$b,$x[$k+10],$S43,0xFFEFF47D);
md5_II($b,$c,$d,$a,$x[$k+1],$S44,0x85845DD1);
md5_II($a,$b,$c,$d,$x[$k+8],$S41,0x6FA87E4F);
md5_II($d,$a,$b,$c,$x[$k+15],$S42,0xFE2CE6E0);
md5_II($c,$d,$a,$b,$x[$k+6],$S43,0xA3014314);
md5_II($b,$c,$d,$a,$x[$k+13],$S44,0x4E0811A1);
md5_II($a,$b,$c,$d,$x[$k+4],$S41,0xF7537E82);
md5_II($d,$a,$b,$c,$x[$k+11],$S42,0xBD3AF235);
md5_II($c,$d,$a,$b,$x[$k+2],$S43,0x2AD7D2BB);
md5_II($b,$c,$d,$a,$x[$k+9],$S44,0xEB86D391);
$a=AddUnsigned($a,$AA);
$b=AddUnsigned($b,$BB);
$c=AddUnsigned($c,$CC);
$d=AddUnsigned($d,$DD);
}
return strtolower(WordToHex($a).WordToHex($b).WordToHex($c).WordToHex($d));
}

$aaa=php_MD5("sdfasdf");
echo $aaa; 


?>
