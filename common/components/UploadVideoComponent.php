<?php
namespace common\components;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class UploadVideoComponent extends Model
{

    /**
     * @var UploadedFile
     */
    public $imageFile;
    public $imagePath;

    /**
     * @var UploadedFile
     */
    public $videoFile;
    public $videoPath;
    
    public $canEmpty = false;

    public function rules()
    {
        return [
                [['imageFile'], 'file', 'skipOnEmpty' => $this->canEmpty, 'extensions' => 'png, jpg', 'maxFiles' => 1, 'checkExtensionByMimeType' => false],
                [['videoFile'], 'file', 'skipOnEmpty' => $this->canEmpty, 'extensions' => 'mp4, avi', 'maxFiles' => 1, 'checkExtensionByMimeType' => false],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $time = time();
            $saveSuccess = true;
            FileHelper::createDirectory('videoimg');
            FileHelper::createDirectory('video');
            if(!empty($this->imageFile)) {
                $this->imagePath = 'videoimg/img' . $time . Yii::$app->security->generateRandomString(20) . '.' . $this->imageFile->extension;
                $saveSuccess = $this->imageFile->saveAs ( $this->imagePath );
            }

            if(!empty($this->videoFile)) {
                $this->videoPath = 'video/v' . $time . Yii::$app->security->generateRandomString(20) . '.' . $this->videoFile->extension;
                $saveSuccess = $saveSuccess && $this->videoFile->saveAs ( $this->videoPath );
            }
            
            if (! $saveSuccess) {
                $this->deleteAllUploadFiles();
            }
            return $saveSuccess;
        } else {
            return false;
        }
    }
    
    /**
     * 删除所有文件，在失败回滚的时候使用
     */
    public function deleteAllUploadFiles()
    {
        if(isset($this->imagePath)) {
            unlink($this->imagePath);
        }

        if(isset($this->videoPath)) {
            unlink($this->videoPath);
        }
    }
}

