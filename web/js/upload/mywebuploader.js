/**
 * Created by lq884 on 2016/10/19.
 */
// 文件上传
/*
* 预定义一些配置参数
*   CHECK_CHUNK_SERVICE     //检查分片的服务器地址
*   UPLOAD_SERVICE          //上传文件的服务器地址
*   CHECK_CHUNK_TIME_OUT    //检查分片超时时间
*   CHUNK_SIZE              //分片大小 1024*1024表示1M
*   UPLOAD_FILE_FIELD       //自动填充上传文件路径到指定input的ID值
*   UPLOAD_FILE_NAME        //自动填充上传文件的名称到指定的input的ID值
*   CSRF_TOKEN              //表单验证Csrf-token值
*
*
*
* */
jQuery(function() {
    //alert(UPLOAD_FILE_TYPE);
    //配置默认参数
    CHECK_CHUNK_SERVICE = typeof(CHECK_CHUNK_SERVICE)   == 'undefined' ? '':CHECK_CHUNK_SERVICE;
    UPLOAD_SERVICE = typeof(UPLOAD_SERVICE)             == 'undefined' ? '':UPLOAD_SERVICE;
    CHECK_CHUNK_TIME_OUT = typeof(CHECK_CHUNK_TIME_OUT) == 'undefined' ? 5000:CHECK_CHUNK_TIME_OUT;
    CHUNK_SIZE = typeof(CHUNK_SIZE)                     == 'undefined' ? (1024*1024):CHUNK_SIZE;
    UPLOAD_FILE_FIELD = typeof(UPLOAD_FILE_FIELD)       == 'undefined' ? 'content-original_path':UPLOAD_FILE_FIELD;
    UPLOAD_FILE_NAME = typeof(UPLOAD_FILE_NAME)         == 'undefined' ? 'content-name':UPLOAD_FILE_NAME;
    UPLOAD_FILE_SIZE = typeof (UPLOAD_FILE_SIZE)        == 'undefined' ? 'authorupload-filesize':UPLOAD_FILE_SIZE;
    UPLOAD_FILE_TYPE = typeof(UPLOAD_FILE_TYPE)         == 'undefined' ? 'video':UPLOAD_FILE_TYPE;
    var $ = jQuery,
        $list = $('#thelist'),
        $btn = $('#ctlBtn1'),
        $btns = $('.btns'),
        state = 'pending',
        uploader,
        fileMd5,
        acceptType
        ;
    //上传文件允许类型
    switch (UPLOAD_FILE_TYPE){
        case 'video':
            acceptType = {
                title: 'Video',
                extensions: 'mp4',
                mimeTypes: 'video/mp4'
            };
            break;
        case 'audio':
            acceptType = {
                title: 'audio',
                extensions: 'mp3',
                mimeTypes: 'audio/mp3'
            };
            break;
    }
    //以下设置只是为了排版界面样式
    $(".field-content-original_path").css("display","inline");
    $("#uploader").css("display","inline");
    $(".btns").css("display","inline");
    //处理用户点提交后才上传视频
    $('#falseUpload').on('click',function(){
        if($("#content-publisheddate").val() == ""){
            alert("您还没有填写发布时间");
            return false;
        }
        if($(".webuploader-pick").length != 0){
            alert("您还没有选择要上传的文件");
            return false;
        }
        //如果已经有上传的路径被添加到input中,则直接提交
        if($('#'+UPLOAD_FILE_FIELD).val() != "" ){
            $('#trueUpload').trigger("click");
            return false;
        }
        $("#ctlBtn1").trigger("click"); //webUploader上传按钮
        return false;
    });
    //注册断点上传和秒传文件的处理函数
    WebUploader.Uploader.register({
            'before-send' : 'beforeSend'         //断点续传
        },
        {
            beforeSend:function(block){
               // console.log(block);
                var task = new $.Deferred();
                $.ajax({
                    type:"POST",
                    url:CHECK_CHUNK_SERVICE,
                    data:{
                        status:"checkChunk"
                        ,hashMD5: fileMd5
                        , chunkIndex: block.chunk
                        , chunks:block.chunks
                        , size: block.end - block.start
                        , type:block.file.ext
                    }
                    ,cache:false
                    ,timeout:CHECK_CHUNK_TIME_OUT //超时就认为该分片没有上传过
                    ,dataType:"json"
                }).then(function(data ,textStatus, jqXHR){
                    if(data.ifExist){   //若存在，返回失败给WebUploader，表明该分块不需要上传
                        task.reject();
                    }else{
                        task.resolve();
                    }
                },function(jqXHR, textStatus, errorThrown){    //任何形式的验证失败，都触发重新上传
                    task.resolve();
                });
                return $.when(task);
            }
        });
    uploader = WebUploader.create({
        // swf文件路径
        swf: '/js/upload/Uploader.swf',

        chunked:true,
        chunkSize:CHUNK_SIZE,
        prepareNextFile: true,
        // 文件接收服务端。
        server:UPLOAD_SERVICE,

        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: {
            id:'#picker',
            label:'添加您要上传的文件',
            multiple:false,

        },
        accept:acceptType, //上传接受类型
        disableGlobalDnd: true,
        fileSizeLimit: 2 * 1024 * 1024 * 1024,           // 2G
        fileSingleSizeLimit: 2 * 1024 * 1024 * 1024     // 2G
    });
    // 当有文件添加进来的时候,开始计算文件的MD5
    uploader.on( 'fileQueued', function( file ) {
            $list.html("");//清空原有的文件信息
            $btns.fadeOut();
         //   console.log(uploader.Queue);
        $list.append( '<div id="' + file.id + '" class="item">' +
            '<h4 class="info">' + file.name + '</h4>' +
            '<p class="state"></p>' +
            '</div>' );
        var $li = $( '#'+file.id );
        var start =  +new Date();
        this.md5File(file).progress(function(percentage){
            //MD5计算的进度;
            $percent = $li.find('.progress .progress-bar');
            // 避免重复创建
            if ( !$percent.length ) {
                $percent = $('<div class="progress progress-striped active">' +
                    '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                    '</div>' +
                    '</div>').appendTo( $li ).find('.progress-bar');
            }
            $li.find('p.state').text('正在计算文件的MD5值');
            $percent.css( 'width', (percentage * 100) + '%' );
        }).then(function(ret){
            //$("#"+file.id).remove();
            //ret为计算后的MD5值
            var end = +new Date();
           // $btns.fadeIn();
            //显示上传按钮
            $("#ctlBtn1").fadeIn();
            $("#picker").fadeOut();
            $(".webuploader-pick").remove();
            $li.find('p.state').text('文件的MD5值计算完毕,用时:'+((end - start)/1000).toFixed(1) + '秒');
            $( '#'+file.id ).find('.progress').fadeOut(1);
            $li.find('.progress .progress-bar').css( 'width', 0+ '%' );
            fileMd5 = ret;
            file.fileMd5 = ret;
            //添加上传文件名
            inputName = $('#'+UPLOAD_FILE_NAME).val();
            if(inputName.trim() == ''){
                $('#'+UPLOAD_FILE_NAME).val(file.name);
            }
            //添加上传文件大小
            $('#'+UPLOAD_FILE_SIZE).val(file.size);
        });

    });

    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        var $li = $( '#'+file.id ),
            $percent = $li.find('.progress .progress-bar');
            if($li.find('.progress').css('display') == 'none'){
               $li.find('.progress').fadeIn();
            }

        // 避免重复创建
        if ( !$percent.length ) {
            $percent = $('<div class="progress progress-striped active">' +
                '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                '</div>' +
                '</div>').appendTo( $li ).find('.progress-bar');
        }

        $li.find('p.state').text('上传中');

        $percent.css( 'width', percentage * 100 + '%' );
    });
    uploader.on( 'uploadSuccess', function( file ,reason) {
        $( '#'+file.id ).find('p.state').text('已上传');
        console.log(file);
        console.log(reason);
        $('#'+UPLOAD_FILE_FIELD).val(reason.result);
        //$("#uploader").remove();
        //上传成功后提交表单信息
        $('#trueUpload').trigger("click");


    });
    uploader.on( 'uploadError', function( file,reason ) {
        $( '#'+file.id ).find('p.state').text('上传出错');
        $("#uploaderStatus").text('文件上传失败');
        alert("文件上传失败");
    });
    uploader.on( 'error',function(type){
        switch (type){
            case 'Q_TYPE_DENIED':
                alert('您不可以上传该类型的文件');
                break;
            case 'Q_EXCEED_SIZE_LIMIT':
                alert('您上传的文件过大');
                break;
        }
    });
    uploader.on( 'uploadComplete', function( file ) {
        $( '#'+file.id ).find('.progress').fadeOut();
    });
    uploader.on( 'stopUpload',function(file){
        uploader.stop(true);
    });
    uploader.on( 'all', function( type ) {
        if ( type === 'startUpload' ) {
            state = 'uploading';
        } else if ( type === 'stopUpload' ) {
            state = 'paused';
        } else if ( type === 'uploadFinished' ) {
            state = 'done';
        }

        if ( state === 'uploading' ) {
            $btn.text('暂停上传');
        } else {
            $btn.text('开始上传');
        }
    });
    /**
     * 上传之前的处理
     */
   uploader.on('uploadBeforeSend',function(object ,data ,headers ){
        //data.fileMd5 = fileMd5;
       data.hashMD5 =  fileMd5;
       data.status = 'upload';
       data._csrf = CSRF_TOKEN;
    });

    $btn.on( 'click', function() {
        if ( state === 'uploading' ) {
            uploader.stop(true);
        } else {
            uploader.upload();
        }
    });

});