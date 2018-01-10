<?php
/**
 * Created by Error202
 * Date: 23.11.2017
 */

namespace app\modules\customers\forms;


use yii\base\Model;

class ChangeGroupForm extends Model
{
    public $base_id;
    public $ids;

    public function rules()
    {
        return [
            [['base_id', 'ids'], 'required'],
            ['base_id', 'integer'],
            ['ids', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'base_id' => \Yii::t('customers', 'Customer Base'),
        ];
    }
}