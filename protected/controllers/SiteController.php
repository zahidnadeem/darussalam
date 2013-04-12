<?php

class SiteController extends Controller {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {

        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $siteUrl = $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $site_id = SelfSite::model()->getSiteId($siteUrl);
        Yii::app()->session['site_id'] = $site_id;
        
        if(isset(Yii::app()->session['city_id']) && Yii::app()->session['city_id']!='')
        {
             $city_id=Yii::app()->session['city_id'];
        }
        else
        {
            $locationArray=Yii::app()->user->IpInfo;
            $city_auto=  strtolower($locationArray['citystate']);
            $country_auto=  strtolower($locationArray['country']);
            $short_country_auto=  strtolower($locationArray['short_country']);

            $cityfind = City::model()->find('LOWER(city_name)=?',array($city_auto));
            if($cityfind!=null)
            {
                $city_id=$cityfind->city_id;
            }
            else
            {
                $countryfind = Country::model()->find('LOWER(country_name)=?',array($country_auto));
                if($countryfind!=null)
                {
                    $city_find = City::model()->find('country_id=?',array($countryfind->country_id));
                    $city_id=$city_find->city_id;
                }
                else
                {
                    $city_auto=Yii::app()->params->head_office_city;
                    $cityfind = City::model()->find('LOWER(city_name)=?',array($city_auto));
                    $city_id=$cityfind->city_id;
                }
            }
        
        }

        $city = City::model()->findByPk($city_id);
        $countries = Country::model()->findByPk($city['country_id']);
        $country_short_name=$countries['short_name'];
        $city_short_name=$city['short_name'];
        
        $layout_id = $city['layout_id'];
        $layout = Layout::model()->findByPk($layout_id);
        $layout_name = $layout['layout_name'];

        Yii::app()->session['layout'] = $layout_name;
        Yii::app()->session['country_short_name'] = $country_short_name;
        Yii::app()->session['city_short_name'] = $city_short_name;
        Yii::app()->session['city_id'] = $city['city_id'];
        Yii::app()->theme = Yii::app()->session['layout'];
        $this->redirect(array('/site/storehome','country' => Yii::app()->session['country_short_name'], 'city' => Yii::app()->session['city_short_name'], 'city_id' => Yii::app()->session['city_id']));
    }

    public function actionStoreHome() {

        $city = City::model()->findByPk($_REQUEST['city_id']);
        $layout_id = $city['layout_id'];
        $layout = Layout::model()->findByPk($layout_id);
        $layout_name = $layout['layout_name'];

        Yii::app()->session['layout'] = $layout_name;
        Yii::app()->session['country_short_name'] = $_REQUEST['country'];
        Yii::app()->session['city_short_name'] = $_REQUEST['city'];
        Yii::app()->session['city_id'] = $_REQUEST['city_id'];
        Yii::app()->theme = Yii::app()->session['layout'];

        $order_detail = new OrderDetail;

        $limit = 3;
        $featured_products = $order_detail->featuredBooks($limit);
        $bestSellings = $order_detail->bestSellings($limit);

        $this->render('storehome', array('product' => $featured_products, 'best_sellings' => $bestSellings)
        );
    }

    public function actionallProducts() {
        //queries 
        $order_detail = new OrderDetail;
        $all_products = $order_detail->featuredBooks();
        Yii::app()->controller->layout = '//layouts/main';
        $this->render('all_products',array('products'=>$all_products));
    }
    public function actionfeaturedProducts() {
        Yii::app()->theme = Yii::app()->session['layout'];
        //queries 
        $order_detail = new OrderDetail;
        $featured_products = $order_detail->featuredBooks();
        Yii::app()->controller->layout = '//layouts/main';
        $this->render('featured_products',array('products'=>$featured_products));
    }

    public function actionbestSellings() {
        Yii::app()->theme = Yii::app()->session['layout'];
        //queries 
        $order_detail = new OrderDetail;
        $best_sellings = $order_detail->bestSellings();
        Yii::app()->controller->layout = '//layouts/main';
        $this->render('best_sellings',array('products'=>$best_sellings));
    }

    public function actionproductListing() {
        Yii::app()->theme = Yii::app()->session['layout'];

        Yii::app()->controller->layout = '//layouts/main';
        $this->render('product_listing');
    }

    public function actionproductDetail() {
        Yii::app()->theme = Yii::app()->session['layout'];

        $product_obj = new Product();
        $product=$product_obj->find($condition='product_id='.$_REQUEST['product_id']);
        
        Yii::app()->controller->layout = '//layouts/main';
        $this->render('product_detail',array('product'=>$product));
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact() {
        Yii::app()->controller->layout = '//layouts/slider';
        //Yii::app()->theme='admin';
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                $to = Yii::app()->params->supportEmail;
                $from = Yii::app()->params->adminEmail;

                $headers = array(
                    'MIME-Version: 1.0',
                    'Content-type: text/html; charset=iso-8859-1',
                );
                $subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';

                $message = "
                                <html>
                                    <body>
                                    Name : $model->name <br />
                                    From : $model->email <br />
                                    Subject : $model->subject <br /><br />
                                    Message : $model->body</body>
                            </html>";

                Yii::app()->email->send($from, $to, $subject, $message, $headers);
                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {

                if (Yii::app()->user->isSuperAdmin) {
                    $this->redirect(array('user/admin'));
                }
                if (Yii::app()->user->isAdmin) {
                    $this->redirect(array('user/admin'));
                }
                if (Yii::app()->user->isCustomer) {
                    //$this->redirect(array('site/index'));
                    $this->redirect(array('/site/allProducts','country' => Yii::app()->session['country_short_name'], 'city' => Yii::app()->session['city_short_name'], 'city_id' => Yii::app()->session['city_id']));
                }
            }
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }
    
    public function actionDdlAjax()
    {
        
        $city_id=$_POST['city_id'];
        $city = City::model()->findByPk($city_id);
        $countries = Country::model()->findByPk($city['country_id']);
        $country_short_name=$countries['short_name'];
        $city_short_name=$city['short_name'];
        
        $layout_id = $city['layout_id'];
        $layout = Layout::model()->findByPk($layout_id);
        $layout_name = $layout['layout_name'];

        Yii::app()->session['layout'] = $layout_name;
        Yii::app()->session['country_short_name'] = $country_short_name;
        Yii::app()->session['city_short_name'] = $city_short_name;
        Yii::app()->session['city_id'] = $city['city_id'];
        Yii::app()->theme = Yii::app()->session['layout'];
        echo CJSON::encode(array('redirect'=>$this->createUrl('/site/storehome',array('country' => Yii::app()->session['country_short_name'], 'city' => Yii::app()->session['city_short_name'], 'city_id' => Yii::app()->session['city_id']))));
    }

}