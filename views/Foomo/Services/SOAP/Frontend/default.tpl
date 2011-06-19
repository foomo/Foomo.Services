<?
/* @var $model Foomo\Services\SOAP\Frontend\Model */
?><h1>Soap Service - <?= $model->serviceSoap->className ?></h1>
<div class="toolBoxMenu">
<ul>
	<li>
		<?= $view->link('wsdl', 'wsdl') ?>
	</li>
<? if(in_array(Foomo\Config::getMode(), array(Foomo\Config::MODE_DEVELOPMENT, Foomo\Config::MODE_TEST))): ?>
	<li>
		<?= $view->link('recompile wsdl', 'recompileWsdl') ?>
	</li>
  	<li>
  	Action script proxy
	<ul>
		<li>
		<!--
			<a href="<?= $model->compileProxyUrl ?>">compile</a>
		-->
			<?= $view->link('getASProxy', 'getASProxy') ?>
		</li>
		<? if(isset($model->serviceSoap->ASProxyCompilerSettings)): ?>
		  <li><a href="<?= $model->ASProxyClientUrl ?>">ASProxy sources download</a></li>
		  <li><a href="<?= $model->ASProxyClientSWCUrl ?>">ASProxy SWC download</a></li>
		</ul>
		<? else: ?>
		</ul>
		<p>if you want to have ASproxy rendering and a package download and / or direct compilation into your src dir, then you need to set the ASProxyCompilerSettings</p>
		<? endif; ?>
    </ul>
<? else: ?>
	<h2>compilation and service proxy downloads are only available in development</h2>
<? endif; ?>
</div>
<?= \Foomo\Services\Renderer\HtmlDocs::render($model->className) ?>
<h1>Plain docs</h1>
<div id="servicePlainDocs">
<pre>
<?= $view->escape(\Foomo\Services\Renderer\PlainDocs::render($model->className)) ?>
</pre>
</div>
