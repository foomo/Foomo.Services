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
	\Foomo\ROOT_HTTP . '/js/jquery.address-1.4.min.js',
	\Foomo\ROOT_HTTP . '/js/modules.js'
))

?>
<div class="innerBox">
		<div class="tabBox">
			<div class="innerBox">
				<h1>RPC Service - <?= $model->serviceClassName ?></h1>
				<? if(Foomo\Config::getMode() != Foomo\Config::MODE_PRODUCTION): ?>
					<b>Serializer:</b> <?= get_class($model->serializer) ?><br>
					<br>
					<? if($model->serializer instanceof Foomo\Services\RPC\Serializer\PHP): ?>
						<?= $view->link('Get a PHP client', 'getPHPClient', array(), array('class'=> 'linkButtonYellow')) ?>
					<? elseif($model->serializer instanceof Foomo\Services\RPC\Serializer\JSON): ?>
						<?= $view->link('update and view jQuery client', 'generateJQueryClient', array(), array('class'=> 'linkButtonYellow')) ?>
					<? elseif($model->serializer instanceof Foomo\Services\RPC\Serializer\AMF): ?>
						<? if (Foomo\Modules\Manager::isEnabled('Foomo.Zugspitze')): ?>
							<? // @todo make it possible to link to other apps ?>
							<b>Zugspitze:</b> &nbsp; <a href="<?= Foomo\Utils::getServerUrl() . \Foomo\ROOT_HTTP ?>/index.php/Foomo/showMVCApp/Foomo.Zugspitze.ProxyGenerator/default" target="_top">Proxy Generator</a>
						<? endif; ?>
						<? if (Foomo\Modules\Manager::isEnabled('Foomo.Zugspitze.Backport')): ?>
							<? // @todo make it possible to link to other apps ?>
							<b>Zugspitze Backport:</b> &nbsp; <a href="<?= Foomo\Utils::getServerUrl() . \Foomo\ROOT_HTTP ?>/index.php/Foomo/showMVCApp/Foomo.Zugspitze.Backport.ProxyGenerator/default" target="_top">Proxy Generator</a>
						<? endif; ?>
					<? else: ?>
						No proxy for the current serializer!<br>
						<br>
					<? endif; ?>
					<?= $view->link('Service endpoint', 'serve', array(), array('class'=> 'linkButtonYellow')) ?><br>
					<br>
				<? else: ?>
					<p>Hint: There are development tools available in runmode development and test</p>
				<? endif; ?>
			</div>
		</div>


			<div class="tabBox">
				<div class="tabNavi">
					<ul>
						<li class="selected">HTML docs</li>
						<li>Plaintext docs</li>
					</ul>
					<hr class="greyLine">
				</div>
				<div class="tabContentBox">

					<div class="tabContent tabContent-1 selected">
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

<script>

// open type docs when jumping to the anchor

(new function() {
	console.log(this);
	this.urlCallBack = function(event) {
		var a = $('a[name="' + event.value.substr(1).replace(/\\/g, '\\\\') + '"]');
		a.parent().parent().children('.toggleOpenIcon').html('-');
		a.parent().parent().parent().children('.toggleContent').show();
		$('html, body').scrollTop(
			a.parent().parent().parent().offset().top
		);
	};
	$.address.init(this.urlCallBack).change(this.urlCallBack);	
});

</script>