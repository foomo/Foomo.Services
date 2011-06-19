<?
/* @var $view \Foomo\MVC\View */
/* @var $model Foomo\Services\RPC\Frontend\Model */
?>
<h1>RPC Service - <?= $model->serviceClassName ?></h1>
<div class="toolBoxMenu">
	<p><?= $view->link('service endpoint', 'serve') ?></p>
	<p>Serializer: <?= get_class($model->serializer) ?></p>
	<? if(Foomo\Config::getMode() != Foomo\Config::MODE_PRODUCTION): ?>
		<h2>Development tools</h2>
		<ul>
			<li><?= $view->link('explain this service to a machine', 'explainMachine') ?></li>
			<li>
				Proxy
				<? if($model->serializer instanceof Foomo\Services\RPC\Serializer\PHP): ?>
					(PHP / radact)
					<ul>
						<li><?= $view->link('Get a PHP client', 'getPHPClient') ?></li>
					</ul>
				<? elseif($model->serializer instanceof Foomo\Services\RPC\Serializer\AMF): ?>
					(Actionscript / Zugspitze)
						<ul>
							<li><?= $view->link('generate client source code', 'generateASClient', array(), 'This is useful, when you are generating the sources') ?></li>
							<li><?= $view->link('generate client source code and download tgz', 'getASClientAsTgz') ?></li>
							<li><?= $view->link('generate client source code and compile it', 'compileASClient') ?></li>
							<li><?= $view->link('generate client source code, compile it and download swc', 'getASClientAsSwc') ?></li>
						</ul>
				<? elseif($model->serializer instanceof Foomo\Services\RPC\Serializer\JSON): ?>
					(Javascript / JSON)
						<ul>
							<li><?= $view->link('jQuery client', 'generateJQueryClient') ?></li>
						</ul>
				<? else: ?>
					No proxy for the current serializer
				<? endif; ?>
			</li>
		</ul>
	<? else: ?>
		<p>Hint: There are development tools available in runmode development and test</p>
	<? endif; ?>
</div>
<h2><a name="docsHtml">HTML documentation</a></h2>
<p><a href="#docsPlain">go to plaintext docs</a></p>
<?= \Foomo\Services\Renderer\HtmlDocs::render($model->serviceClassName) ?>
<h2><a name="docsPlain">Plaintext documentation</a></h2>
<p><?= $view->link('get only the plaintext docs', 'plainTextDocs') ?></p>
<pre>
<?= $view->escape(\Foomo\Services\Renderer\PlainDocs::render($model->serviceClassName)) ?>
</pre>

