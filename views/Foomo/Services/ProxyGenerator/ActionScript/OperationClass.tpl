<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
use Foomo\Services\ProxyGenerator\ActionScript\Utils;
$operation = $model->currentOperation;
?>package <?= $model->myPackage; ?>.operations
{
<?	if (!Utils::isASStandardType($operation->returnType->type)): ?>
	<?= $model->getClientAsClassImport($operation->returnType->type) . PHP_EOL ?>

<? endif ?>
	import <?= $model->myPackage; ?>.<?= Utils::getASType($model->proxyClassName) ?>;
	import <?= $model->myPackage; ?>.events.<?= $model->operationToOperationEventName($operation->name) ?>;
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
	<?= $model->getClientAsClassImport($throwType->type) .PHP_EOL ?>
	import <?= $model->myPackage; ?>.events.<?= $model->toEventName($model->getVOClassName($dataClass)) ?>;
<? endforeach; ?>
<? endif; ?>

	import org.foomo.zugspitze.services.core.proxy.operations.ProxyMethodOperation;

	[Event(name="<?= ucfirst($model->operationToOperationName($operation->name)) ?>Complete", type="<?= $model->myPackage; ?>.events.<?= $model->operationToOperationEventName($operation->name) ?>")]
	[Event(name="<?= ucfirst($model->operationToOperationName($operation->name)) ?>Progress", type="<?= $model->myPackage; ?>.events.<?= $model->operationToOperationEventName($operation->name) ?>")]
	[Event(name="<?= ucfirst($model->operationToOperationName($operation->name)) ?>Error", type="<?= $model->myPackage; ?>.events.<?= $model->operationToOperationEventName($operation->name) ?>")]

	/**
	 *
	 */
	public class <?= $model->operationToOperationName($operation->name) ?> extends ProxyMethodOperation
	{
		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------

		/**
		 *
		 */
		public function <?= $model->operationToOperationName($operation->name) ?>(<?= Utils::renderParameters($operation->parameters); ?><?= (count($operation->parameters) > 0) ? ', ' : ''; ?>proxy:<?= Utils::getASType($model->proxyClassName) ?>)
		{
			super(proxy, '<?= $operation->name ?>', [<?= Utils::renderParameters($operation->parameters, false); ?>], <?= $model->operationToOperationEventName($operation->name) ?>);
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
			this._methodCall.addEventListener(<?= $model->toEventName($model->getVOClassName($dataClass)) ?>.<?= $model->toConstantName(lcfirst($model->getVOClassName($dataClass))) ?>, this.methodCall_proxyMethodCallExceptionHandler);
<? endforeach; ?>
<? endif; ?>
		}

		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------

		/**
		 *
		 */
		public function get result():<?= Utils::getASType($operation->returnType->type) . PHP_EOL ?>
		{
			return this.untypedResult;
		}
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>

		/**
		 *
		 */
		public function get <?= lcfirst($model->getVOClassName($dataClass)) ?>():<?= $model->getVOClassName($dataClass) . PHP_EOL ?>
		{
			return this.error as <?= $model->getVOClassName($dataClass) . PHP_EOL ?>
		}
<? endforeach; ?>

		//-----------------------------------------------------------------------------------------
		// ~ Overriden methods
		//-----------------------------------------------------------------------------------------

		/**
		 * @inherit
		 */
		override public function unload():void
		{
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
			this._methodCall.removeEventListener(<?= $model->toEventName($model->getVOClassName($dataClass)) ?>.<?= $model->toConstantName(lcfirst($model->getVOClassName($dataClass))) ?>, this.methodCall_proxyMethodCallExceptionHandler);
<? endforeach; ?>
			super.unload();
		}
<? endif; ?>
	}
}