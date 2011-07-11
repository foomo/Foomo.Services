<?
/* @var $view \Foomo\MVC\View */
/* @var $model Foomo\Services\RPC\Frontend\Model */

 Foomo\HTMLDocument::getInstance()->addStylesheets(array(
	'http://fonts.googleapis.com/css?family=Ubuntu:regular,bold&v1',
	\Foomo\ROOT_HTTP . '/css/reset.css',
	\Foomo\ROOT_HTTP . '/css/app.css',
	\Foomo\ROOT_HTTP . '/css/module.css'
 ));
 
 Foomo\HTMLDocument::getInstance()->addJavascripts(array(
	\Foomo\ROOT_HTTP . '/js/jquery-1.6.1.min.js',
	\Foomo\ROOT_HTTP . '/js/modules.js'
))

?>
<div class="innerBox">
	
<h1>RPC Service - <?= $model->serviceClassName ?></h1>

	<div class="greyBox">
		<div class="innerBox">
			<?= $view->link('Service endpoint', 'serve', array(), array('class'=> 'linkButtonYellow')) ?><br>
			<br>
			<b>Serializer:</b> <?= get_class($model->serializer) ?><br>
			<br>	
			<? if(Foomo\Config::getMode() != Foomo\Config::MODE_PRODUCTION): ?>
				<div class="whiteBox">
					<div class="innerBox">
						<h2>Proxy</h2>
						<? if($model->serializer instanceof Foomo\Services\RPC\Serializer\PHP): ?>
							<b>PHP / Radact:</b> &nbsp; <?= $view->link('Get a PHP client', 'getPHPClient', array(), array('class'=> 'linkButtonYellow')) ?><br>
							<br>
						<? elseif($model->serializer instanceof Foomo\Services\RPC\Serializer\JSON): ?>
							<b>Javascript / JSON:</b> &nbsp; <?= $view->link('Get a jQuery client', 'generateJQueryClient', array(), array('class'=> 'linkButtonYellow')) ?><br>
							<br>
						<? else: ?>
							No proxy for the current serializer!<br>
							<br>
						<? endif; ?>
					</div>
				</div>
			<? else: ?>
				<p>Hint: There are development tools available in runmode development and test</p>
			<? endif; ?>

				
			<div class="tabBox">
				<div class="tabNavi">
					<ul>
						<li class="selected">Operations / Types</li>
						<li>Plaintext documentation</li>
					</ul>
					<hr class="greyLine">
				</div>
				<div class="tabContentBox">

					<div class="tabContent tabContent-1 selected">
						
						<h2>Operations</h2>
						
						<?= \Foomo\Services\Renderer\HtmlDocs::render($model->serviceClassName) ?>
						
					</div>

					<div class="tabContent tabContent-2">
						
						<h2>Plaintext documentation</h2>
						
						<div class="greyBox">
							<div class="innerBox">
								<pre><?= $view->escape(\Foomo\Services\Renderer\PlainDocs::render($model->serviceClassName)) ?></pre>
							</div>
						</div>
						
					</div>

				</div>
			</div>

		</div>
	</div>
</div>


<!--				
	<h2><a name="docsHtml">HTML documentation</a></h2>
	<p><a href="#docsPlain">go to plaintext docs</a></p>
	
	
	
	<h2><a name="docsPlain">Plaintext documentation</a></h2>
	<p><?= $view->link('get only the plaintext docs', 'plainTextDocs') ?></p>
-->