<?php
/* @var $this CategoriesController */
/* @var $model Categories */

$this->breadcrumbs = array(
    'Categories' => array('index'),
    $model->category_id,
);

if(!(Yii::app()->user->isGuest)) {
        $this->renderPartial("/common/_left_menu");
}
?>



<div class="pading-bottom-5">
    <div class="left_float">
        <h1>View Categories #<?php echo $model->category_id; ?></h1>
    </div>

    <?php /* Convert to Monitoring Log Buttons */ ?>
    <div class = "right_float">
        <span class="creatdate">
            <?php
            echo CHtml::link("Edit", $this->createUrl("update",array("id"=>$model->primaryKey)), array('class' => "print_link_btn"))
            ?>
        </span>
    </div>
</div>

<?php
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'category_name',
        'added_date',
    ),
));
?>
