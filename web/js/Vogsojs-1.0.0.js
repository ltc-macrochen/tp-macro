var Vogsojs = {};
Vogsojs.VER = '1.0.0';
Vogsojs.data = {};
Vogsojs.alert = function(str) {
    var _this = this;

    var w = $(window).width();
    if(w < 1000){
        $(".maskAlert").addClass("wapMask").removeClass("pcMask");
        $(".alertText").addClass("wapText").removeClass("pcText");
        $(".alertSure").addClass("wapSure").removeClass("pcSure");
    }else{
        $(".alertLine").css("bottom","50px");
        $(".maskAlert").addClass("pcMask").removeClass("wapMask");
        $(".alertText").addClass("pcText").removeClass("wapText");
        $(".alertSure").addClass("pcSure").removeClass("wapSure");
    }

    if(str.length>11 ){
        $(".wapText").css({"line-height":"22px"});
    }else if((str.length<11)){
        $(".wapText").css({"line-height":"70px"});
    }
    //改变弹窗内容
    $(".alertText").text(str);

    /**弹窗出现**/
    _this.show = function(){
        $(".maskAlert").show();
        $(".alertSure").one("click",_this.hide);
    };
    /**弹窗消失**/
    _this.hide = function(){
        $(".maskAlert").hide()
    };

    _this.show();
};

/**
 * 初始化
 * 页面加载完毕以后
 * */
Vogsojs.init = function(){
    var w = $(window).width();
    var h = $(window).height();
    //console.log(w,h);
    $("body").css("height",h);
    $("body").append("<div class='maskAlert'></div>");
    $(".maskAlert").append("<div class='alertText'>敬请期待</div>");
    $(".maskAlert").append("<div class='alertLine'></div>");
    $(".maskAlert").append("<div class='alertSure'>确定</div>");

};


$(function(){
    Vogsojs.init();

});
