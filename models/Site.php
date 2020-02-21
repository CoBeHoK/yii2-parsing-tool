<?php

namespace app\models;

class Site extends \yii\db\ActiveRecord
{
    public $id;

    public static function tableName()
    {
        return 'sites';
    }
}
