function display(url, obj){
    var aj=Ajax();
    if(ieff(obj) == '正常'){   
        aj.get(url);
        ieff(obj, '锁定');
        obj.style.color="red";
    }else{
        aj.get(url);
        ieff(obj, '正常');
        obj.style.color="green";
    }
}

function ieff(obj, value){
    if(document.all){
        if(typeof(value)=="undefined")
            return obj.innerText;
        else
            obj.innerText=value;
    }else{
        if(typeof(value)=="undefined")  
            return obj.textContent;
        else
            obj.textContent=value;
    }
}

function openwin(url, name, width, height){
    var left=(screen.width - width) / 2;
    var top =(screen.height - height) / 2;
    window.open(url, name, 'width='+width+', height='+height+', top='+top+', left='+left);
}

function select(){
    var checkall=document.getElementsByName("id[]");
    for(i=0; i<checkall.length; i++){
        var e=checkall[i];
        e.checked=!e.checked;
    }
}
      
function show(num) {
var h3s=document.getElementById("jh").getElementsByTagName("li");
var jhcs=document.getElementById("jhc").getElementsByTagName("div");

    for(var i=0; i<h3s.length; i++){
        if(num==i){
            h3s[num].className="cview";
            jhcs[num].style.display="block";
        }else{
            h3s[i].className="";
            jhcs[i].style.display="none";
        }
    }
}





