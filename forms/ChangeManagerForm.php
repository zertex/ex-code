<?php
/**
 * Created by Error202
 * Date: 23.11.2017
 */

namespace app\modules\customers\forms;


use yii\base\Model;

class ChangeManagerForm extends Model
{
    public $user_id;
    public $ids;
    public $base_id;

    public function rules()
    {
        return [
            [['user_id', 'ids'], 'required'],
            [['user_id', 'base_id'], 'integer'],
            ['ids', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => \Yii::t('customers', 'Manager'),
        ];
    }
}