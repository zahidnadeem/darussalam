<?php $form = $this->beginWidget('CActiveForm'); ?>

<div class="row">
    <?php echo $form->labelEx($model, 'role'); ?>
    <?php
    $criteria = new CDbCriteria();
    $criteria->select = "name";
    $criteria->condition = "type = 2 AND name <> 'SuperAdmin'";
    $data = CHtml::listData(Authitem::model()->findAll($criteria), "name", "name");
    echo $form->dropDownList($model, 'role', array(""=>"All")+$data);
    ?>
    <?php echo $form->error($model, 'role'); ?>

</div>

<div class="row">
    <?php echo $form->labelEx($model, 'auth_item'); ?>
    <?php
    $criteria = new CDbCriteria();
    $criteria->select = "name";
    $criteria->condition = "type = 0 AND name LIKE '%*'";
    $data = CHtml::listData(Authitem::model()->findAll($criteria), "name", "name");
    echo $form->dropDownList($model, 'auth_item', array(""=>"All")+$data);
    ?>
    <?php echo $form->error($model, 'auth_item'); ?>

</div>
<div class="row buttons">
    <?php echo CHtml::submitButton(Rights::t('core', 'Save'), array("class" => "btn")); ?> | <?php echo CHtml::link(Rights::t('core', 'Cancel'), Yii::app()->user->rightsReturnUrl); ?>
</div>

<?php $this->endWidget(); ?>