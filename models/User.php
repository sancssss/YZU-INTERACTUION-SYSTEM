<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $user_number
 * @property string $user_name
 * @property string $user_password
 * @property string $user_authKey
 *
 * @property StudentWork[] $studentWorks
 * @property TeacherWork[] $teacherWorks
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_number', 'user_name', 'user_password'], 'required'],
            [['user_number'], 'integer'],
            [['user_name'], 'string', 'max' => 255],
            [['user_password'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_number' => '用户ID',
            'user_name' => '姓名',
            'user_password' => '密码',
            'user_authKey' => 'User Auth Key',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->user_authKey = Yii::$app->security->generateRandomString();
                $this->user_password = md5($this->user_password);
            }
            return true;
        }
        return false;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentWorks()
    {
        return $this->hasMany(StudentWork::className(), ['user_number' => 'user_number']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherWorks()
    {
        return $this->hasMany(TeacherWork::className(), ['user_number' => 'user_number']);
    }

    public function getAuthKey() {
        return $this->user_authKey;
    }

    public function getId() {
        return $this->user_number;
    }

    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    public static function findIdentity($id) {
         return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne(['access_token' => $token]);
    }

}