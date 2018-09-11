// common variables
var upload_iBytesUploaded = 0;
var upload_iBytesTotal = 0;
var upload_iPreviousBytesLoaded = 0;
var upload_iMaxFilesize = 1048576; // 1MB
var upload_iLastUpdateTime = 0;   // 上次刷新时间，用于计算上传速度
var upload_iCurUpdateTime = 0;    // 本次刷新时间，用于计算上传速度
var upload_oTimer = 0;
//var sResultFileSize = '';
//var sUploadFinishUrl = ''; // 上传成功后跳转的url

function secondsToTime(secs) { // we will use this function to convert seconds in normal time format
    var hr = Math.floor(secs / 3600);
    var min = Math.floor((secs - (hr * 3600))/60);
    var sec = Math.floor(secs - (hr * 3600) -  (min * 60));

    if (hr < 10) {hr = "0" + hr; }
    if (min < 10) {min = "0" + min;}
    if (sec < 10) {sec = "0" + sec;}
    if (hr) {hr = "00";}
    return hr + ':' + min + ':' + sec;
};

function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB'];
    if (bytes == 0) return 'n/a';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
};

function fileSelected(inputId, imgId, labelId) {

    // get selected file element
    var oFile = document.getElementById(inputId).files[0];

    // filter for image files
    var rFilter = /^(image\/bmp|image\/gif|image\/jpeg|image\/png|image\/tiff)$/i;
    if (! rFilter.test(oFile.type)) {
        //document.getElementById('error').style.display = 'block';
        return;
    }

    // little test for filesize
    if (oFile.size > upload_iMaxFilesize) {
        //document.getElementById('warnsize').style.display = 'block';
        return;
    }

    // get preview element
    var oImage = document.getElementById(imgId);

    // prepare HTML5 FileReader
    var oReader = new FileReader();
        oReader.onload = function(e){

        // e.target.result contains the DataURL which we will use as a source of the image
        oImage.src = e.target.result;

        //@macro 图片预览
        oViewImg = document.getElementById('view_'+imgId);
        if('undefined' != oViewImg){
            oViewImg.src = e.target.result;
        }
        oImage.onload = function () { // binding onload event

            // we are going to display some custom image information here
            //sResultFileSize = bytesToSize(oFile.size);
            //document.getElementById('fileinfo').style.display = 'block';
            //document.getElementById('filename').innerHTML = 'Name: ' + oFile.name;
            //document.getElementById('filesize').innerHTML = 'Size: ' + sResultFileSize;
            //document.getElementById('filetype').innerHTML = 'Type: ' + oFile.type;
            //document.getElementById('filedim').innerHTML = 'Dimension: ' + oImage.naturalWidth + ' x ' + oImage.naturalHeight;
            document.getElementById(labelId).innerHTML = '尺寸: ' + oImage.naturalWidth + ' * ' + oImage.naturalHeight;
        };
    };

    // read selected file as DataURL
    oReader.readAsDataURL(oFile);
}

function startUploading(modalId, formId) {
    // cleanup all temp states
    upload_iPreviousBytesLoaded = 0;
    upload_iCurUpdateTime = (new Date()).getTime();
    upload_iLastUpdateTime = 0;
    //document.getElementById('upload_response').style.display = 'none';
    //document.getElementById('error').style.display = 'none';
    //document.getElementById('error2').style.display = 'none';
    //document.getElementById('abort').style.display = 'none';
    //document.getElementById('warnsize').style.display = 'none';
    document.getElementById('upload_progress_percent').innerHTML = '';
    var oProgress = document.getElementById('upload_progress');
    oProgress.style.display = 'block';
    oProgress.style.width = '0px';

    if(!$.fn.yiiActiveForm.Closure)
    //if(!$('#' + formId).validate()) {
    //    alert('error');
    //    return false;
    //}

    // 弹出窗口
    $('#' + modalId).modal();

    // get form data for POSTing
    //var vFD = document.getElementById('upload_form').getFormData(); // for FF3
    var vFD = new FormData(document.getElementById(formId));

    // create XMLHttpRequest object, adding few event listeners, and POSTing our data
    var oXHR = new XMLHttpRequest();        
    oXHR.upload.addEventListener('progress', uploadProgress, false);
    oXHR.addEventListener('load', uploadFinish, false);
    oXHR.addEventListener('error', uploadError, false);
    oXHR.addEventListener('abort', uploadAbort, false);
    oXHR.open('POST', document.getElementById(formId).action);
    oXHR.send(vFD);

    // set inner timer
    upload_oTimer = setInterval(doInnerUpdates, 300);
}

function doInnerUpdates() { // we will use this function to display upload speed
    var iCB = upload_iBytesUploaded;
    var iDiff = iCB - upload_iPreviousBytesLoaded;

    // if nothing new loaded - exit
    if (iDiff == 0)
        return;

    upload_iPreviousBytesLoaded = iCB;
    if((upload_iLastUpdateTime == 0) || (upload_iCurUpdateTime == upload_iLastUpdateTime)) {
        iDiff = iDiff * 2;
    } else {
        iDiff = iDiff * 1000 / (upload_iCurUpdateTime - upload_iLastUpdateTime);
    }
    var iBytesRem = upload_iBytesTotal - upload_iPreviousBytesLoaded;
    var secondsRemaining = iBytesRem / iDiff;
    if(iBytesRem == 0) {secondsRemaining = 0, iDiff = 0}

    // update speed info
    var iSpeed = iDiff.toString() + 'B/s';
    if (iDiff > 1024 * 1024) {
        iSpeed = (Math.round(iDiff * 100/(1024*1024))/100).toString() + 'MB/s';
    } else if (iDiff > 1024) {
        iSpeed =  (Math.round(iDiff * 100/1024)/100).toString() + 'KB/s';
    }

    document.getElementById('upload_speed').innerHTML =  '上传速度：' + iSpeed;
    document.getElementById('upload_remaining').innerHTML = '剩余时间：'  + secondsToTime(secondsRemaining);
}

function uploadProgress(e) { // upload process in progress
    if (e.lengthComputable) {
        upload_iBytesUploaded = e.loaded;
        upload_iBytesTotal = e.total;
        upload_iLastUpdateTime = upload_iCurUpdateTime;
        upload_iCurUpdateTime = (new Date()).getTime();
        var iPercentComplete = Math.round(e.loaded * 100 / e.total);
        var iBytesTransfered = bytesToSize(upload_iBytesUploaded);

        document.getElementById('upload_progress_percent').innerHTML = '上传进度：' + iPercentComplete.toString() + '%';
        document.getElementById('upload_progress').style.width = iPercentComplete.toString() + '%';
        document.getElementById('upload_b_transfered').innerHTML =  '已经上传：' + iBytesTransfered;
        if (iPercentComplete == 100) {
            var oUploadResponse = document.getElementById('upload_response');
            oUploadResponse.innerHTML = '<h1>马上就好，请等待。。。</h1>';
            oUploadResponse.style.display = 'block';
            document.getElementById('upload_remaining').innerHTML =  '剩余时间：' + ' 00:00:00';
        }
    } else {
        document.getElementById('upload_progress').innerHTML = 'unable to compute';
    }
}

function uploadFinish(e) { // upload successfully finished
    //var oUploadResponse = document.getElementById('upload_response');
    //oUploadResponse.innerHTML = e.target.responseText;
    //oUploadResponse.style.display = 'block';

    clearInterval(upload_oTimer);

    document.getElementById('upload_progress_percent').innerHTML =  '下载进度：' + '100%';
    document.getElementById('upload_progress').style.width = '100%';
    //document.getElementById('upload_filesize').innerHTML = sResultFileSize;
    document.getElementById('upload_remaining').innerHTML =  '剩余时间：' + ' 00:00:00';
    document.getElementById('upload_speed').innerHTML =  '上传速度：0';

    // 判断是否由js重定向,js重定向的话直接定向到新的页面(因为直接替换html的方法会导致页面js不执行,必须要执行js的情况下使用手工重定向来实现)
    if(e.target.responseText == 'redirect') {
        var toUrl = e.target.getResponseHeader('redirectTo');
        if(toUrl != undefined && toUrl != null && typeof toUrl === "string") {
            document.location.href = toUrl;
            return;
        }
    }
    if(this.responseURL != undefined) {
        history.pushState({}, '', this.responseURL);
    } else {
        var url = e.target.getResponseHeader('Url');
        if(url != undefined && url != null && typeof url === "string") {
            history.pushState({}, '', url);
        }
    }
    var oUploadResponse = document.getElementsByTagName('html');
    oUploadResponse[0].innerHTML = e.target.responseText;
    //$('html').html(e.target.responseText);
    if(jQuery != undefined && jQuery.reloadExeFunc != undefined && typeof jQuery.reloadExeFunc === "function") {
        jQuery.reloadExeFunc();
    }
}

function uploadError(e) { // upload error
    //document.getElementById('error2').style.display = 'block';
    alert('上传失败');
    clearInterval(upload_oTimer);
}  

function uploadAbort(e) { // upload abort
    //document.getElementById('abort').style.display = 'block';
    alert('上传中断');
    clearInterval(upload_oTimer);
}