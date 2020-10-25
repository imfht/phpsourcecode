$(document).ready(function(){
       
});

$("#pageOne").bind("swiperup", function (event) {
    $.mobile.changePage('#pageTwo', { transition: "slideup", changeHash: true });
});
$("#pageTwo").bind("swiperup", function (event) {
    $.mobile.changePage('#pageThree', { transition: "slideup", changeHash: true });
});

$("#pageTwo").bind("swiperdown", function (event) {
    $.mobile.changePage('#pageOne', { transition: "slidedown", changeHash: true });
});


$("#pageThree").bind("swiperdown", function (event) {
    $.mobile.changePage('#pageTwo', { transition: "slidedown", changeHash: true });
});

$(document).on("pageinit", "#pageOne", function(event) {
    $(".date").addClass("dumpup");
    $(".yagu").addClass("divLeft");
    $(".jialulu").addClass("divRight");
    $(".Lightning").addClass("lightin");
    $(".Logo").addClass("LogoIn");
    setTimeout(function () {
       $(".movenone").addClass("move");
    }, 3500);
    console.log('pageOne-init');
});

$(document).on("pageinit", "#pageTwo", function (event) {
    $("#mask1").hide();
    $("#title1").addClass("title_img1");
    setTimeout(function () { $("#title2").addClass("title_img2"); }, 200);
    setTimeout(function () { $("#title3").addClass("title_img3"); }, 400);
    setTimeout(function () { $("#title4").addClass("title_img4"); }, 600);
    setTimeout(function () { $("#title5").addClass("title_img5"); }, 800);
    
    
    
    
    $(".record:first .bak").addClass("d_ba_right");
    $(".movenone1").addClass("move");

    console.log('pageTwo-init');
});

$(document).on("pageinit", "#pageThree", function(event) {
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

    setTimeout(function () {
        $("#card1").attr("src", "Assets/Images/yagu5.png");

    }, 3600)
    setTimeout(function () {

        $("#card2").attr("src", "Assets/Images/yagu6.png");

    }, 5600)
    setTimeout(function () {
    

        $("#card3").attr("src", "Assets/Images/yagu7.png");

    }, 7600)
    setTimeout(function () {

        $("#card4").attr("src", "Assets/Images/yagu8.png");
    }, 9600)
       

    
    console.log('pageThree-init');
});

$(".beat").click(function () {
    $(this).addClass("beatCss");
    $(".poto").removeClass("potoImg");
    $(".poto").addClass("ngse");
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

    },700)
    
})

$("#title1").on("click",function () {
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

    var Mailtext = $(".InputText");
    var myreg = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
    if (myreg.test(Mailtext.val())) {
        Mailtext.val("邮箱格式正确");
        
    }
    else {
        Mailtext.val("邮箱格式错误");
        return false;
    }
})
$("#mask").on("click", function () {
    $(this).hide();
    $("#pagefour").hide();
})