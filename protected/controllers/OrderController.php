<?php

class OrderController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    public function beforeAction($action) {
        Yii::app()->theme = "admin";
        parent::beforeAction($action);
        return true;
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index', 'view',
                ),
                'users' => array('@'),
            ),
            array('allow',
                'actions' => array('update', 'orderDetail'),
                'expression' => 'Yii::app()->user->isAdmin',
            //the 'user' var in an accessRule expression is a reference to Yii::app()->user
            ),
            array('allow',
                'actions' => array('delete', 'update', 'orderDetail'),
                'expression' => 'Yii::app()->user->isSuperAdmin',
            //the 'user' var in an accessRule expression is a reference to Yii::app()->user
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $model = $this->loadModel($id);

        /**
         * order detail part
         * 
         */
        $model_d = new OrderDetail('Search');
        $model_d->unsetAttributes();  // clear any default values
        $model_d->order_id = $id;
        if (isset($_GET['Order'])) {
            $model_d->attributes = $_GET['Order'];
        }
        
        $this->render('view', array(
            'model' => $model,
            'model_d' => $model_d,
            
        ));
    }

    /**
     * will be only use for update status
     * @param type $id
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        if (isset($_POST['Order'])) {
            $model->attributes = $_POST['Order'];
            $model->updateByPk($id, array("status" => $model->status));
            $this->redirect(array("view", "id" => $id));
        }
        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Manages all models.
     */
    public function actionIndex() {
        $model = new Order('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Order']))
            $model->attributes = $_GET['Order'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function actionOrderDetail($id) {

        $model = new OrderDetail('Search');
        $model->unsetAttributes();  // clear any default values
        $model->order_id = $id;
        if (isset($_GET['Order'])) {
            $model->attributes = $_GET['Order'];
        }
        $this->renderPartial('_order_detail', array(
            'model' => $model,
            'user_name' => $_POST['username'],
        ));
        Yii::app()->end();
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Order the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Order::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Order $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'order-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
