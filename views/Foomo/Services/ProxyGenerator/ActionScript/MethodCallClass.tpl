<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $dataClass ServiceObjectType */
/* @var $operation ServiceOperation */
$operation = $model->currentOperation;


/**
 * name => ServiceObjectType
 *
 * @param $props[] $params
 * @return string
 */
function renderMethodProperties($props)
{
	$output = array();
	if (count($props) == 0) return '';
	foreach($props as $name => $type) {
		$output[] = 'this._methodReply.exception.' . $name;
	}
	return ', ' . implode(', ', $output);
}


use Foomo\Services\ProxyGenerator\ActionScript\Utils;
?>package <?= $model->myPackage; ?>.calls
{
<? if (count($operation->throwsTypes) > 0): ?>
	import org.foomo.zugspitze.services.core.rpc.events.RPCMethodCallEvent;
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
	<?= $model->getClientAsClassImport($throwType->type) .PHP_EOL ?>
	import <?= $model->myPackage; ?>.events.<?= $model->toEventName($model->getVOClassName($dataClass)) ?>;
<? endforeach; ?>

<? endif; ?>
	import <?= $model->myPackage; ?>.events.<?= $model->operationToMethodCallEventName($operation->name) ?>;
	import org.foomo.zugspitze.services.core.proxy.calls.ProxyMethodCall;
<?	if (!Utils::isASStandardType($operation->returnType->type)): ?>
	<?= $model->getClientAsClassImport($operation->returnType->type) . PHP_EOL ?>
<? endif ?>

	[Event(name="<?= $operation->name ?>CallComplete", type="<?= $model->myPackage; ?>.events.<?= $model->operationToMethodCallEventName($operation->name) ?>")]
	[Event(name="<?= $operation->name ?>CallProgress", type="<?= $model->myPackage; ?>.events.<?= $model->operationToMethodCallEventName($operation->name) ?>")]
	[Event(name="<?= $operation->name ?>CallError", type="<?= $model->myPackage; ?>.events.<?= $model->operationToMethodCallEventName($operation->name) ?>")]
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
	[Event(name="<?= lcfirst($model->getVOClassName($dataClass)) ?>", type="<?= $model->myPackage; ?>.events.<?= $model->toEventName($model->getVOClassName($dataClass)) ?>")]
<? endforeach; ?>
<? endif; ?>

	/**
	 *
	 */
	public class <?= $model->operationToMethodCallName($operation->name) ?> extends ProxyMethodCall
	{
		//-----------------------------------------------------------------------------------------
		// ~ Constants
		//-----------------------------------------------------------------------------------------

		public static const METHOD_NAME:String = '<?= $operation->name ?>';

		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------

		public function <?= $model->operationToMethodCallName($operation->name) ?>(<?= Utils::renderParameters($operation->parameters) ?>)
		{
			super(METHOD_NAME, [<?= Utils::renderParameters($operation->parameters, false) ?>], <?= $model->operationToMethodCallEventName($operation->name) ?>);
		}

		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------

		/**
		 * Method call result
		 */
		public function get result():<?= Utils::getASType($operation->returnType->type) . PHP_EOL ?>
		{
			return this.methodReply.value;
		}
<? if (count($operation->throwsTypes) > 0): ?>

		//-----------------------------------------------------------------------------------------
		// ~ Overriden methods
		//-----------------------------------------------------------------------------------------

		/**
		 * Complete handler
		 *
		 * @private
		 */
		override protected function token_methodCallTokenCompleteHandler(event:RPCMethodCallEvent):void
		{
			this._methodReply = event.methodReply;
			if (this._methodReply.exception != null) {
				switch (true) {
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
					case (this._methodReply.exception is <?= $model->getVOClassName($dataClass) ?>):
						this.dispatchEvent(new <?= $model->toEventName($model->getVOClassName($dataClass)) ?>(<?= $model->toEventName($model->getVOClassName($dataClass)) ?>.<?= $model->toConstantName(lcfirst($model->getVOClassName($dataClass))) ?><?= renderMethodProperties($dataClass->props) ?>));
						break;
<? endforeach; ?>
					default:
						throw new Error('Unhandled exception type');
						break;
				}
			} else {
				this.dispatchEvent(new <?= $model->operationToMethodCallEventName($operation->name) ?>(<?= $model->operationToMethodCallEventName($operation->name) ?>.<?= $model->toConstantName($operation->name) ?>_CALL_COMPLETE, this));
			}
		}
<? endif; ?>
	}
}