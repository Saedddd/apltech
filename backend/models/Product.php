<?php

namespace app\models;

use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%products}}';
    }

    public function rules(): array
    {
        return [
            [['name', 'price'], 'required'],
            [['price', 'rrp_price', 'status'], 'integer'],
            [['name', 'category_name', 'brand_name'], 'string', 'max' => 255],


            [['name'], 'validName'], 
            [['price'], 'validPrice'],
            
        ];
    }

    public function validName($attribute, $params = null)
    {
        if (!empty($this->$attribute) && stripos($this->$attribute, "test") !== false) {
            $this->addError($attribute, "поле name не должно содержать слово 'test'");
        }
    }

    public function validPrice($attribute, $params = null)
    {
        if ($this->$attribute <= 0) {
           $this->addError($attribute, 'цена не может быть равна 0'); 
        }
        
    }


}

