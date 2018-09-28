
// 设置校花轮播
function setFlowersSlick() {
    $(".flowers_turn").slick({
        speed: 1000,
        dots: true,
        infinite: true,
        arrows: true,
        draggable: false,
        prevArrow: ".prev",
        nextArrow: ".next",
        autoplay:false,
        slidesToShow : 1
    });
}

// 点击显示视频
function onShowVideo() {
    $('.video_img').on('click', function () {
        $('#video1').attr('src', $(this).data('video'));
        $(".pop").show();
        $(".player1").show();
        $("#video1")[0].play();
    });
}

function increaseValue(obj, value) {
    if (typeof(obj) !== 'object') {
        return false;
    }
    var oldValue = obj.html();
    obj.html(parseInt(oldValue) + parseInt(value));
}

// 获取所有校花
function getAllUsers() {
    $.post('/index.php?r=data/all-girls', '', function (res) {
        if (res.err === 0) {
            $('.flowers_turn').empty();
            var d = res.data;
            for (var i=0; i<d.length; i++) {
                var uHtml = '<div class="flowers_img">' +
                    '                <img src="images/flowers_bg.png" alt="">' +
                    '                <div class="flowers_bottom">' +
                    '                    <img src="images/flowers_bottom.png" alt="">' +
                    '                </div>' +
                    '                <div class="video_img" data-id="'+d[i]['id']+'" data-video="'+d[i]['video']+'">' +
                    '                    <img src="'+d[i]['head']+'" alt="">' +
                    '                </div>' +
                    '                <!--姓名-->' +
                    '                <p class="name">姓名:<span>'+d[i]['name']+'</span></p>' +
                    '                <!--赛区-->' +
                    '                <p class="division">赛区:<span>'+d[i]['area']+'</span></p>' +
                    '                <!--票数-->' +
                    '                <p class="votes">票数:<span><span class="votecount-'+d[i]['id']+'">'+d[i]['vote_count']+'</span>票</span></p>' +
                    '                <!--页数-->' +
                    '                <p class="page_num">'+(i+1)+'<span>/'+d.length+'</span></p>' +
                    '' +
                    '                <div class="next">' +
                    '                    <img src="images/next_btn.png" alt="">' +
                    '                </div>' +
                    '                <div class="prev">' +
                    '                    <img src="images/prev_btn.png" alt="">' +
                    '                </div>' +
                    '' +
                    '            </div>';
                $('.flowers_turn').append(uHtml);
            }

            if (d.length > 0) {
                setFlowersSlick();
                onShowVideo();
            } else {
                $('.vote_btn').hide();
            }
        }
    }, 'json');
}

// 投票
function doVote(uid) {
    if (undefined == uid || !uid) {
        alert('请选择投票对象');
        return false;
    }

    var doing = false;
    if (doing) {
        alert('您的操作过于频繁，请稍后再试');
        return false;
    }
    doing = true;

    $.post('/index.php?r=data/do-vote', {id:uid}, function (res) {
        alert(res.msg);
        if (res.err === 0) {
            increaseValue($('.votecount-' + uid), 1);
        }
        doing = false;
    }, 'json');
}

$(function () {
    getAllUsers();

    $('.vote_btn img').on('click', function () {
        var uid = $('.slick-active .video_img').data('id');
        doVote(uid);
    });
});