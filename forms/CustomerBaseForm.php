<?php

namespace app\modules\customers\forms;

use app\modules\customers\entities\CustomerBase;
use yii\base\Model;
use Yii;

class CustomerBaseForm extends Model
{
    public $id;
    public $name;

    private $_customerBase;

    public function __construct(CustomerBase $customerBase = null, $config = [])
    {
        if ($customerBase) {
            $this->id = $customerBase->id;
            $this->name = $customerBase->name;
            $this->_customerBase = $customerBase;
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 32],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('customers', 'Title'),
        ];
    }
}