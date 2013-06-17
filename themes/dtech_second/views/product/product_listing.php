<div class="general_content">
    <div class="under_heading">
        <h2>All General Books</h2>
        <?php
        echo CHtml::image(Yii::app()->theme->baseUrl . "/images/under_heading_07.png") . '<br>';
        ?>
    </div>

    <div id="right_main_content">
        <?php
        $this->renderPartial("//product/_product_list", array(
            'products' => $products,
            'dataProvider' => $dataProvider,
            'allCate' => $allCate));
        ?>
    </div>


</div>

