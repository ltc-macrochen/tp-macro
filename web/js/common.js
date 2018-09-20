var number =null;

$(function () {

    // 劳动者港湾
    $(".labor_turn").slick({
        dots:true,
        infinite: true,
        autoplay:true,
        arrows: false,
        autoplaySpeed:1000,
        speed: 1000,
        slidesToShow: 1,
        slidesToScroll: 1
    });

    // 视频轮播
    // $(".flowers_turn").slick({
    //     dots:true,
    //     infinite: true,
    //     arrows: true,
    //     speed: 1000,
    //     fade: true,
    //     cssEase: 'linear',
    //     prevArrow:	".prev",
    //     nextArrow:	".next",
    //     slidesToShow: 1,
    //     slidesToScroll: 1
    // });

    // 点击视频图片弹窗对应视频弹窗
    // $(".video_img").on("click",function(){
    //     var number = $(this).attr("data-id");
    //     $(".pop").show();
    //     $(".player"+number).show();
    //     // $(".video_img1").hide();
    //     $("#video"+number)[0].play();
    //     console.log(number);
    // });

    // 关闭视频
    $(".Shut_btn").on("click",function(){
        $(".pop").hide();
        $(".play").hide();
        videoPause();
    });

});

// 视频时间清零
function unlockVdeo01() {
    $(".player1 video")[0].play();
    $(".player1 video")[0].pause();
    document.querySelector(".player1 video").currentTime = 0;
}
function unlockVdeo02() {
    $(".player2 video")[0].play();
    $(".player2 video")[0].pause();
    document.querySelector(".player2 video").currentTime = 0;
}
function unlockVdeo03() {
    $(".player3 video")[0].play();
    $(".player3 video")[0].pause();
    document.querySelector(".player3 video").currentTime = 0;
}
function unlockVdeo04() {
    $(".player4 video")[0].play();
    $(".player4 video")[0].pause();
    document.querySelector(".player4 video").currentTime = 0;
}
function unlockVdeo05() {
    $(".player5 video")[0].play();
    $(".player5 video")[0].pause();
    document.querySelector(".player5 video").currentTime = 0;
}
function unlockVdeo06() {
    $(".player6 video")[0].play();
    $(".player6 video")[0].pause();
    document.querySelector(".player6 video").currentTime = 0;
}
function unlockVdeo07() {
    $(".player7 video")[0].play();
    $(".player7 video")[0].pause();
    document.querySelector(".player7 video").currentTime = 0;
}
function unlockVdeo08() {
    $(".player8 video")[0].play();
    $(".player8 video")[0].pause();
    document.querySelector(".player8 video").currentTime = 0;
}

// 视频初始化
function videoPause() {
    unlockVdeo01();
    // unlockVdeo02();
    // unlockVdeo03();
    // unlockVdeo04();
    // unlockVdeo05();
    // unlockVdeo06();
    // unlockVdeo07();
    // unlockVdeo08();
}
