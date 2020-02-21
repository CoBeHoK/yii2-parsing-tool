<?php

namespace app\models;

class Coupon extends \yii\db\ActiveRecord
{
    public $id;

    public static function tableName()
    {
        return 'coupons';
    }
}
