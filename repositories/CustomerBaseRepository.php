<?php

namespace app\modules\customers\repositories;

use app\modules\customers\entities\CustomerBase;
use app\repositories\NotFoundException;

class CustomerBaseRepository
{
    public function get($id)
    {
        if (!$customerBase = CustomerBase::findOne($id)) {
            throw new NotFoundException('Customer base is not found.');
        }
        return $customerBase;
    }

    public function save(CustomerBase $customerBase)
    {
        if (!$customerBase->save()) {
            throw new \RuntimeException('Saving error.');
        }
    }

    public function remove(CustomerBase $customerBase)
    {
        if (!$customerBase->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }
}