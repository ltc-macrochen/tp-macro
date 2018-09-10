<?php

namespace app\common\models;

use common\components\OrganizationComponent;
use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "t_user".
 *
 * @property string $id
 * @property string $user_name
 * @property string $user_role
 * @property string $user_password
 * @property string $user_head
 * @property string $nickname
 * @property string $email
 * @property string $mobile
 * @property string $gender
 * @property string $status
 * @property integer $last_login_at
 *
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    // 用户角色
    const USER_ROLE_USER    =  0; // 普通用户
    const USER_ROLE_ADMIN   =  1; // 超级管理员
    const USER_ROLE_ADMIN_MERCHANT = 2; // 商户管理员

    // 用户状态
    const USER_STATUS_NORMAL   = 0; // 正常
    const USER_STATUS_DISABLE  = 1; // 禁用

    // 性别
    const USER_GENDER_UNDEFINED = 0; // 未定义
    const USER_GENDER_MALE      = 1; // 男
    const USER_GENDER_FEMALE    = 2; // 女

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 't_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_role', 'status', 'gender', 'last_login_at'], 'integer'],
            [['user_name', 'user_password', 'nickname'], 'string', 'max' => 40],
            [['user_head', 'email'], 'string', 'max' => 255],
            [['mobile'], 'string', 'max' => 11],
            ['user_name', 'required','message'=>'登录名称不能为空'],
            // ['user_password', 'required','message'=>'登录密码不能为空'], // 生成钱包时输入
            [['private_key'], 'string', 'max' => 160],
            [['user_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => '登录名称',
            'user_role' => '用户角色',
            'user_password' => '登录密码',
            'nickname' => '用户昵称',
            'status' => '用户状态',
            'user_head' => '用户头像',
            'email' => '邮箱',
            'mobile' => '手机号',
            'gender' => '性别',
            'last_login_at' => '最近登录时间'
        ];
    }

    /**
     * 是否超级管理员
     * @return bool
     */
    public function isAdmin()
    {
        return ($this->user_role & static::USER_ROLE_ADMIN) != 0;
    }

    /**
     * 根据ID查找已激活的用户
     * @param int|string $id
     * @return null|static
     */
    public static function findIdentity($id)
    {
        if ($id === 0) {
            return self::initAdminUser();
        } else {
            return static::findOne(['id' => $id, 'status' => static::USER_STATUS_NORMAL]);
        }
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getAuthKey()
    {
        return $this->id;
    }

    public function validateAuthKey($authKey)
    {
        return $this->id === $authKey;
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function validatePassword($password)
    {
        return $this->user_password == md5($password);
    }

    /**
     * 性别数组
     * @return array
     */
    public function genderNameMap()
    {
        return array(
            self::USER_GENDER_UNDEFINED => '未设置',
            self::USER_GENDER_MALE => '先生',
            self::USER_GENDER_FEMALE => '女士'
        );
    }

    /**
     * 性别
     * @return mixed|string
     */
    public function getGenderName()
    {
        $genderNameMap = $this->genderNameMap();
        if (isset($genderNameMap[$this->gender])) {
            return $genderNameMap[$this->gender];
        }

        return '-';
    }

    /**
     * 查找超级管理员
     * @param $username
     * @return User|null
     */
    public static function findAdminByUsername($username)
    {
        if ($username !== Yii::$app->params['adminName']) {
            return null;
        }

        return static::initAdminUser();
    }

    /**
     * 初始化admin
     * @return User
     */
    protected static function initAdminUser()
    {
        $admin = new self();
        $admin->user_name = Yii::$app->params['adminName'];
        $admin->user_password = md5(Yii::$app->params['adminPassword']);
        $admin->nickname = Yii::$app->params['adminNick'];
        $admin->user_role = self::USER_ROLE_ADMIN;
        $admin->status = self::USER_STATUS_NORMAL;
        $admin->id = 0;
        return $admin;
    }

    /**
     * 是否超级管理员
     * @return bool
     */
    public function isSuperAdmin()
    {
        if ($this->id === 0 && $this->user_role == self::USER_ROLE_ADMIN) {
            return true;
        }
        return false;
    }
}
