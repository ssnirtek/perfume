<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fragrances".
 *
 * @property int $id_fragrances
 * @property string $product_name
 * @property int $category_id
 * @property string $description
 * @property float $price
 * @property int $volume_ml
 * @property string $gender
 * @property int $raiting_f
 * @property string $image_f
 */
class Fragrances extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const GENDER_FEMALE = 'Female';
    const GENDER_MALE = 'Male';
    const GENDER_UNISEX = 'Unisex';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fragrances';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_name', 'category_id', 'description', 'price', 'volume_ml', 'gender', 'raiting_f', 'image_f'], 'required'],
            [['category_id', 'volume_ml', 'raiting_f'], 'integer'],
            [['price'], 'number'],
            [['gender'], 'string'],
            [['product_name'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 355],
            [['image_f'], 'string', 'max' => 500],
            ['gender', 'in', 'range' => array_keys(self::optsGender())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_fragrances' => 'Id Fragrances',
            'product_name' => 'Product Name',
            'category_id' => 'Category ID',
            'description' => 'Description',
            'price' => 'Price',
            'volume_ml' => 'Volume Ml',
            'gender' => 'Gender',
            'raiting_f' => 'Raiting F',
            'image_f' => 'Image F',
        ];
    }


    /**
     * column gender ENUM value labels
     * @return string[]
     */
    public static function optsGender()
    {
        return [
            self::GENDER_FEMALE => 'Female',
            self::GENDER_MALE => 'Male',
            self::GENDER_UNISEX => 'Unisex',
        ];
    }

    /**
     * @return string
     */
    public function displayGender()
    {
        return self::optsGender()[$this->gender];
    }

    /**
     * @return bool
     */
    public function isGenderFemale()
    {
        return $this->gender === self::GENDER_FEMALE;
    }

    public function setGenderToFemale()
    {
        $this->gender = self::GENDER_FEMALE;
    }

    /**
     * @return bool
     */
    public function isGenderMale()
    {
        return $this->gender === self::GENDER_MALE;
    }

    public function setGenderToMale()
    {
        $this->gender = self::GENDER_MALE;
    }

    /**
     * @return bool
     */
    public function isGenderUnisex()
    {
        return $this->gender === self::GENDER_UNISEX;
    }

    public function setGenderToUnisex()
    {
        $this->gender = self::GENDER_UNISEX;
    }
}
