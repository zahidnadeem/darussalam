<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->user_id=>array('view','id'=>$model->user_id),
	'Update',
);

$this->menu=array(
	array('label'=>'List User', 'url'=>array('index')),
	array('label'=>'Create User', 'url'=>array('create')),
	array('label'=>'View User', 'url'=>array('view', 'id'=>$model->user_id)),
	array('label'=>'Manage User', 'url'=>array('admin')),
);
?>

<h1>Dear: <?php echo $model->user_name; ?>  Update your Profile</h1>

<?php echo $this->renderPartial('_form_profile', array('model'=>$model)); ?>