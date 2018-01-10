<?php
/**
 * Created by Error202
 * Date: 13.11.2017
 */

namespace app\modules\customers\forms;


use yii\base\Model;

class ImportFieldForm extends Model
{
    public $value;

    public function rules()
    {
        return array_filter([
            [['value'], 'integer'],
        ]);
    }
}