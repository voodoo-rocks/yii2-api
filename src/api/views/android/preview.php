<?php
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

?>
<div class="page-header">
    <h1>
        Android SDK
    </h1>
</div>
<?php
$form = ActiveForm::begin();

echo Html::submitButton('Download', ['class' => 'btn btn-primary']);

$form->end();
?>
