<?php

namespace app\modules\customers\services;

use app\modules\customers\entities\CustomerBase;
use app\modules\customers\forms\CustomerBaseForm;
use app\modules\customers\repositories\CustomerBaseRepository;

class CustomerBaseManageService
{
    private $customerBases;

    public function __construct(CustomerBaseRepository $customersBases)
    {
        $this->customerBases = $customersBases;
    }

    public function create(CustomerBaseForm $form)
    {
        $customerBase = CustomerBase::create(
            $form->name
        );
        $this->customerBases->save($customerBase);
        return $customerBase;
    }

    public function edit($id, CustomerBaseForm $form)
    {
        /* @var $customerBase CustomerBase */
        $customerBase = $this->customerBases->get($id);

        $customerBase->edit(
            $form->name
        );
        $this->customerBases->save($customerBase);
    }

    public function remove($id)
    {
        /* @var $customerBase \app\modules\customers\entities\CustomerBase */
        $customerBase = $this->customerBases->get($id);

        $baseCount = CustomerBase::find()->count();

        if ($baseCount == 1) {
            throw new \DomainException(\Yii::t('customers', 'You can not delete the last customers base'));
        }

        $this->customerBases->remove($customerBase);
    }
}