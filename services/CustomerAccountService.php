<?php

namespace app\modules\customers\services;

use app\modules\customers\entities\Customer;
use app\modules\customers\entities\CustomerBase;
use app\modules\customers\forms\CustomerBaseForm;
use app\modules\customers\forms\CustomerForm;
use app\modules\customers\forms\CustomerAccountForm;
use app\modules\customers\entities\CustomerAccount;
use app\modules\customers\repositories\CustomerAccountRepository;
use yii\web\UploadedFile;
use yii\base\Security;
use yii\helpers\Json;
use Yii;

class CustomerAccountService
{
    private $customerAccounts;

    public function __construct(CustomerAccountRepository $customerAccounts)
    {
        $this->customerAccounts = $customerAccounts;
    }

    public function create(CustomerAccountForm $form)
    {
        if ($form->photo) {
            $filename = $form->photo->baseName . '_' . (new Security())->generateRandomString(5) . '.' . $form->photo->extension;
            $path = \Yii::getAlias('@staticRoot') . '/photos';
            if (!file_exists($path))
            {
                mkdir($path, 0777, true);
            }
            $form->photo->saveAs($path . '/' . $filename);
            $form->photo = $filename;
        }

        $account = CustomerAccount::create(
            $form->name,
            $form->lastname,
            $form->position,
            $form->phone,
            $form->email,
            $form->photo,
            $form->title,
            $form->sub_email,
            $form->sub_sms,
            $form->sex,
            $form->birth_date,
            $form->comment,
            $form->customer_id
        );

        $this->customerAccounts->save($account);
        return $account;
    }

    public function edit($id, CustomerAccountForm $form)
    {
        /* @var $account CustomerAccount */
        $account = $this->customerAccounts->get($id);

        if ($form->photo) {
            $filename = $form->photo->baseName . '_' . (new Security())->generateRandomString(5) . '.' . $form->photo->extension;
            $form->photo->saveAs(\Yii::getAlias('@staticRoot') . '/photos/' . $filename);
            $form->photo = $filename;
        }
        else {
            $form->photo = $account->photo;
        }

        $account->edit(
	        $form->name,
	        $form->lastname,
	        $form->position,
	        $form->phone,
	        $form->email,
	        $form->photo,
	        $form->title,
	        $form->sub_email,
	        $form->sub_sms,
	        $form->sex,
	        $form->birth_date,
	        $form->comment,
	        $form->customer_id
        );
        $this->customerAccounts->save($account);
    }

    public function remove($id)
    {
        /* @var $account CustomerAccount */
        $account = $this->customerAccounts->get($id);
        if ($account->photo) {
	        $this->removePhoto( $account->id, $account->photo );
        }
        $this->customerAccounts->remove($account);
    }

    public function removePhoto($id, $file)
    {
        /* @var $account CustomerAccount */
        $account = $this->customerAccounts->get($id);

        if (file_exists(\Yii::getAlias('@staticRoot/photos/' . $file)))
        {
            unlink(\Yii::getAlias('@staticRoot/photos/' . $file));
        }
        $account->photo = '';
        $this->customerAccounts->save($account);
    }
}