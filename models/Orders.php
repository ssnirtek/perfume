<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id_orders
 * @property int $user_id
 * @property int $fragrances_id
 * @property int $adress
 * @property string $status
 * @property string $pay_metod
 * @property string $comment
 */
class Orders extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_CONFIRMED = 'Confirmed';
    const STATUS_ASSEMBLY = 'Assembly';
    const STATUS_SENT = 'Sent';
    const STATUS_RECEIVED = 'Received';
    const PAY_METOD_CARD = 'Card';
    const PAY_METOD_CASH = 'Cash';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'fragrances_id', 'adress', 'status', 'pay_metod'], 'required'],
            [['user_id', 'fragrances_id'], 'integer'],
            [['status', 'pay_metod', 'adress'], 'string'],
            [['comment'], 'string', 'max' => 500], 
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            ['pay_metod', 'in', 'range' => array_keys(self::optsPayMetod())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
       return [
            'id_orders' => 'ID',
            'user_id' => 'Пользователь',
            'fragrances_id' => 'Аромат',
            'adress' => 'Адрес',
            'status' => 'Статус',
            'pay_metod' => 'Способ оплаты',
            'comment' => 'Комментарий',
        ];
    }



      /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFragrance()
    {
        return $this->hasOne(Fragrances::class, ['id_fragrances' => 'fragrances_id']);
    }

    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
      return [
            self::STATUS_CONFIRMED => 'Подтвержден',
            self::STATUS_ASSEMBLY => 'Сборка',
            self::STATUS_SENT => 'Отправлен',
            self::STATUS_RECEIVED => 'Получен',
        ];
    }

    /**
     * column pay_metod ENUM value labels
     * @return string[]
     */
    public static function optsPayMetod()
    {
        return [
            self::PAY_METOD_CARD => 'Карта',
            self::PAY_METOD_CASH => 'Наличные',
        ];
    }


 /**
     * @return string
     */
    public function getStatusLabel()
    {
        return self::optsStatus()[$this->status] ?? $this->status;
    }

    /**
     * @return string
     */
    public function getPayMethodLabel()
    {
        return self::optsPayMetod()[$this->pay_metod] ?? $this->pay_metod;
    }

    /**
     * @return bool
     */
    public function isStatusConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function setStatusToConfirmed()
    {
        $this->status = self::STATUS_CONFIRMED;
    }

    /**
     * @return bool
     */
    public function isStatusAssembly()
    {
        return $this->status === self::STATUS_ASSEMBLY;
    }

    public function setStatusToAssembly()
    {
        $this->status = self::STATUS_ASSEMBLY;
    }

    /**
     * @return bool
     */
    public function isStatusSent()
    {
        return $this->status === self::STATUS_SENT;
    }

    public function setStatusToSent()
    {
        $this->status = self::STATUS_SENT;
    }

    /**
     * @return bool
     */
    public function isStatusReceived()
    {
        return $this->status === self::STATUS_RECEIVED;
    }

    public function setStatusToReceived()
    {
        $this->status = self::STATUS_RECEIVED;
    }

    /**
     * @return bool
     */
    public function isPayMetodCard()
    {
        return $this->pay_metod === self::PAY_METOD_CARD;
    }

    public function setPayMetodToCard()
    {
        $this->pay_metod = self::PAY_METOD_CARD;
    }

    /**
     * @return bool
     */
    public function isPayMetodCash()
    {
        return $this->pay_metod === self::PAY_METOD_CASH;
    }

    public function setPayMetodToCash()
    {
        $this->pay_metod = self::PAY_METOD_CASH;
    }
}
