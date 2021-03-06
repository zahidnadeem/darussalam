<?php
/* @var $this CityController */
/* @var $model City */

$this->breadcrumbs = array(
    'Cities' => array('index'),
    $model->city_id,
);

if (!(Yii::app()->user->isGuest)) {
    $this->renderPartial("/common/_left_menu");
}
?>

<div class="pading-bottom-5">
    <div class="left_float">
        <h1>View City #<?php echo $model->city_id; ?></h1>
    </div>

    <?php /* Convert to Monitoring Log Buttons */ ?>
    <div class = "right_float">
        <span class="creatdate">
            <?php
            echo CHtml::link("Edit", $this->createUrl("update", array("id" => $model->primaryKey)), array('class' => "print_link_btn"))
            ?>
        </span>
    </div>
</div>

<?php
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'city_name',
        'short_name',
        'address',
    ),
));
?>
