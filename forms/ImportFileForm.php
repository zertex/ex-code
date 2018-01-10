<?php

namespace app\modules\customers\forms;

use yii\base\Model;
use Yii;

class ImportFileForm extends Model
{
    public $file;
    public $base_id;

    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => 'xls, xlsx, csv'],
            [['base_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => Yii::t('customers', 'Excel file'),
            'base_id' => Yii::t('customers', 'Customer Base'),
        ];
    }
}