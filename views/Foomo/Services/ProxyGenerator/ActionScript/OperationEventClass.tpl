<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
use Foomo\Services\ProxyGenerator\ActionScript\Utils;
?>package <?= $model->myPackage; ?>.events
{
<?	if (!Utils::isASStandardType($model->currentOperation->returnType->type)): ?>
	<?= $model->getClientAsClassImport($model->currentOperation->returnType->type) . PHP_EOL ?>

<? endif; ?>
	import org.foomo.zugspitze.services.core.proxy.events.ProxyMethodOperationEvent;

	/**
	 *
	 */
	public class <?= $model->operationToOperationEventName($model->currentOperation->name) ?> extends ProxyMethodOperationEvent
	{
		//-----------------------------------------------------------------------------------------
		// ~ Constants
		//-----------------------------------------------------------------------------------------

		public static const <?= $model->toConstantName($model->currentOperation->name) ?>_OPERATION_COMPLETE:String = '<?= lcfirst($model->currentOperation->name) ?>OperationComplete';
		public static const <?= $model->toConstantName($model->currentOperation->name) ?>_OPERATION_PROGRESS:String = '<?= lcfirst($model->currentOperation->name) ?>OperationProgress';
		public static const <?= $model->toConstantName($model->currentOperation->name) ?>_OPERATION_ERROR:String = '<?= lcfirst($model->currentOperation->name) ?>OperationError';

		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------

		public function <?= $model->operationToOperationEventName($model->currentOperation->name) ?>(type:String, result:*=null, error:*=null, messages:Array=null, total:Number=0, progress:Number=0)
		{
			super(type, result, error, messages, total, progress);
		}

		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------

		/**
		 *
		 */
		public function get result():<?= Utils::getASType($model->currentOperation->returnType->type) . PHP_EOL ?>
		{
			return this.untypedResult;
		}

		/**
		 *
		 */
		public function get error():*
		{
			return this.untypedError;
		}
	}
}