<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
use Foomo\Services\ProxyGenerator\ActionScript\Utils;
$operation = $model->currentOperation;
?>package <?= $model->myPackage; ?>.events
{
<?	if (!Utils::isASStandardType($operation->returnType->type)): ?>
	<?= $model->getClientAsClassImport($operation->returnType->type) . PHP_EOL ?>
<? endif ?>
	import <?= $model->myPackage; ?>.calls.<?= $model->operationToMethodCallName($operation->name) ?>;

	import flash.events.Event;

	import org.foomo.zugspitze.services.core.proxy.events.ProxyMethodCallEvent;

	/**
	 *
	 */
	public class <?= $model->operationToMethodCallEventName($operation->name) ?> extends ProxyMethodCallEvent
	{
		//-----------------------------------------------------------------------------------------
		// ~ Constants
		//-----------------------------------------------------------------------------------------

		public static const <?= $model->toConstantName($operation->name) ?>_CALL_COMPLETE:String = "<?= $operation->name ?>CallComplete";
		public static const <?= $model->toConstantName($operation->name) ?>_CALL_PROGRESS:String = "<?= $operation->name ?>CallProgress";
		public static const <?= $model->toConstantName($operation->name) ?>_CALL_ERROR:String = "<?= $operation->name ?>CallError";

		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------

		public function <?= $model->operationToMethodCallEventName($operation->name) ?>(type:String, result:*=null, error:String='', exception:*=null, messages:Array=null, bytesTotal:Number=0, bytesLoaded:Number=0)
		{
			super(type, result, error, exception, messages, bytesTotal, bytesLoaded);
		}

		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------

		/**
		 * Method call result
		 */
		public function get result():<?= Utils::getASType($operation->returnType->type) . PHP_EOL ?>
		{
			return this.untypedResult;
		}
	}
}