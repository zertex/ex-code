<?php

namespace app\modules\customers\controllers;

use app\modules\customers\entities\CustomerBase;
use app\modules\customers\forms\CustomerBaseForm;
use app\modules\customers\forms\search\CustomerBaseSearch;
use app\modules\customers\services\CustomerBaseManageService;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;

class CustomerBaseController extends Controller
{
    private $service;

    public function __construct($id, $module, CustomerBaseManageService $service, $config = [])
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
                        'actions' => ['create','view','index', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['CustomerBaseManagement'],
                    ],
                    [    // all the action are accessible to admin
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerBaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

	    if (!isset($_GET['per-page'])) {
		    $dataProvider->pagination->pageSize = \app\helpers\UserHelper::getSetting( 'perPage', 50 );
	    }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	/**
	 * @param $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
    public function actionView($id)
    {
        $customerBase = $this->findModel($id);

        return $this->render('view', [
            'customerBase' => $customerBase,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate()
    {
        $form = new CustomerBaseForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $customerBase = $this->service->create($form);
                return $this->redirect(['view', 'id' => $customerBase->id]);
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $form,
        ]);
    }

	/**
	 * @param $id
	 *
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException
	 */
    public function actionUpdate($id)
    {
        /* @var $customerBase CustomerBase */
        $customerBase = $this->findModel($id);

        $form = new CustomerBaseForm($customerBase);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($customerBase->id, $form);
                return $this->redirect(['view', 'id' => $customerBase->id]);
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $form,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->service->remove($id);
        } catch (\DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return CustomerBase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerBase::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
