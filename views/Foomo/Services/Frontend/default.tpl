<?
/* @var $model Foomo\Services\Frontend\Model */
 Foomo\HTMLDocument::getInstance()->addStylesheets(array(
	Foomo\ROOT_HTTP . '/modules/' . Foomo\Services\Module::NAME . '/css/module.css'
 ));
?>

<div id="serviceMenu">

    <div class="innerBox">

	<h1>Services</h1>

	<? foreach($model->services as $moduleName => $services): ?>
		<? if(count($services) > 0): ?>
			<? $serviceRoot = \Foomo\ROOT_HTTP . '/modules/' . $moduleName . '/services'; ?>
			<h2 title="<?= $serviceRoot ?>"><?= $moduleName ?></h2>
			<ul>
	        <? foreach ($services as $service): ?>
				<li class="linkButtonYellow serviceHintHide" style="margin: 5px 0;"><a href="<?= $service;?>?explain" target="serviceDisplay"><?= substr($service, strlen($serviceRoot)+1) ?></a></li>
	        <? endforeach; ?>
			</ul>
			<br>
		<? endif; ?>
	<? endforeach; ?>
	</div>
</div>
<div id="serviceContent">
	<!--<img src="<? \Foomo\ROOT_HTTP ?>/r/img/trns.gif" width="1" height="1">-->
	<div id="serviceHint">
		<div class="whiteBox">
			<div class="innerBox">
				<p>Select a service from the menu</p>
			</div>
		</div>
	</div>
	<iframe id="serviceFrame" width="100%" height="100%" name="serviceDisplay" frameborder="0" src="about:blank" style="overflow: hidden"></iframe>
</div>
<script>
	$('#serviceFrame').load(function() {
		this.style.height = this.contentWindow.document.body.scrollHeight + 25 + 'px';
	});
	$('#serviceFrame').load(function() {
		$(this.contentWindow.document.body).click(function() {
			window.setTimeout(function() {
				var theIframe = $('#serviceFrame').get()[0].contentWindow.parent.window.document.getElementById('serviceFrame');
				$(theIframe).css('height', $('#serviceFrame').get()[0].contentWindow.document.body.scrollHeight + 25 + 'px');
			}, 100)
		})
	});
	$('.serviceHintHide').click(function() {
		$('#serviceHint').hide();
	});
</script>
