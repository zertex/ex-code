<?php
/**
 * Created by Error202
 * Date: 18.11.2017
 */

namespace app\modules\customers\controllers;


use app\modules\customers\entities\Customer;
use app\modules\customers\entities\CustomerAccount;
use app\modules\customers\entities\CustomerBase;
use app\modules\customers\helpers\CustomerHelper;
use yii\web\Controller;
use PHPExcel;
use PHPExcel_Writer_Excel2007;
use yii\filters\AccessControl;
use Yii;

class ExportController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['export'],
                        'allow' => true,
                        'roles' => ['ExportManagement'],
                    ],
                    [    // all the action are accessible to admin
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionExport($base_id = null)
    {
        if ($base_id) {
            $customers = Customer::find()->where(['base_id' => $base_id])->all();
            $baseName = CustomerBase::findOne($base_id)->name;
        }
        else {
            $customers = Customer::find()->all();
            $baseName = '';
        }

        if ($customers) {
            $objPHPExcel = new PHPExcel();
	        $objPHPExcel->createSheet(1);
            $objPHPExcel->setActiveSheetIndex(0);
            $rowCount = 1;
            $rowAccountCount = 1;

	        $objPHPExcel->getActiveSheet()->setTitle(Yii::t('customers', 'Customers'));
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, '#');
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, Yii::t('customers', 'ID Name'));
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, Yii::t('customers', 'Company'));
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, Yii::t('customers', 'Country'));
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, Yii::t('customers', 'City'));
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, Yii::t('customers', 'Web site'));
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, Yii::t('customers', 'Customer Base'));
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, Yii::t('customers', 'Comment'));
            $rowCount++;

            // Prepare second sheet for contacts
	        $objPHPExcel->setActiveSheetIndex(1);
	        $objPHPExcel->getActiveSheet()->setTitle(Yii::t('customers', 'Contacts'));
	        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowAccountCount, '#');
	        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowAccountCount, Yii::t('customers', 'Customer'));
	        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowAccountCount, Yii::t('customers', 'Company'));
	        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowAccountCount, Yii::t('customers', 'Name'));
	        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowAccountCount, Yii::t('customers', 'Last Name'));
	        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowAccountCount, Yii::t('customers', 'Position'));
	        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowAccountCount, Yii::t('customers', 'Phone'));
	        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowAccountCount, Yii::t('customers', 'E-mail'));
	        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowAccountCount, Yii::t('customers', 'Sex'));
	        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowAccountCount, Yii::t('customers', 'Birth Date'));
	        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowAccountCount, Yii::t('customers', 'Comment'));
	        $rowAccountCount++;

	        $objPHPExcel->setActiveSheetIndex(0);

            foreach ($customers as $customer)
            {
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $customer->id);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $customer->name);
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $customer->company);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $customer->country);
                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $customer->city);
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $customer->website);
                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $baseName);
                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $customer->comment);
                $rowCount++;

				// Filling contacts
	            $objPHPExcel->setActiveSheetIndex(1);
                foreach ($customer->accounts as $account) {
	                $objPHPExcel->setActiveSheetIndex(1);
	                /* @var $account CustomerAccount */
	                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowAccountCount, $account->id);
	                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowAccountCount, $customer->name);
	                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowAccountCount, $customer->company);
	                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowAccountCount, $account->name);
	                $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowAccountCount, $account->lastname);
	                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowAccountCount, $account->position);
	                $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowAccountCount, $account->phone);
	                $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowAccountCount, $account->email);
	                $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowAccountCount, CustomerHelper::sexName($account->sex));
	                $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowAccountCount, $account->birth_date ? date('d.m.Y', $account->birth_date) : '');
	                $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowAccountCount, $account->comment);
	                $rowAccountCount++;
                }
	            $objPHPExcel->setActiveSheetIndex(0);
            }

            foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($col)
                    ->setAutoSize(true);
            }

	        $objPHPExcel->setActiveSheetIndex(1);
	        foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
		        $objPHPExcel->getActiveSheet()
		                    ->getColumnDimension($col)
		                    ->setAutoSize(true);
	        }
	        $objPHPExcel->setActiveSheetIndex(0);

            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="export_' . $baseName . '.xlsx"');
	        $objWriter->save(Yii::getAlias('@runtime/export_' . $baseName . '.xlsx'));
            //$objWriter->save('php://output');
	        readfile(Yii::getAlias('@runtime/export_' . $baseName . '.xlsx'));
        }
        else {
            throw new \DomainException('Customers not found.');
        }
    }
}