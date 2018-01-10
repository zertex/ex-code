<?php
/**
 * Created by Error202
 * Date: 08.11.2017
 */

namespace app\modules\customers\entities;

use yii\db\ActiveRecord;
use Yii;

/**
 * @property integer $id
 * @property string $name
 */
class CustomerBase extends ActiveRecord
{
    public static function create($name)
    {
        $customerBase = new CustomerBase();
        $customerBase->name = $name;
        return $customerBase;
    }

    public function edit($name)
    {
        $this->name = $name;
    }

    public static function tableName()
    {
        return '{{%customer_bases}}';
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('customers', 'Title'),
        ];
    }
}