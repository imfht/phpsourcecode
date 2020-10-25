<?php
/**
*     关键字 替换类  替换(随机位置) 且指定数量的关键词
      先对关键字 md5加密(若关键字之间有重字部分，md5加密，可防止重复替换)，替换标志/&--&/
/*    替换成/*md5*/
/*    最后用 关键词 替换掉 **形式

$KeyArray=Array(
    0=>array("Key"=>"this","Href"=>"<B>this</B>","ReplaceNumber"=>1),
    1=>array("Key"=>"test","Href"=>"<a href='test'>test</a>","ReplaceNumber"=>1)
);
$str = "this is test content!";
$a = new Seokey($KeyArray,$str);
$a->KeyOrderBy();
$a->Replaces();
echo $a->HtmlString;

*/
class Seokey{
    public $KeyArray;  //关键字
    public $HtmlString; //文字内容
    public $ArrayCount; //关键字的个数
    public $Key;
    public $Href;

    /*
        初始化：
        $keyArray 关键字 数组
        $String   检索字域，文字
    */
    function Seokey($KeyArray,$String,$Key='Key',$Href='Href'){
       $this->KeyArray=$KeyArray;
       $this->HtmlString=$String;
       $this->ArrayCount=count($KeyArray);
       $this->Key=$Key;
       $this->Href=$Href;
    }

    /*
        关键字 按长度排序
    */
    function KeyOrderBy(){
        usort($this->KeyArray,'sortcmp');
    }

    function Replaces(){
    		
        for($i=0;$i<$this->ArrayCount;$i++){

            if((integer)$this->KeyArray[$i]['ReplaceNumber'] != 0 ){
                str_replace($this->KeyArray[$i][$this->Key],"/*".md5($this->KeyArray[$i][$this->Key])."*/",$this->HtmlString,$num);//$num查询到的数量

                if((integer)$this->KeyArray[$i]['ReplaceNumber']>$num) {//当关键词 需要替换的数量 大于 包含的数量时，替换全部
                    $this->KeyArray[$i]['ReplaceNumber']=$num;
                    $this->HtmlString=str_replace($this->KeyArray[$i][$this->Key],"/*".md5($this->KeyArray[$i][$this->Key])."*/",$this->HtmlString);
                    continue;
                }
                //当关键词 需要替换的数量 不大于 包含的数量时，使用 KeyStrpos($i);方法替换
                $ListNumber=array();
                $ListNumber=$this->KeyStrpos($i);//$i: 表示第$i个关键词($i从0开始)
                $RegArray=array();

                if(count($ListNumber)<1) continue;//不存在 关键词

                $n=0;
                while($n<(integer)$this->KeyArray[$i]["ReplaceNumber"]){
                    $g=0;
                    $x=rand(0,count($ListNumber)-1);//随机数
                    for($xcn=0;$xcn<=$n;$xcn++){
                        if($RegArray[$xcn]==$ListNumber[$x]){
                            $g=1;
                        }
                    }
                    if($g==0){
                        $RegArray[$n]=$ListNumber[$x];
                        $n++;
                    }
                }

                for($c=0;$c<count($RegArray)-1;$c++)
                {//关键词所在位置 递增排序
                    for($jx=$c+1;$jx<count($RegArray);$jx++){
                        if($RegArray[$c]>$RegArray[$jx]){
                            $TempArray=$RegArray[$c];
                            $RegArray[$c]=$RegArray[$jx];
                            $RegArray[$jx]=$TempArray;
                        }
                    }
                }

                for($c=0;$c<count($RegArray);$c++){
                    $this->StrposKey($this->KeyArray[$i][$this->Key],$RegArray[$c],$c);// 逐位(索引位) 替换截取到的关键字
                }

               $this->HtmlString=str_replace("/&".md5($this->KeyArray[$i][$this->Key])."&/",$this->KeyArray[$i][$this->Key],$this->HtmlString);
            }else{
               $this->HtmlString=str_replace($this->KeyArray[$i][$this->Key],"/*".md5($this->KeyArray[$i][$this->Key])."*/",$this->HtmlString);
            }
       }

       for($i=0;$i<$this->ArrayCount;$i++){
           $this->HtmlString=str_replace("/*".md5($this->KeyArray[$i][$this->Key])."*/",$this->KeyArray[$i][$this->Href],$this->HtmlString);
       }

    }

    function StrposKey($Key,$StrNumber,$n){//在字符串里 截取关键字 并替换，从$StrNumber这个位置开始(包含$StrNumber这个位置)替换到$n(包含$n这个位置)这个位置
       $this->HtmlString=substr_replace($this->HtmlString, "/*".md5($Key)."*/", $StrNumber, 36);
    }

    /* 递归 查找 关键词 所在的位置 存于数组中 */
    function KeyStrpos($KeyId){
        $StrListArray=array();
        $StrNumberss=strpos($this->HtmlString, $this->KeyArray[$KeyId][$this->Key]);
        $xf=0;
        while(!($StrNumberss===false)){
            $StrListArray[$xf]=$StrNumberss;
            $this->HtmlString=substr_replace($this->HtmlString,"/&".md5($this->KeyArray[$KeyId][$this->Key])."&/",$StrNumberss, strlen($this->KeyArray[$KeyId][$this->Key]));
            $StrNumberss=strpos($this->HtmlString, $this->KeyArray[$KeyId][$this->Key]);
            $xf++;
        }
        return $StrListArray;
    }
   
}
?>
