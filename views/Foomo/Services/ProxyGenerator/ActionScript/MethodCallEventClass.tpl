<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
/* @var $view Foomo\MVC\View */
use Foomo\Flash\ActionScript\PHPUtils;
use Foomo\Flash\ActionScript\ViewHelper;
$operation = $model->currentOperation;
?>package <?= $model->myPackage; ?>.events
{
<?	if (!PHPUtils::isASStandardType($operation->returnType->type)): ?>
	<?= $model->getClientAsClassImport($operation->returnType->type) . PHP_EOL ?>
<? endif ?>
	import <?= $model->myPackage; ?>.calls.<?= ViewHelper::toClassName($operation->name, 'Call') ?>;

	import flash.events.Event;

	import org.foomo.zugspitze.services.core.proxy.events.ProxyMethodCallEvent;

	/**
	 *
	 */
	public class <?= ViewHelper::toClassName($operation->name, 'CallEvent') ?> extends ProxyMethodCallEvent
	{
		//-----------------------------------------------------------------------------------------
		// ~ Constants
		//-----------------------------------------------------------------------------------------

		public static const <?= ViewHelper::toConstantName($operation->name) ?>_CALL_COMPLETE:String = "<?= $operation->name ?>CallComplete";
		public static const <?= ViewHelper::toConstantName($operation->name) ?>_CALL_PROGRESS:String = "<?= $operation->name ?>CallProgress";
		public static const <?= ViewHelper::toConstantName($operation->name) ?>_CALL_ERROR:String = "<?= $operation->name ?>CallError";

		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------

		public function <?= ViewHelper::toClassName($operation->name, 'CallEvent') ?>(type:String, result:*=null, error:String='', exception:*=null, messages:Array=null, bytesTotal:Number=0, bytesLoaded:Number=0)
		{
			super(type, result, error, exception, messages, bytesTotal, bytesLoaded);
		}

		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------

		/**
		 * Method call result
		 */
		public function get result():<?= PHPUtils::getASType($operation->returnType->type) . PHP_EOL ?>
		{
			return this.untypedResult;
		}
	}
}