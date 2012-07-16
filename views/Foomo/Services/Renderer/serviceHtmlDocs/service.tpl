<?php
/* @var $model ServiceHtmlRenderer */
$model = $model;
?>
<h2><?= $model->serviceName ?></h2>
<p><?= htmlentities($model->serviceClassDocs) ?></p>
<h2>Operations</h2>
<?= $model->opsHtml ?>
<br>
<h2>Types</h2>
<?= $model->typesHtml ?>
