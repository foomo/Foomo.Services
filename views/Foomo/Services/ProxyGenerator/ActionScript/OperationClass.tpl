<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
/* @var $view Foomo\MVC\View */
use Foomo\Flash\ActionScript\PHPUtils;
use Foomo\Flash\ActionScript\ViewHelper;
$operation = $model->currentOperation;
?>package <?= $model->myPackage; ?>.operations
{
<?	if (!PHPUtils::isASStandardType($operation->returnType->type)): ?>
	<?= $model->getClientAsClassImport($operation->returnType->type) . PHP_EOL ?>

<? endif ?>
	import <?= $model->myPackage; ?>.<?= PHPUtils::getASType($model->proxyClassName) ?>;
	import <?= $model->myPackage; ?>.events.<?= ViewHelper::toClassName($operation->name, 'OperationEvent') ?>;
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
	<?= $model->getClientAsClassImport($throwType->type) .PHP_EOL ?>
	import <?= $model->myPackage; ?>.events.<?= ViewHelper::toClassName($model->getVOClassName($dataClass), 'Event') ?>;
<? endforeach; ?>
<? endif; ?>

	import org.foomo.zugspitze.services.core.proxy.operations.ProxyMethodOperation;

	[Event(name="<?= ucfirst(ViewHelper::toClassName($operation->name, 'Operation')) ?>Complete", type="<?= $model->myPackage; ?>.events.<?= ViewHelper::toClassName($operation->name, 'OperationEvent') ?>")]
	[Event(name="<?= ucfirst(ViewHelper::toClassName($operation->name, 'Operation')) ?>Progress", type="<?= $model->myPackage; ?>.events.<?= ViewHelper::toClassName($operation->name, 'OperationEvent') ?>")]
	[Event(name="<?= ucfirst(ViewHelper::toClassName($operation->name, 'Operation')) ?>Error", type="<?= $model->myPackage; ?>.events.<?= ViewHelper::toClassName($operation->name, 'OperationEvent') ?>")]

	/**
	 *
	 */
	public class <?= ViewHelper::toClassName($operation->name, 'Operation') ?> extends ProxyMethodOperation
	{
		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------

		/**
		 *
		 */
		public function <?= ViewHelper::toClassName($operation->name, 'Operation') ?>(<?= ViewHelper::renderParameters($operation->parameters); ?><?= (count($operation->parameters) > 0) ? ', ' : ''; ?>proxy:<?= PHPUtils::getASType($model->proxyClassName) ?>)
		{
			super(proxy, '<?= $operation->name ?>', [<?= ViewHelper::renderParameters($operation->parameters, false); ?>], <?= ViewHelper::toClassName($operation->name, 'OperationEvent') ?>);
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
			this._methodCall.addEventListener(<?= ViewHelper::toClassName($model->getVOClassName($dataClass), 'Event') ?>.<?= ViewHelper::toConstantName($model->getVOClassName($dataClass)) ?>, this.methodCall_proxyMethodCallExceptionHandler);
<? endforeach; ?>
<? endif; ?>
		}

		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------

		/**
		 *
		 */
		public function get result():<?= PHPUtils::getASType($operation->returnType->type) . PHP_EOL ?>
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
			this._methodCall.removeEventListener(<?= ViewHelper::toClassName($model->getVOClassName($dataClass), 'Event') ?>.<?= ViewHelper::toConstantName($model->getVOClassName($dataClass)) ?>, this.methodCall_proxyMethodCallExceptionHandler);
<? endforeach; ?>
			super.unload();
		}
<? endif; ?>
	}
}