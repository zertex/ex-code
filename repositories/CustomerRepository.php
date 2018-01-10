<?php

namespace app\modules\customers\repositories;

use app\modules\customers\entities\Customer;
use app\repositories\NotFoundException;

class CustomerRepository
{
    public function get($id)
    {
        if (!$customer = Customer::findOne($id)) {
            throw new NotFoundException('Customer is not found.');
        }
        return $customer;
    }

    public function save(Customer $customer)
    {
        if (!$customer->save()) {
            throw new \RuntimeException('Saving error.');
        }
    }

    public function remove(Customer $customer)
    {
        if (!$customer->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }
}