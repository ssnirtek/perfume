<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $id_post
 * @property int $user_id
 * @property int $category_id
 * @property int $fragrances_id
 * @property int $raiting
 * @property string $text
 * @property string $creat_post
 * 
 * @property Fragrances $fragrance
 * @property Category $category
 * @property User $user
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fragrances_id', 'raiting', 'text'], 'required'],
            [['user_id', 'category_id', 'fragrances_id', 'raiting'], 'integer'],
            [['raiting'], 'integer', 'min' => 1, 'max' => 5],
            [['creat_post'], 'safe'],
            [['text'], 'string', 'max' => 500],
            [['fragrances_id'], 'exist', 'skipOnError' => true, 
            
                'targetClass' => Fragrances::class, 'targetAttribute' => ['fragrances_id' => 'id_fragrances']],
            [['category_id'], 'exist', 'skipOnError' => true, 
                'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id_category']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_post' => 'Id Post',
            'user_id' => 'User ID',
            'category_id' => 'Category ID',
            'fragrances_id' => 'Fragrances ID',
            'raiting' => 'Raiting',
            'text' => 'Text',
            'creat_post' => 'Creat Post',
        ];
    }
    
    /**
     * Gets query for [[Fragrance]].
     */
    public function getFragrance()
    {
        return $this->hasOne(Fragrances::class, ['id_fragrances' => 'fragrances_id']);
    }
    
    /**
     * Gets query for [[Category]].
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id_category' => 'category_id']);
    }
    
    /**
     * Gets query for [[User]].
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id_user' => 'user_id']);
    }
    
    /**
     * Автоматическая установка даты создания
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->creat_post = date('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }
}