$(document).ready(function () {
    clearAll();
    $(".date").addClass("dumpup");
    $(".yagu").addClass("divLeft");
    $(".jialulu").addClass("divRight");
    $(".Lightning").addClass("lightin");
    $(".Logo").addClass("LogoIn");
    t = setTimeout(function () {
        $(".movenone").addClass("move");
    }, 3500);

    var media = $(".globalAudio").find("audio")[0];
  
    media.play();
    audioPaused = true;
    $(".icon-music").addClass("play");
});

$("#pageOne").bind("swiperup", function (event) {

   


    $("#mask1").hide();
    $("#title1").addClass("title_img1");
    setTimeout(function () { $("#title2").addClass("title_img2"); }, 200);
    setTimeout(function () { $("#title3").addClass("title_img3"); }, 400);
    setTimeout(function () { $("#title4").addClass("title_img4"); }, 600);
    setTimeout(function () { $("#title5").addClass("title_img5"); }, 800);
    setTimeout(function () { $(".movenone1").addClass("move"); }, 2000);
    $(".record:first .bak").addClass("d_ba_right");
    $.mobile.changePage('#pageTwo', { transition: "slideup", changeHash: false });
});
$("#pageTwo").bind("swiperup", function (event) {
    clearAll()
    $(".HeaderWord").addClass("HeaderWordUP");
    $(".CenterImg1").addClass("CenterImgmove1");
    $(".CenterImg2").addClass("CenterImgmove2");
    $(".CenterImg3").addClass("CenterImgmove3");
    $(".CenterImg4").addClass("CenterImgmove4");
    $(".ImgWord").addClass("imgwordimg");
    setTimeout(function () {
        $(".flop1").addClass("flopimg1");
        $(".flop2").addClass("flopimg2");
        $(".flop3").addClass("flopimg3");
        $(".flop4").addClass("flopimg4");
        $(".poto").addClass("potoImg");
    }, 1500)
    setTimeout(function () {
        $(".flop1").removeClass("flopimg1");
        $(".flop2").removeClass("flopimg2");
        $(".flop3").removeClass("flopimg3");
        $(".flop4").removeClass("flopimg4");

        $(".flop1").addClass("Imgflop");
        $(".flop2").addClass("Imgflop2");
        $(".flop3").addClass("Imgflop3");
        $(".flop4").addClass("Imgflop4");
    }, 3600)

    a = setTimeout(function () {
        $("#card1").attr("src", "Assets/Images/yagu5.png");

    }, 3600)
    b = setTimeout(function () {

        $("#card2").attr("src", "Assets/Images/yagu6.png");

    }, 5600)
    c = setTimeout(function () {


       $("#card3").attr("src", "Assets/Images/yagu7.png");

    }, 7600)
    d=setTimeout(function () {

        $("#card4").attr("src", "Assets/Images/yagu8.png");
    }, 9600)



    $.mobile.changePage('#pageThree', { transition: "slideup", changeHash: false });
});

$("#pageTwo").bind("swiperdown", function (event) {
  
    $(".date").addClass("dumpup");
    $(".yagu").addClass("divLeft");
    $(".jialulu").addClass("divRight");
    $(".Lightning").addClass("lightin");
    $(".Logo").addClass("LogoIn");
    t = setTimeout(function () {
        $(".movenone").addClass("move");
    }, 3500);
    $.mobile.changePage('#pageOne', { transition: "slidedown", changeHash: false });
});


$("#pageThree").bind("swiperdown", function (event) {
    $("#mask").hide();
    $("#pagefour").hide();
    clearAll();
    clearTimeout(a);
    clearTimeout(b);
    clearTimeout(c);
    clearTimeout(d);
    $("#mask1").hide();
    $("#title1").addClass("title_img1");
    setTimeout(function () { $("#title2").addClass("title_img2"); }, 200);
    setTimeout(function () { $("#title3").addClass("title_img3"); }, 400);
    setTimeout(function () { $("#title4").addClass("title_img4"); }, 600);
    setTimeout(function () { $("#title5").addClass("title_img5"); }, 800);
    setTimeout(function () { $(".movenone1").addClass("move"); }, 2000);
    $(".record:first .bak").addClass("d_ba_right");
    $.mobile.changePage('#pageTwo', { transition: "slidedown", changeHash: false });
});







$(".beat").click(function () {
    $(this).addClass("beatCss");
    $(".poto").removeClass("potoImg");

    $(".flop1").removeClass("Imgflop");
    $(".flop2").removeClass("Imgflop2");
    $(".flop3").removeClass("Imgflop3");
    $(".flop4").removeClass("Imgflop4");

    $(".flop1").addClass("ngs");
    $(".flop2").addClass("ngs");
    $(".flop3").addClass("ngs");
    $(".flop4").addClass("ngs");
    setTimeout(function () {

        $("#mask").show();
        $("#pagefour").show();
        $("#pageThree").unbind("swiperdown")
    }, 700)

})

$("#title1").on("click", function () {
    Clear();
    $("#mask1").show();
    $("#content1").addClass("d_content");
    $("#content1 .body").addClass("d_body");
    $("#content1 .body").addClass("d_body_txt");
    $(this).parent().find(".bak").addClass("d_bak_right");
})

$("#title2").on("click", function () {
    Clear();
    $("#mask1").show();
    $("#content2").addClass("d_content");
    $("#content2 .body").addClass("d_body");
    $("#content2 .body").addClass("d_body_txt");
    $(this).parent().find(".bak").addClass("d_bak_right");
})
$("#title3").on("click", function () {
    Clear();
    $("#mask1").show();
    $("#content3").addClass("d_content");
    $("#content3 .body").addClass("d_body");
    $("#content3 .body").addClass("d_body_txt");
    $(this).parent().find(".bak").addClass("d_bak_right");
})
$("#title4").on("click", function () {
    Clear();
    $("#mask1").show();
    $("#content4").addClass("d_content");
    $("#content4 .body").addClass("d_body");
    $("#content4 .body").addClass("d_body_txt");
    $(this).parent().find(".bak").addClass("d_bak_right");
})
$("#title5").on("click", function () {
    Clear();
    $("#mask1").show();
    $("#content5").addClass("d_content");
    $("#content5 .body").addClass("d_body");
    $("#content5 .body").addClass("d_body_txt");
    $(this).parent().find(".bak").addClass("d_bak_right");
})
$(".content").on("click", function () {

    $(this).removeClass("d_content");
    $(this).addClass("ngse");
    $("#mask1").hide();
})
$("#mask1").on("click", function () {
    

    $("#content1").removeClass("d_content");
    $("#content2").removeClass("d_content");
    $("#content3").removeClass("d_content");
    $("#content4").removeClass("d_content");
    $("#content5").removeClass("d_content");

    $("#mask1").hide();
})



function Clear() {

    $("#content1").removeClass("d_content");
    $("#content1.body").removeClass("d_body");
    $("#content1.body").removeClass("d_body_txt");
    $(".record .bak").removeClass("d_ba_right");

    $("#content2").removeClass("d_content");
    $("#content2.body").removeClass("d_body");
    $("#content2.body").removeClass("d_body_txt");
    $(".record .bak").removeClass("d_bak_right");

    $("#content3").removeClass("d_content");
    $("#content3.body").removeClass("d_body");
    $("#content3.body").removeClass("d_body_txt");
    $(".record .bak").removeClass("d_bak_right");

    $("#content4").removeClass("d_content");
    $("#content4.body").removeClass("d_body");
    $("#content4.body").removeClass("d_body_txt");
    $(".record .bak").removeClass("d_bak_right");

    $("#content5").removeClass("d_content");
    $("#content5.body").removeClass("d_body");
    $("#content5.body").removeClass("d_body_txt");
    $(".record .bak").removeClass("d_bak_right");


}


$(".InputText").on("focus", function () {
    $(this).addClass("InputText1");
    $(this).val("");
})
$("#MailButton").on("click", function () {
     //if (getCookie("Key")==null) {
        var Mailtext = $(".InputText").val();
        var myreg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
        if (myreg.test(Mailtext)) {
        var email = Mailtext;
        setCookie("email", email);
        $.ajax({
            url: "http://wx.digicru.tyyouxi.com/lotto.php?email="+email,
            type: "get",         
       datatype: "json",
            error: function () {
                alert("chucuo");
            },
            success: function (data) {
       
                if (data != null) {
                  
                  var obj = JSON.parse(data); //由JSON字符串转换为JSON对象
                    
                  var value = "";
                  var masg = obj.msg;
                  setCookie("msg", masg);
                  if (obj.code == "success") {
                    
                      value = obj.key;
                        setCookie("Key", value);
                         $.ajax({
                            url: "http://wx.digicru.tyyouxi.com/send.php?email=" + email + "&key=" + value,
                            type: "get",
                            datatype: "json",
                            error: function () {
                                alert("chucuo");
                            },
                            success: function (data) {
                                if (data != null) {
                                    var obj1 = JSON.parse(data);
                                    alert(obj1.msg);

                                    setCookie("code", obj1.code);
                                          window.location = "Prize.html";

                                }
                                else {
                                    alert("发送邮件失败！")
                                }

                            }
                        });
                       
                    
                      

                  }
                  else {
                        alert("对不起，您已经参加过了");
                  }
                  

                  

                }

            }
        });

         }
            else {

        document.getElementById("pStr").innerHTML = "邮箱格式错误";
        return false;
                    }

    //}
     //else{

      //  alert("对不起，您已经参加过了");
     //}
    
});

function getCookie(name) {
    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");

    if (arr = document.cookie.match(reg))

        return unescape(arr[2]);
    else
        return null;
}
function setCookie(name, value) {
    var Days = 14;
    var exp = new Date();
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
}

$("#mask").on("click", function () {
    $(this).hide();
    $("#pagefour").hide();
    $("#pageThree").bind("swiperdown", function (event) {
        $("#mask").hide();
        $("#pagefour").hide();
        clearAll();
        clearTimeout(a);
        clearTimeout(b);
        clearTimeout(c);
        clearTimeout(d);
        $("#mask1").hide();
        $("#title1").addClass("title_img1");
        setTimeout(function () { $("#title2").addClass("title_img2"); }, 200);
        setTimeout(function () { $("#title3").addClass("title_img3"); }, 400);
        setTimeout(function () { $("#title4").addClass("title_img4"); }, 600);
        setTimeout(function () { $("#title5").addClass("title_img5"); }, 800);
        setTimeout(function () { $(".movenone1").addClass("move"); }, 2000);
        $(".record:first .bak").addClass("d_ba_right");
        $.mobile.changePage('#pageTwo', { transition: "slidedown", changeHash: false });
    });
})
function clearAll() {
    $(".movenone").removeClass("move");
    $(".date").removeClass("dumpup");
    $(".yagu").removeClass("divLeft");
    $(".jialulu").removeClass("divRight");
    $(".Lightning").removeClass("lightin");
    $(".Logo").removeClass("LogoIn");

    $("#title2").removeClass("title_img2");
    $("#title3").removeClass("title_img3");
    $("#title4").removeClass("title_img4");
    $("#title5").removeClass("title_img5");
    $("#title1").removeClass("title_img1");
    $(".record:first .bak").removeClass("d_ba_right");
    $(".movenone1").removeClass("move");
    Clear();




    $(".HeaderWord").removeClass("HeaderWordUP");
    $(".CenterImg1").removeClass("CenterImgmove1");
    $(".CenterImg2").removeClass("CenterImgmove2");
    $(".CenterImg3").removeClass("CenterImgmove3");
    $(".CenterImg4").removeClass("CenterImgmove4");
    $(".ImgWord").removeClass("imgwordimg");
    $(".poto").removeClass("potoImg");
    $(".flop1").removeClass("Imgflop");
    $(".flop2").removeClass("Imgflop2");
    $(".flop3").removeClass("Imgflop3");
    $(".flop4").removeClass("Imgflop4");

    $("#card1").attr("src", "Assets/Images/yagu1.png");
    $("#card2").attr("src", "Assets/Images/yagu2.png");
    $("#card3").attr("src", "Assets/Images/yagu3.png");
    $("#card4").attr("src", "Assets/Images/yagu4.png");

    $("#card1").removeClass("beatCss");
    $("#card2").removeClass("beatCss");
    $("#card3").removeClass("beatCss");
    $("#card4").removeClass("beatCss");



}


$(".globalAudio").bind("click", function () {
    var media = $(this).find("audio")[0];
    if (media.paused) {
        $(".icon-music").removeClass("play");
        media.play();
        audioPaused = true;
        $(".icon-music").addClass("play");
    } else {
        media.pause();
        audioPaused = false;
        $(".icon-music").removeClass("play");
    }
});




