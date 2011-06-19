<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
use Foomo\Services\ProxyGenerator\ActionScript\Utils;
?>package <?= $model->myPackage . PHP_EOL; ?>
{
	import org.foomo.zugspitze.zugspitze_internal;
	import org.foomo.zugspitze.services.core.proxy.Proxy;
<?= $model->getMethodCallImports() . PHP_EOL ?>

	public class <?= Utils::getASType($model->proxyClassName) ?> extends Proxy
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

		public function <?= Utils::getASType($model->proxyClassName) ?>(endPoint:String=null)
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
		public function <?= $operation->name; ?>(<?= Utils::renderParameters($operation->parameters) ?>):<?= $model->operationToMethodCallName($operation->name). PHP_EOL ?>
		{
			return zugspitze_internal::sendMethodCall(new <?= $model->operationToMethodCallName($operation->name) ?>(<?= Utils::renderParameters($operation->parameters, false) ?>));
		}
<?php endforeach; ?>
	}
}
