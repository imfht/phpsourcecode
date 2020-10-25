<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
session_start();
include 'Root_Hackdone_Action.php';
function VI($content) { global $Mark_Config_Action; ?>
<script type="text/javascript" src="<?php echo $Mark_Config_Action['level']; ?>/Public/xheditor/jquery/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="<?php echo $Mark_Config_Action['level']; ?>/Public/xheditor/xheditor-1.2.1.min.js"></script>
<script type="text/javascript" src="<?php echo $Mark_Config_Action['level']; ?>/Public/xheditor/xheditor_lang/zh-cn.js"></script>
<script type="text/javascript">  
      $(function(){  
       var plugins={  
        Code:{c:'btnCode',t:'插入代码',h:1,e:function(){  
            var _this=this;  
            var htmlCode="<div>编程语言<select id='xheCodeType'>";  
                htmlCode+="<option value='html'>HTML/XML</option>";  
                htmlCode+="<option value='js'>Javascript</option>";  
                htmlCode+="<option value='css'>CSS</option>";  
                htmlCode+="<option value='php'>PHP</option>";  
                htmlCode+="<option value='mysql'>MySql</option>";  
                htmlCode+="<option value='java'>Java</option>";  
                htmlCode+="<option value='py'>Python</option>";  
                htmlCode+="<option value='pl'>Perl</option>";  
                htmlCode+="<option value='rb'>Ruby</option>";  
                htmlCode+="<option value='cs'>C#</option>";  
                htmlCode+="<option value='c'>C++/C</option>";  
                htmlCode+="<option value='vb'>VB/ASP</option>";  
                htmlCode+="<option value=''>其它<d/option>";  
                htmlCode+="</select></div><div>";  
                htmlCode+="<textarea id='xheCodeValue' wrap='soft' spellcheck='false' style='width:300px;height:100px;' />";  
                htmlCode+="</div><div style='text-align:right;'><input type='button' id='xheSave' value='确定' /></div>";           
            var jCode=$(htmlCode),jType=$('#xheCodeType',jCode),jValue=$('#xheCodeValue',jCode),jSave=$('#xheSave',jCode);  
            jSave.click(function(){  
                _this.loadBookmark();  
                _this.pasteHTML('<pre><code>'+_this.domEncode(jValue.val())+'</code></pre>');  
                _this.hidePanel();  
                return false;     
            });  
            _this.saveBookmark();  
            _this.showDialog(jCode);  
        }}, 
          }; emots={
        msn:{name:'MSN',count:40,width:22,height:22,line:8},
        pidgin:{name:'Pidgin',width:22,height:25,line:8,list:{smile:'微笑',cute:'可爱',wink:'眨眼',laugh:'大笑',victory:'胜利',sad:'伤心',cry:'哭泣',angry:'生气',shout:'大骂',curse:'诅咒',devil:'魔鬼',blush:'害羞',tongue:'吐舌头',envy:'羡慕',cool:'耍酷',kiss:'吻',shocked:'惊讶',sweat:'汗',sick:'生病',bye:'再见',tired:'累',sleepy:'睡了',question:'疑问',rose:'玫瑰',gift:'礼物',coffee:'咖啡',music:'音乐',soccer:'足球',good:'赞同',bad:'反对',love:'心',brokenheart:'伤心'}},
        ipb:{name:'IPB',width:20,height:25,line:8,list:{smile:'微笑',joyful:'开心',laugh:'笑',biglaugh:'大笑',w00t:'欢呼',wub:'欢喜',depres:'沮丧',sad:'悲伤',cry:'哭泣',angry:'生气',devil:'魔鬼',blush:'脸红',kiss:'吻',surprised:'惊讶',wondering:'疑惑',unsure:'不确定',tongue:'吐舌头',cool:'耍酷',blink:'眨眼',whistling:'吹口哨',glare:'轻视',pinch:'捏',sideways:'侧身',sleep:'睡了',sick:'生病',ninja:'忍者',bandit:'强盗',police:'警察',angel:'天使',magician:'魔法师',alien:'外星人',heart:'心动'}}
    }; 
        $('#mark').xheditor({skin:'nostyle',plugins:plugins,emots:emots,upLinkUrl:"Upload_System.php",upLinkExt:"zip,rar,txt",upImgUrl:"Upload_System.php",upImgExt:"jpg,jpeg,gif,png",upFlashUrl:"Upload_System.php",upFlashExt:"swf",upMediaUrl:"Upload_System.php",upMediaExt:"wmv,avi,wma,mp3,mid",
            loadCSS:'<style>pre{margin-left:2em;border-left:3px solid #CCC;padding:0 1em;}</style>',  
        });  
    })  
</script>  
<textarea id="mark" name="content" rows="18" cols="80" style="width: 100%">
<?php echo htmlspecialchars($content); ?>
</textarea>
<?php } ?>