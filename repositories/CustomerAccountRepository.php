<?php

namespace app\modules\customers\repositories;

use app\modules\customers\entities\CustomerAccount;
use app\repositories\NotFoundException;

class CustomerAccountRepository
{
    public function get($id)
    {
        if (!$customer = CustomerAccount::findOne($id)) {
            throw new NotFoundException('Customer account is not found.');
        }
        return $customer;
    }

    public function save(CustomerAccount $customerAccount)
    {
        if (!$customerAccount->save()) {
            throw new \RuntimeException('Saving error.');
        }
    }

    public function remove(CustomerAccount $customerAccount)
    {
        if (!$customerAccount->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }
}