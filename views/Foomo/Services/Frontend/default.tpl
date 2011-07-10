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
	          <li class="linkButtonYellow" style="margin: 5px 0;"><a href="<?= $service;?>?explain" target="serviceDisplay"><?= substr($service, strlen($serviceRoot)+1) ?></a></li>
	        <? endforeach; ?>
			</ul>
			<br>
		<? endif; ?>
	<? endforeach; ?>
	</div>
</div>
<div id="serviceContent">
	<!--<img src="<? \Foomo\ROOT_HTTP ?>/r/img/trns.gif" width="1" height="1">-->
	<iframe width="100%" height="100%" name="serviceDisplay" frameborder="0" src="about:blank"></iframe>
</div>
