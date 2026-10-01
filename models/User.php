<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id_user
 * @property string $fio
 * @property string $phone
 * @property string $email
 * @property string $role
 * @property string $password
 * @property string $creat_user
 * @property string $access_token

 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * ENUM field values
     */
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
      
            [['fio', 'phone', 'email', 'password'], 'required', 'on' => 'create'],
            
            [['role'], 'string'],
            [['creat_user', 'password', 'access_token'], 'safe'],
            
            [['fio'], 'string', 'max' => 255],
            [['fio'], 'match', 'pattern' => '/^[а-яА-ЯёЁ\s\-]+$/u', 'message' => 'ФИО может содержать только русские буквы, пробелы и дефисы'],
           
            [['email'], 'string', 'max' => 255],
            [['email'], 'email', 'message' => 'Некорректный формат email'],
            [['email'], 'unique', 'message' => 'Этот email уже занят'],

            [['phone'], 'string', 'max' => 150],
            [['phone'], 'match', 'pattern' => '/^[\d\s\-\+\(\)]+$/', 'message' => 'Телефон может содержать только цифры, пробелы и символы: + - ( )'],
            [['phone'], 'validatePhone'],
            
            [['password'], 'string', 'max' => 355],
            [['password'], 'string', 'min' => 6, 'on' => 'create'],
            
            
            [['access_token'], 'string', 'max' => 255],
            [['access_token'], 'unique'],
        

            ['role', 'in', 'range' => array_keys(self::optsRole())],
        ];
    }


   

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_user' => 'ID пользователя',
            'fio' => 'ФИО',
            'phone' => 'Телефон',
            'email' => 'Email',
            'role' => 'Роль',
            'password' => 'Пароль',
            'creat_user' => 'Дата создания',
            'access_token' => 'Токен доступа',
       
        ];
    }

    /**
     * Валидация телефона
     */
    public function validatePhone($attribute, $params)
    {
        if (!empty($this->$attribute)) {
            $phoneDigits = preg_replace('/[^\d]/', '', $this->$attribute);
            
            if (strlen($phoneDigits) < 10) {
                $this->addError($attribute, 'Телефон должен содержать минимум 10 цифр');
                return;
            }
            
            if (strlen($phoneDigits) > 15) {
                $this->addError($attribute, 'Телефон слишком длинный (максимум 15 цифр)');
                return;
            }
            
            // Более гибкая проверка формата
            $formats = [
                '/^8\(\d{3}\)\d{3}-\d{2}-\d{2}$/', 
                '/^\+\d\(\d{3}\)\d{3}-\d{2}-\d{2}$/', 
                '/^\d{10,15}$/' 
            ];
            
            $valid = false;
            foreach ($formats as $format) {
                if (preg_match($format, $this->$attribute)) {
                    $valid = true;
                    break;
                }
            }
            
            if (!$valid) {
                $this->addError($attribute, 'Телефон должен быть в формате: 8(XXX)XXX-XX-XX, +X(XXX)XXX-XX-XX или только цифры (10-15 цифр)');
            }
        }
    }

   
    public function setPassword($password)
    {
        $this->password = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

   
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

   
    public function generateAuthToken()
    {
        
        $token = Yii::$app->security->generateRandomString(32);
        $this->access_token = $token;
       
        return $token;
    }

  
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }


    public static function findByPhone($phone)
    {
        return static::findOne(['phone' => $phone]);
    }

   
    public static function findByToken($token)
    {
        return static::findOne([
            'access_token' => $token,
           
        ]);
    }

  
    public static function findIdentity($id)
    {
        return static::findOne(['id_user' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findByToken($token);
    }

    public function getId()
    {
        return $this->id_user;
    }

    public function getAuthKey()
    {
        return $this->access_token;
    }

    public function validateAuthKey($authKey)
    {
        return $this->access_token === $authKey;
    }

    /**
     * column role ENUM value labels
     * @return string[]
     */
    public static function optsRole()
    {
        return [
            self::ROLE_USER => 'user',
            self::ROLE_ADMIN => 'admin',
        ];
    }

    /**
     * @return string
     */
    public function displayRole()
    {
        return self::optsRole()[$this->role];
    }

    /**
     * @return bool
     */
    public function isRoleUser()
    {
        return $this->role === self::ROLE_USER;
    }

    public function setRoleToUser()
    {
        $this->role = self::ROLE_USER;
    }

    /**
     * @return bool
     */
    public function isRoleAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function setRoleToAdmin()
    {
        $this->role = self::ROLE_ADMIN;
    }

    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->creat_user = date('Y-m-d H:i:s');
                if (empty($this->role)) {
                    $this->setRoleToUser();
                }
            }
            return true;
        }
        return false;
    }
}