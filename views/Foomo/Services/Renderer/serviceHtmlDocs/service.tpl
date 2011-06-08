<?php
/* @var $model ServiceHtmlRenderer */
$model = $model;
?><div id="serviceHtmlRenderer">
<h1>Documentation for Service <?php echo $model->serviceName . ' ' . date('Y-m-d H:i:s'); ?></h1>
<p><?php echo $model->serviceClassDocs; ?></p>
<h2>Operations</h2>
<ul>
<?php
	echo $model->opsHtmlToc;
?>
</ul>
<table>
<?php echo $model->opsHtml ?>
</table>
<h2>Types</h2>
<div id="serviceHtmlRendererTypes">
<?php echo $model->typesHtml ?>
</div>
</div>