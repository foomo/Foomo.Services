<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
/* @var $view Foomo\MVC\View */
use Foomo\Flash\ActionScript\PHPUtils;
use Foomo\Flash\ActionScript\ViewHelper;
?>package <?= $model->myPackage; ?>.events
{
<?	if (!PHPUtils::isASStandardType($model->currentOperation->returnType->type)): ?>
	<?= $model->getClientAsClassImport($model->currentOperation->returnType->type) . PHP_EOL ?>

<? endif; ?>
	import org.foomo.zugspitze.services.core.proxy.events.ProxyMethodOperationEvent;

	/**
	 *
	 */
	public class <?= ViewHelper::toClassName($model->currentOperation->name, 'OperationEvent') ?> extends ProxyMethodOperationEvent
	{
		//-----------------------------------------------------------------------------------------
		// ~ Constants
		//-----------------------------------------------------------------------------------------

		public static const <?= ViewHelper::toConstantName($model->currentOperation->name) ?>_OPERATION_COMPLETE:String = '<?= lcfirst($model->currentOperation->name) ?>OperationComplete';
		public static const <?= ViewHelper::toConstantName($model->currentOperation->name) ?>_OPERATION_PROGRESS:String = '<?= lcfirst($model->currentOperation->name) ?>OperationProgress';
		public static const <?= ViewHelper::toConstantName($model->currentOperation->name) ?>_OPERATION_ERROR:String = '<?= lcfirst($model->currentOperation->name) ?>OperationError';

		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------

		public function <?= ViewHelper::toClassName($model->currentOperation->name, 'OperationEvent') ?>(type:String, result:*=null, error:*=null, messages:Array=null, total:Number=0, progress:Number=0)
		{
			super(type, result, error, messages, total, progress);
		}

		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------

		/**
		 *
		 */
		public function get result():<?= PHPUtils::getASType($model->currentOperation->returnType->type) . PHP_EOL ?>
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