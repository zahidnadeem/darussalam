<?php

/**
 * This is the model class for table "order_detail".
 *
 * The followings are the available columns in table 'order_detail':
 * @property integer $user_order_id
 * @property integer $order_id
 * @property integer $product_id
 * @property string $product_price
 *
 * The followings are the available model relations:
 * @property Product $product
 * @property Order $order
 */
class OrderDetail extends CActiveRecord {

    public $totalOrder;

    /**
     * 
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return OrderDetail the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'order_detail';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('order_id, product_id, product_price', 'required'),
            array('order_id, product_id', 'numerical', 'integerOnly' => true),
            array('product_price', 'length', 'max' => 10),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('user_order_id, order_id, product_id, product_price', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'product' => array(self::BELONGS_TO, 'Product', 'product_id'),
            'order' => array(self::BELONGS_TO, 'Order', 'order_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'user_order_id' => 'User Order',
            'order_id' => 'Order',
            'product_id' => 'Product',
            'product_price' => 'Product Price',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('user_order_id', $this->user_order_id);
        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('product_id', $this->product_id);
        $criteria->compare('product_price', $this->product_price, true);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

    public function featuredBooks($limit = 30) {
        $is_featured = '1';

        $criteria = new CDbCriteria(array(
                    'select' => '*',
                    'condition' => "is_featured='" . $is_featured . "' AND city_id='" . Yii::app()->session['city_id'] . "'",
                    'limit' => $limit,
                    'order' => 'product_id ASC',
                        //'with'=>'commentCount' 
                ));

        $data = Product::model()->findAll($criteria);


        $featured_products = array();
        $product = array();
        $images = array();
        foreach ($data as $products) {
            $product_id = $products->product_id;
            $criteria2 = new CDbCriteria;
            $criteria2->select = '*';  // only select the 'title' column
            $criteria2->condition = "product_id='" . $product_id . "'";
            $imagedata = ProductImage::model()->findAll($criteria2);
            $images = array();
            foreach ($imagedata as $img) {
                $images[] = array('product_image_id' => $img->product_image_id,
                    'image_large' => $img->image_large,
                    'image_small' => $img->image_small,
                );
            }

            $featured_products[] = array(
                'product_id' => $products->product_id,
                'product_name' => $products->product_name,
                'product_description' => $products->product_description,
                'product_price' => $products->product_price,
                'image' => $images
            );
        }
        return $featured_products;
    }

    public function bestSellings($limit = 30) {
        $best_products = array();
        $city_id= Yii::app()->session['city_id'];
        
        $criteria = new CDbCriteria(array(
                    'select' => "COUNT( t.product_id ) as totalOrder",
                    'group' => 't.product_id',
                    // 'condition'=>"is_featured='".$is_featured."' AND city_id='".Yii::app()->session['city_id']."'",
                    'limit' => $limit,
                    'order' => 'totalOrder DESC',
                ));
      
        $best_join = OrderDetail::model()->with(array(
                    'product' => array('select' => '*',
                        'joinType' => 'INNER JOIN',
                    'condition'=>'product.city_id= "'.$city_id.'"' ),))->findAll($criteria);

         $counter=count($best_join);
      for($i=0; $i<$counter; $i++)
      {
       $product_id= $best_join[$i]->product->product_id;  
       $product_name= $best_join[$i]->product->product_name;  
       $product_description= $best_join[$i]->product->product_description; 
       $product_price= $best_join[$i]->product->product_price; 
       $product_totalOrder= $best_join[$i]->totalOrder; 
      
                                    $criteria6 = new CDbCriteria;
                  $criteria6->select = '*';  // only select the 'title' column
                  $criteria6->condition = 'product_id="' . $product_id . '"';
                  $imagebest = ProductImage::model()->findAll($criteria6);
                  $imagesbestproducts = array();
                  foreach ($imagebest as $img) {
                      $imagesbestproducts[] = array('product_image_id' => $img->product_image_id,
                          'image_large' => $img->image_large,
                          'image_small' => $img->image_small,
                      );
                  }
       
                 $best_products[] = array('product_id' => $product_id,
                                        'product_name' => $product_name,
                                        'product_description' => $product_description,
                                        'product_price' => $product_price,
                                        'totalOrder' => $product_totalOrder,
                                        'image' => $imagesbestproducts);  
                    }

        return $best_products;
    }
    
    public function allProducts($limit=30)
    {
        
        //return $criteriaAll=  OrderDetail::model()->findAll();
        
        $all_pro = array();
        $city_id= Yii::app()->session['city_id'];
        
       $criteria = new CDbCriteria(array(
                    'select' => '*',
                    'condition' => "city_id='" . $city_id . "'",
                    'limit' =>$limit ,
                    'order' => 'product_id ASC',
                        //'with'=>'commentCount' 
                ));

        $data = Product::model()->findAll($criteria);


        $featured_products = array();
        $product = array();
        $images = array();
        foreach ($data as $products) {
            $product_id = $products->product_id;
            $criteria2 = new CDbCriteria;
            $criteria2->select = '*';  // only select the 'title' column
            $criteria2->condition = "product_id='" . $product_id . "'";
            $imagedata = ProductImage::model()->findAll($criteria2);
            $images = array();
            foreach ($imagedata as $img) {
                $images[] = array('product_image_id' => $img->product_image_id,
                    'image_large' => $img->image_large,
                    'image_small' => $img->image_small,
                );
            }

            $all_pro[] = array(
                'product_id' => $products->product_id,
                'product_name' => $products->product_name,
                'product_description' => $products->product_description,
                'product_price' => $products->product_price,
                'image' => $images
            );
        }
        return $all_pro;
    }
}