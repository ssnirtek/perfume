<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id_category
 * @property string $category_name
 */
class Category extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        
        [['fio', 'email', 'password', 'role'], 'required'],
        
        ['email', 'email'],
        ['email', 'string', 'max' => 255],
        ['email', 'unique', 'targetClass' => self::class, 'message' => 'Этот email уже занят.'],
        
        ['fio', 'string', 'max' => 255],
        ['fio', 'match', 'pattern' => '/^[а-яА-ЯёЁ\s\-]+$/u', 'message' => 'ФИО может содержать только русские буквы, пробелы и дефисы.'],

        ['phone', 'string', 'max' => 150],
        ['phone', 'match', 'pattern' => '/^[\d\s\-\+\(\)]+$/', 'message' => 'Телефон может содержать только цифры, пробелы, +, - и скобки.'],
        
        ['password', 'string', 'min' => 6, 'max' => 255],
        ['password', 'match', 'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', 'message' => 'Пароль должен содержать хотя бы одну заглавную букву, одну строчную букву и одну цифру.'],
      
        ['role', 'in', 'range' => ['user', 'admin']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_category' => 'Id Category',
            'category_name' => 'Category Name',
        ];
    }

}
