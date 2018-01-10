<?php
/**
 * Created by Error202
 * Date: 13.11.2017
 */

namespace app\modules\customers\controllers;


use app\modules\customers\entities\CustomerBase;
use app\modules\customers\forms\ImportFieldForm;
use app\modules\customers\forms\ImportFileForm;
use app\modules\customers\services\ImportService;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\base\Model;
use yii\filters\AccessControl;
use Yii;

class ImportController extends Controller
{
    private $service;

    public function __construct($id, $module, ImportService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['import', 'fields'],
                        'allow' => true,
                        'roles' => ['ImportManagement'],
                    ],
                    [    // all the action are accessible to admin
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionImport()
    {
        $form = new ImportFileForm();

        if ($form->load(Yii::$app->request->post())) {
            $form->file = UploadedFile::getInstance($form, 'file');
            if ($form->validate()) {
                try {
                    $this->service->saveFile($form);
                    return $this->redirect(['fields', 'base_id'=>$form->base_id, 'ext' => $form->file->extension]);
                } catch (\DomainException $e) {
                    Yii::$app->errorHandler->logException($e);
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        }

        return $this->render('import', [
            'model' => $form,
            'bases' => CustomerBase::find()->all(),
        ]);
    }

    public function actionFields($base_id, $ext)
    {
        // excel titles
        $excelFile = Yii::getAlias('@runtime/import_customers.' . $ext);
        if (!file_exists($excelFile)) {
            return $this->redirect(['import']);
        }
        $headers = $this->service->import($ext);

        // customer fields
        $fields = [
            'name' => Yii::t('customers', 'ID Name'),
            'company' => Yii::t('customers', 'Company'),
            'contact_name' => Yii::t('customers', 'Name'),
            'contact_lastname' => Yii::t('customers', 'Last Name'),
            'email' => Yii::t('customers', 'E-mail'),
            'phone' => Yii::t('customers', 'Phone'),
            'country' => Yii::t('customers', 'Country'),
            'city' => Yii::t('customers', 'City'),
            'website' => Yii::t('customers', 'Web site'),
        ];

        $fieldForms = [];
        foreach ($fields as $key => $field)
        {
            $fieldForms[$key] = new ImportFieldForm();
        }

        if (Model::loadMultiple($fieldForms, Yii::$app->request->post()) && Model::validateMultiple($fieldForms)) {
            // saving
            $result = $this->service->insert($fieldForms, $ext, $base_id);
            //return 'Result... Good: ' . $result[0] . ', Bad: ' . $result[1];
            return $this->render('result', [
                'result' => $result,
            ]);
        }

        return $this->render('fields', [
            'headers' => $headers,
            'forms' => $fieldForms,
            'fields' => $fields,
        ]);
    }

}