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
			<?= $view->link('Service endpoint', 'serve', array(), array('class'=> 'linkButtonYellow')) ?><br />
			<br />
			<b>Serializer:</b> <?= get_class($model->serializer) ?><br />
			<br />
			<? if(Foomo\Config::getMode() != Foomo\Config::MODE_PRODUCTION): ?>
				<div class="whiteBox">
					<div class="innerBox">
						<h2>Proxy</h2>
						<? if($model->serializer instanceof Foomo\Services\RPC\Serializer\PHP): ?>
							<b>PHP / Foomo:</b> &nbsp; <?= $view->link('Get a PHP client', 'getPHPClient', array(), array('class'=> 'linkButtonYellow')) ?><br>
							<br>
						<? elseif($model->serializer instanceof Foomo\Services\RPC\Serializer\JSON): ?>
							<b>Javascript / JSON:</b> &nbsp; <?= $view->link('Get a jQuery client', 'generateJQueryClient', array(), array('class'=> 'linkButtonYellow')) ?><br />
							<br />
						<? elseif($model->serializer instanceof Foomo\Services\RPC\Serializer\AMF): ?>
							<? if (Foomo\Modules\Manager::isEnabled('Foomo.Zugspitze')): ?>
								<? // @todo make it possible to link to other apps ?>
								<b>Zugspitze:</b> &nbsp; <a href="<?= Foomo\Utils::getServerUrl() . \Foomo\ROOT_HTTP ?>/index.php/Foomo/showMVCApp/Foomo.Zugspitze.ProxyGenerator/default" target="_top">Proxy Generator</a><br />
								<br />
							<? endif; ?>
							<? if (Foomo\Modules\Manager::isEnabled('Foomo.Zugspitze.Backport')): ?>
								<? // @todo make it possible to link to other apps ?>
								<b>Zugspitze Backport:</b> &nbsp; <a href="<?= Foomo\Utils::getServerUrl() . \Foomo\ROOT_HTTP ?>/index.php/Foomo/showMVCApp/Foomo.Zugspitze.Backport.ProxyGenerator/default" target="_top">Proxy Generator</a><br />
								<br />
							<? endif; ?>
						<? else: ?>
							No proxy for the current serializer!<br />
							<br />
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