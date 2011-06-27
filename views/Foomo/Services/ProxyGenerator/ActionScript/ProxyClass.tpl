<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
/* @var $view Foomo\MVC\View */
use Foomo\Flash\ActionScript\PHPUtils;
use Foomo\Flash\ActionScript\ViewHelper;
?>package <?= $model->myPackage . PHP_EOL; ?>
{
	import org.foomo.zugspitze.zugspitze_internal;
	import org.foomo.zugspitze.services.core.proxy.Proxy;
<? foreach($model->operations as $operation): ?>
	import <?= $model->myPackage ?>.calls.<?=ViewHelper::toClassName($operation->name, 'Call') ?>;
<? endforeach; ?>
<?= $view->indent($model->getAllClientClassImports(), 1) . PHP_EOL; ?>

	public class <?= PHPUtils::getASType($model->proxyClassName) ?> extends Proxy
	{
		//-----------------------------------------------------------------------------------------
		// ~ Constants
		//-----------------------------------------------------------------------------------------

		public static const VERSION:Number 		= <?= constant($model->serviceName.'::VERSION') ?>;
		public static const CLASS_NAME:String 	= '<?= str_replace('\\', '\\\\', $model->serviceName); ?>';

		//-----------------------------------------------------------------------------------------
		// ~ Static variables
		//-----------------------------------------------------------------------------------------

		/**
		 *
		 */
		public static var defaultEndPoint:String = '<?= Foomo\Utils::getServerUrl() . \Foomo\MVC::getCurrentUrlHandler()->renderMethodUrl('serve') ?>';

		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------

		public function <?= PHPUtils::getASType($model->proxyClassName) ?>(endPoint:String=null)
		{
			super((endPoint != null) ? endPoint : defaultEndPoint, CLASS_NAME, VERSION);
		}

		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------
<?php foreach($model->operations as $operation): ?>

		/**
		 *
		 */
		public function <?= $operation->name; ?>(<?= ViewHelper::renderParameters($operation->parameters) ?>):<?= ViewHelper::toClassName($operation->name, 'Call') . PHP_EOL ?>
		{
			return zugspitze_internal::sendMethodCall(new <?= ViewHelper::toClassName($operation->name, 'Call') ?>(<?= ViewHelper::renderParameters($operation->parameters, false) ?>));
		}
<?php endforeach; ?>
	}
}
