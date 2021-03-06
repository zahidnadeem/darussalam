<?php

class UserProfileController extends Controller {

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

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index', 'view'),
                'users' => array('@'),
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
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionIndex() {


        Yii::app()->user->SiteSessions;
        Yii::app()->controller->layout = '//layouts/main';
        $model = UserProfile::model()->findByPk(Yii::app()->user->id);
        /**
         * to persist old pic for this
         */
        $old_pic = $model->avatar;
        if (empty($model)) {
            $model = new UserProfile;
        }


        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['UserProfile'])) {
            $model->id = Yii::app()->user->id;
            $model->attributes = $_POST['UserProfile'];
            $user_file = DTUploadedFile::getInstance($model, 'avatar');
            $model->avatar = $user_file;
            if (empty($user_file)) {
                $model->avatar = $old_pic;
            }

            if ($model->save()) {
                $upload_path = DTUploadedFile::creeatRecurSiveDirectories(array("user_profile", Yii::app()->user->id));
                if (!empty($user_file)) {
                    $user_file->saveAs($upload_path . $user_file->name);
                }
                Yii::app()->user->setFlash("profie_success", "Your Profile has been updated successfully");
                $this->redirect($this->createUrl("index"));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return UserProfile the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = UserProfile::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param UserProfile $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'user-profile-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
