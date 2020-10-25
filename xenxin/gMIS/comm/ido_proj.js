//- 
//- proj specified funcs
//- added by wadelau@ufqi.com, 10:34 Sunday, January 10, 2016
//-

//- fill default value
//- Xenxin@ufqi.com, 19:03 27 November 2017
//- randType = string, number, date, [sys values]
//- e.g.
//- <delayjsaction>onload::3::fillDefault('apikey','string',16);</delayjsaction>
//- 

if(typeof userinfo == 'undefined'){ userinfo = {}; } 

function fillDefault(fieldId, randType, randLen){
    var f = document.getElementById(fieldId);
    if(f){
           var oldv = f.value;
           if(oldv == ''){
                if(randType == null || randType == ''){
                    randType = 'string';    
                } 
                if(randLen == null || randLen == 0 || randLen == ''){
                    radnLen = 16
                }
                if(randType == 'string'){
                    var randVal = '';
                    var sDict = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
                    for (var i = 0; i < randLen; i++){
                        randVal += sDict.charAt(Math.floor(Math.random() * sDict.length));
                    }
                }
                f.value = randVal;
                console.log(' randVal:['+randVal+'] fillDefault succ.');
           }
           else{
               console.log('oldv not empty. fillDefault stopped.');
           } 
    }
    else{
        console.log('fieldId:['+fieldId+'] invalid. fillDefault failed.');
    }
}




