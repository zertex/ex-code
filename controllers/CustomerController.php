<?php

namespace app\modules\customers\controllers;

use app\entities\User;
use app\modules\customers\entities\CustomerAccount;
use app\modules\customers\entities\CustomerBase;
use app\modules\customers\forms\ChangeGroupForm;
use app\modules\customers\forms\ChangeManagerForm;
use app\modules\customers\forms\CustomerAccountForm;
use app\modules\customers\forms\CustomerForm;
use app\modules\customers\helpers\CustomerHelper;
use app\modules\customers\services\CustomerAccountService;
use app\modules\customers\services\CustomerManageService;
use app\modules\deals\forms\search\DealCustomerSearch;
use Yii;
use app\modules\customers\entities\Customer;
use app\modules\customers\forms\search\CustomerSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class CustomerController extends Controller
{
    private $service;
    private $accounts;

    public function __construct($id, $module, CustomerManageService $service, CustomerAccountService $accounts, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->accounts = $accounts;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                        	'create','view','index', 'update', 'delete',
	                        'delete-file', 'toggle-status', 'delete-selected', 'change-base',
	                        'create-contact', 'update-contact', 'delete-contact', 'share-contact',
	                        'contact-info'],
                        'allow' => true,
                        'roles' => ['CustomerManagement'],
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
	                'delete-contact' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex($base_id = null)
    {
        $searchModel = new CustomerSearch();
        if ($base_id)
        {
            $searchModel->base_id = $base_id;
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (!isset($_GET['per-page'])) {
	        $dataProvider->pagination->pageSize = \app\helpers\UserHelper::getSetting( 'perPage', 50 );
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'base_id' => $base_id,
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
        $searchModel = new DealCustomerSearch();
        $searchModel->setCustomer($id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$customer = $this->findModel($id);

        return $this->render('view', [
            'customer' => $customer,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
	        'accounts' => $customer->accounts,
        ]);
    }

    /**
     * @return mixed
     */
    public function actionCreate($base_id = null)
    {
        $form = new CustomerForm();

        if ($base_id)
        {
            $form->base_id = $base_id;
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
	        $customer = $this->service->create( $form );
	        $form->account->customer_id = $customer->id;
	        if ($form->account->load(Yii::$app->request->post()) && $form->account->validate()) {
		        try {
			        $account = $this->accounts->create( $form->account );
			        return $this->redirect( [ 'view', 'id' => $customer->id ] );
		        } catch ( \DomainException $e ) {
			        Yii::$app->errorHandler->logException( $e );
			        Yii::$app->session->setFlash( 'error', $e->getMessage() );
		        }
	        }
	        else {
	        	print_r($form->account->getErrors()); die;
	        }
        }

        $customerBases = CustomerBase::find()->all();

        return $this->render('create', [
            'model' => $form,
            'customerBases' => $customerBases,
        ]);
    }

	/**
	 * @param $id
	 *
	 * @return string|Response
	 * @throws NotFoundHttpException
	 */
    public function actionUpdate($id)
    {
        /* @var $customer \app\modules\customers\entities\Customer */
        $customer = $this->findModel($id);

        $form = new CustomerForm($customer);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($customer->id, $form);
                return $this->redirect(['view', 'id' => $customer->id]);
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        $customerBases = CustomerBase::find()->all();

        return $this->render('update', [
            'model' => $form,
            'customerBases' => $customerBases,
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

    public function actionChangeManager()
    {
        $selected = Yii::$app->request->post('gridSelection');
        $base_id = Yii::$app->request->post('base_id');
        $form = new ChangeManagerForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $ids = Json::decode($form->ids, true);
            Customer::updateAll(['user_id' => $form->user_id], ['IN', 'id', $ids]);
            Yii::$app->session->setFlash('success', Yii::t('customers', 'Customers information updated.'));
            return $this->redirect(['index', 'base_id' => $form->base_id]);
        }
        else {
            $form->ids = Json::encode($selected);
            $form->base_id = $base_id;
        }

        return $this->render('change-manager', [
            'model' => $form,
            'users' => ArrayHelper::map(User::find()->all(), 'id', 'username'),
            'count' => count($selected),
        ]);
    }

    public function actionChangeBase()
    {
        $selected = Yii::$app->request->post('gridSelection');
        $form = new ChangeGroupForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $ids = Json::decode($form->ids, true);
            Customer::updateAll(['base_id' => $form->base_id], ['IN', 'id', $ids]);
            Yii::$app->session->setFlash('success', Yii::t('customers', 'Customers information updated.'));
            return $this->redirect(['index', 'base_id' => $form->base_id]);
        }
        else {
            $form->ids = Json::encode($selected);
        }

        return $this->render('change-base', [
            'model' => $form,
            'bases' => ArrayHelper::map(CustomerBase::find()->all(), 'id', 'name'),
            'count' => count($selected),
        ]);
    }

    public function actionDeleteSelected()
    {
        $post = Yii::$app->request->post();
        $ids = isset($post['ids']) ? $post['ids'] : null;

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$ids) {
            return [
                'result' => 'error',
            ];
        }

        $idsArray = Json::decode($ids, true);

        foreach ($idsArray as $id)
        {
            try {
                $this->service->remove($id);
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
                return [
                    'result' => 'failed',
                ];
            }
        }
        Yii::$app->session->setFlash('success', Yii::t('customers', 'Selected customers deleted'));
        return [
            'result' => 'success',
        ];
    }

    public function actionDeleteFile()
    {
        $id = (int)\Yii::$app->request->post('id');
        $file = \Yii::$app->request->post('file');
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $this->service->removeFile($id, $file);
            return [
                'result' => 'success',
            ];
        } catch (\DomainException $e) {
            Yii::$app->errorHandler->logException($e);
        }
        return [
            'result' => 'failed'
        ];
    }

    public function actionToggleStatus($id)
    {
        $this->layout = false;
        if (Yii::$app->request->isAjax)
        {
            $status = $this->service->toggleStatus($id);
            return CustomerHelper::statusLabel($status);
        }
        return '';
    }

	public function actionCreateContact($cid = null)
	{
		$form = new CustomerAccountForm();
		$customer = $this->findModel($cid);
		$form->customer_id = $customer->id;
		$form->sub_email = 1;
		$form->sub_sms = 1;

		if ($form->load(Yii::$app->request->post()) && $form->validate()) {
			try {
				$account = $this->accounts->create($form);
				return $this->redirect( [ 'view', 'id' => $customer->id ] );
			} catch ( \DomainException $e ) {
				Yii::$app->errorHandler->logException( $e );
				Yii::$app->session->setFlash( 'error', $e->getMessage() );
			}
		}
		return $this->render('create-contact', [
			'model' => $form,
		]);
	}

	public function actionUpdateContact($id)
	{
		/* @var $account \app\modules\customers\entities\CustomerAccount */
		$account = $this->findAccountModel($id);
		$form = new CustomerAccountForm($account);
		if ($form->birth_date) {
			$form->birth_date = date('d.m.Y', $form->birth_date);
		}

		if ($form->load(Yii::$app->request->post()) && $form->validate()) {
			try {
				$this->accounts->edit($account->id, $form);
				return $this->redirect(['view', 'id' => $account->customer_id]);
			} catch (\DomainException $e) {
				Yii::$app->errorHandler->logException($e);
				Yii::$app->session->setFlash('error', $e->getMessage());
			}
		}
		return $this->render('update-contact', [
			'model' => $form,
		]);
	}

	public function actionDeleteContact($id)
	{
		$account = $this->findAccountModel($id);
		try {
			$this->accounts->remove($account->id);
		} catch (\DomainException $e) {
			Yii::$app->errorHandler->logException($e);
			Yii::$app->session->setFlash('error', $e->getMessage());
		}
		return $this->redirect(['view', 'id' => $account->customer_id]);
	}

	public function actionContactInfo($id) {
    	$account = $this->findAccountModel($id);
    	//$this->layout = false;
    	return $this->renderAjax('contact-info', [
    		'account' => $account,
	    ]);
	}

    /**
     * @param integer $id
     * @return \app\modules\customers\entities\Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (!\Yii::$app->user->can('AllCustomersManagement') && !\Yii::$app->user->can('admin')) {
            $model = Customer::find()->where(['id' => $id])->their()->one();
        }
        else {
            $model = Customer::findOne($id);
        }

        if ($model !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('customers', 'The requested page does not exist.'));
    }

	protected function findAccountModel($id)
	{
		$model = CustomerAccount::findOne($id);

		if ($model !== null) {
			return $model;
		}
		throw new NotFoundHttpException(Yii::t('customers', 'The requested page does not exist.'));
	}
}
