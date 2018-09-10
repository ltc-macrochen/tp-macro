<?php
/**
 * User: macrochen
 * Time: 2018/5/21 19:29
 */

namespace common\components;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class UploadImgComponent extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;
    public $imagePath;

    public $canEmpty = false;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => $this->canEmpty, 'extensions' => 'png, jpg', 'maxFiles' => 1, 'checkExtensionByMimeType' => false],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            FileHelper::createDirectory('image');
            $time = time();
            $saveSuccess = true;
            if(!empty($this->imageFile)) {
                $dirPath='image/app/';
                if(!is_dir($dirPath . "image")){
                    FileHelper::createDirectory($dirPath . "image");
                }
                $this->imagePath = $dirPath.'image/app_img' . $time . Yii::$app->security->generateRandomString(20) . '.' . $this->imageFile->extension;
                $saveSuccess = $this->imageFile->saveAs ( $this->imagePath );
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
    }
}