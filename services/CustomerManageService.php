<?php

namespace app\modules\customers\services;

use app\modules\customers\entities\Customer;
use app\modules\customers\entities\CustomerBase;
use app\modules\customers\forms\CustomerBaseForm;
use app\modules\customers\forms\CustomerForm;
use app\modules\customers\repositories\CustomerAccountRepository;
use app\modules\customers\repositories\CustomerBaseRepository;
use app\modules\customers\repositories\CustomerRepository;
use app\modules\customers\services\CustomerBaseManageService;
use yii\web\UploadedFile;
use yii\base\Security;
use yii\helpers\Json;
use Yii;

class CustomerManageService
{
    private $customers;
    private $accounts;

    public function __construct(CustomerRepository $customers, CustomerAccountRepository $accounts)
    {
        $this->customers = $customers;
        $this->accounts = $accounts;
    }

    public function create(CustomerForm $form)
    {
        if ($form->files) {
            $files = [];
            foreach ($form->files as $file) {
                /* @var $file UploadedFile */
                $filename = $file->baseName . '_' . (new Security())->generateRandomString(5) . '.' . $file->extension;
                $path = \Yii::getAlias('@staticRoot') . '/files';
                if (!file_exists($path))
                {
                    mkdir($path, 0777, true);
                }
                $file->saveAs($path . '/' . $filename);
                $files[] = $filename;
            }
            $form->files = Json::encode($files);
        }
        else {
            $form->files = Json::encode([]);
        }

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

        // Select default base or created it if needed
        if (!$form->base_id)
        {
            $bases = CustomerBase::find()->all();
            if ($bases) {
                $form->base_id = $bases[0]->id;
            }
            else {
                $baseForm = new CustomerBaseForm();
                $baseForm->name = 'Default';
                $baseForm->index = 'default';
                $newBase = (new CustomerBaseManageService(new CustomerBaseRepository()))->create($baseForm);
                $form->base_id = $newBase->id;
            }
        }

        $customer = Customer::create(
            $form->base_id,
            $form->user_id && Yii::$app->request->isConsoleRequest ? $form->user_id : Yii::$app->user->id,
            $form->name,
            $form->company,
            $form->photo,
            $form->comment,
            $form->city,
            $form->country,
            $form->website,
            $form->files,
            $form->status
        );

        $this->customers->save($customer);
        return $customer;
    }

    public function edit($id, CustomerForm $form)
    {
        /* @var $customer \app\modules\customers\entities\Customer */
        $customer = $this->customers->get($id);

        $files = Json::decode($customer->files);
        if ($form->files) {
            foreach ($form->files as $file) {
                /* @var $file UploadedFile */
                $filename = $file->baseName . '_' . (new Security())->generateRandomString(5) . '.' . $file->extension;
                $file->saveAs(\Yii::getAlias('@staticRoot') . '/files/' . $filename);
                $files[] = $filename;
            }
        }
        $form->files = Json::encode($files);

        if ($form->photo) {
            $filename = $form->photo->baseName . '_' . (new Security())->generateRandomString(5) . '.' . $form->photo->extension;
            $form->photo->saveAs(\Yii::getAlias('@staticRoot') . '/photos/' . $filename);
            $form->photo = $filename;
        }
        else {
            $form->photo = $customer->photo;
        }

        $customer->edit(
            $form->base_id,
            $form->name,
            $form->company,
            $form->photo,
            $form->comment,
            $form->city,
            $form->country,
            $form->website,
            $form->files,
            $form->status
        );
        $this->customers->save($customer);
    }

    public function remove($id)
    {
        /* @var $customer Customer */
        $customer = $this->customers->get($id);
        $this->removeFiles($id);
        $this->customers->remove($customer);
    }

    public function unSubscribeEmail($id)
    {
	    /* @var $account \app\modules\customers\entities\CustomerAccount */
	    $account = $this->accounts->get($id);
        $account->unsub_email = Customer::UN_SUB_EMAIL;
	    $this->accounts->save($account);
    }

    public function unSubscribeSms($id)
    {
        /* @var $account \app\modules\customers\entities\CustomerAccount */
        $account = $this->accounts->get($id);
        $account->unsub_sms = Customer::UN_SUB_SMS;
        $this->accounts->save($account);
    }

    public function removePhoto($id, $file)
    {
        /* @var $customer \app\modules\customers\entities\Customer */
        $customer = $this->customers->get($id);

        if (file_exists(\Yii::getAlias('@staticRoot/photos/' . $file)))
        {
            unlink(\Yii::getAlias('@staticRoot/photos/' . $file));
        }
        $customer->photo = '';
        $this->customers->save($customer);
    }

    public function removeFile($id, $file)
    {
        /* @var $customer Customer */
        $customer = $this->customers->get($id);
        $files = Json::decode($customer->files);
        if (in_array($file, $files))
        {
            $files = array_diff($files, [$file]);
            if (file_exists(\Yii::getAlias('@staticRoot/files/' . $file)))
            {
                unlink(\Yii::getAlias('@staticRoot/files/' . $file));
            }
        }
        $customer->files = Json::encode($files);
        $this->customers->save($customer);
    }

    public function removeFiles($id)
    {
        /* @var $customer \app\modules\customers\entities\Customer */
        $customer = $this->customers->get($id);
        $files = Json::decode($customer->files);
        foreach ($files as $file)
        {
            if (file_exists(\Yii::getAlias('@staticRoot/files/' . $file)))
            {
                unlink(\Yii::getAlias('@staticRoot/files/' . $file));
            }
        }
        $customer->files = Json::encode([]);
        $this->customers->save($customer);
    }

    public function toggleStatus($id)
    {
        /* @var $customer Customer */
        $customer = $this->customers->get($id);
        $customer->status = $customer->status == Customer::STATUS_ACTIVE ? Customer::STATUS_DRAFT : Customer::STATUS_ACTIVE;
        $this->customers->save($customer);
        return $customer->status;
    }
}