<?
/* @var $model \Foomo\Services\Utils */
$allServices = \Foomo\Services\Utils::getAllServices();
\Foomo\HTMLDocument::getInstance()->addStylesheets(array(
	\Foomo\ROOT_HTTP . '/css/module.css',
	\Foomo\ROOT_HTTP . '/modules/services/css/module.css'
));
		
?>
<h1>Services</h1>
<div class="toolBoxMenu" style="position:fixed;left:0;bottom:0;top:60px;width:300;overflow: auto;">
    <ul>
	<? foreach($allServices as $moduleName => $services): ?>
		<? if(count($services) > 0): ?>
			<? $serviceRoot = \Foomo\ROOT_HTTP . '/modules/' . $moduleName . '/services'; ?>
			<h2 title="<?= $serviceRoot ?>"><?= $moduleName ?></h2>
	        <? foreach ($services as $service): ?>
	          <li><a href="<?= $service;?>?explain" target="serviceDisplay"><?= substr($service, strlen($serviceRoot)+1) ?></a></li>
	        <? endforeach; ?>
		<? endif; ?>
	<? endforeach; ?>
      </ul>
</div>
<div style="position:fixed;bottom:0;top:0px;right:0;left:350px;background-color: white;">
	<img src="<? \Foomo\ROOT_HTTP ?>/r/img/trns.gif" width="1" height="1">
	<iframe width="100%" height="100%" name="serviceDisplay" frameborder="0" src="about:blank"></iframe>
</div>
