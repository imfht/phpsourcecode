var token="";
var wxjs=null;
function wxlogin(imgsrc){
    window.setInterval("checktoken()",1000);
    token=2;
    
    
}

function checktoken(){
  $.get("token.php",{"token":token},
          function(res){
            if(res["status"]==0 ){
              if(token!==res["token"]){
                  token=res["token"];
                  url=res["wxloginurl"];
                 // imgurl="http://qr.liantu.com/api.php?text="+encodeURIComponent(url);
                  imgurl=res["qrurl"];
                      $("#qrurl").attr("src",imgurl);
                  
              }
           }else if(res["status"]==1){
               location=res["url"];
               
           }
        },"json"
   );
    
};

