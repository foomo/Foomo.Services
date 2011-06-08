<?
/* @var $view \Foomo\MVC\View */
/* @var $model Foomo\Services\RPC\Frontend\Model */
?>
<h2>Proxy generator report</h2>
<p><?= $model->proxyGeneratorReport->success?'Success':'Failure' ?></p>
<pre>
<?= $view->escape($model->proxyGeneratorReport->report) ?>
</pre>

