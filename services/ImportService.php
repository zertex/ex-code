<?php
/**
 * Created by Error202
 * Date: 13.11.2017
 */

namespace app\modules\customers\services;


use app\helpers\PhoneHelper;
use app\modules\customers\entities\Customer;
use app\modules\customers\forms\CustomerAccountForm;
use app\modules\customers\forms\CustomerForm;
use app\modules\customers\forms\ImportFieldForm;
use app\modules\customers\forms\ImportFileForm;
use app\modules\customers\repositories\CustomerAccountRepository;
use app\modules\customers\repositories\CustomerRepository;

class ImportService
{
    private $_errors;

    public function saveFile(ImportFileForm $form)
    {
        $excelFile = \Yii::getAlias('@runtime/import_customers.' . $form->file->extension);
        $form->file->saveAs($excelFile);
    }

    public function import($ext)
    {
        $excelFile = \Yii::getAlias('@runtime/import_customers.' . $ext);
        $objPHPExcel = \PHPExcel_IOFactory::load($excelFile);
        $firstRowCells = $objPHPExcel->getActiveSheet()->rangeToArray('A1:' . $objPHPExcel->getActiveSheet()->getHighestColumn() . '1');
        return $firstRowCells[0];
    }

    public function insert($fieldForms, $ext, $base_id)
    {
        $bad = 0;
        $good = 0;
        $excelFile = \Yii::getAlias('@runtime/import_customers.' . $ext);
        $objPHPExcel = \PHPExcel_IOFactory::load($excelFile);
        // count rows
        $count = $objPHPExcel->getActiveSheet()->getHighestRow();
        for ($i = 1; $i <= $count; $i++)
        {
            // skip header line
            if ($i == 1) {
                continue;
            }
            $row = $objPHPExcel->getActiveSheet()->rangeToArray('A'.$i.':' . $objPHPExcel->getActiveSheet()->getHighestColumn() . $i);
            if ($this->_insert($fieldForms, $row[0], $base_id)) {
                $good++;
            }
            else {
                $bad++;
            }
        }
        if (file_exists($excelFile)) {
            unlink($excelFile);
        }
        return [$good, $bad, $this->_errors];
    }

    private function _insert($fieldForms, $row, $base_id)
    {
        $customerForm = new CustomerForm();
        $accountForm = new CustomerAccountForm();
        foreach ($fieldForms as $index => $fieldForm)
        {
            /* @var $fieldForm ImportFieldForm */
            if ($fieldForm->value === '')
            {
                continue;
            }
            if ($index == 'phone')
            {
                if (PhoneHelper::isCorrect($row[$fieldForm->value])) {
                    $row[$fieldForm->value] = PhoneHelper::beautyPhone(PhoneHelper::normalizePhone($row[$fieldForm->value]));
                }
                else {
                    $row[$fieldForm->value] = '';
                }
            }
            if ($index == 'email' || $index == 'phone' || $index == 'contact_name' || $index == 'contact_lastname') {
            	if ($index == 'contact_name') {
            		$index = 'name';
	            }
	            if ($index == 'contact_lastname') {
		            $index = 'lastname';
	            }
            	$accountForm->$index = $row[ $fieldForm->value ];
            }
            else {
	            $customerForm->$index = $row[ $fieldForm->value ];
            }
        }
        $customerForm->status = Customer::STATUS_ACTIVE;
        $customerForm->base_id = $base_id;
        try {
            if ($customerForm->validate()) {
                $customer = (new CustomerManageService(new CustomerRepository()))->create($customerForm);
                $accountForm->customer_id = $customer->id;
                if ($accountForm->validate()) {
	                ( new CustomerAccountService( new CustomerAccountRepository() ) )->create( $accountForm );
                }
                return true;
            }
            else {
                $this->_errors[] = [
                    'record' => $row,
                    'message' => implode('; ', $row) . "\n" . implode("\n", array_map(function ($item) {
                        return 'Error: ' . implode(',', $item);
                    }, $customerForm->errors)) . "\n\n",
                ];
                return false;
            }
        }
        catch (\DomainException $e)
        {
            $this->_errors[] = [
                'record' => $row,
                'message' => $e->getMessage(),
            ];
            return false;
        }
    }
}