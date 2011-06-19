<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
use Foomo\Services\ProxyGenerator\ActionScript\Utils;
$operation = $model->currentOperation;
?>package <?= $model->myPackage; ?>.commands
{
	import <?= $model->myPackage; ?>.<?= Utils::getASType($model->proxyClassName) ?>;
	import <?= $model->myPackage; ?>.calls.<?= $model->operationToMethodCallName($operation->name) ?>;
	import <?= $model->myPackage; ?>.events.<?= $model->operationToMethodCallEventName($operation->name) ?>;
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
	import <?= $model->myPackage; ?>.events.<?= $model->toEventName($model->getVOClassName($dataClass)) ?>;
<? endforeach; ?>
<? endif; ?>

	import org.foomo.zugspitze.commands.Command;
	import org.foomo.zugspitze.commands.ICommand;
	import org.foomo.zugspitze.core.IUnload;

	/**
	 * Create your own command instance and override the protected event handlers
	 */
	public class <?= $model->operationToAbstractCommandName($operation->name) ?> extends Command implements ICommand, IUnload
	{
		//-----------------------------------------------------------------------------------------
		// ~ Variables
		//-----------------------------------------------------------------------------------------

		/**
		 * Service proxy
		 */
		public var proxy:<?= Utils::getASType($model->proxyClassName) ?>;
<? foreach ($operation->parameters as $name => $type): ?>
<?= Utils::indentLines(Utils::renderComment(isset($operation->parameterDocs[$name]) ? $operation->parameterDocs[$name]->comment : ''), 2) . PHP_EOL ?>
		public var <?= $name ?>:<?= Utils::getASType($type) ?>;
<? endforeach; ?>
		/**
		 * Returned call from the proxy
		 */
		protected var _methodCall:<?= $model->operationToMethodCallName($operation->name) ?>;

		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------

		/**
<? foreach ($operation->parameters as $name => $type): ?>
		 * @param <?= $name ?> <?= isset($operation->parameterDocs[$name]) && !empty($operation->parameterDocs[$name]->comment) ? $operation->parameterDocs[$name]->comment : '' ?>;
<? endforeach; ?>
		 * @param proxy Service proxy
		 * @param setBusyStatus Set busy status while pending
		 */
		public function <?= $model->operationToAbstractCommandName($operation->name) ?>(<?= Utils::renderParameters($operation->parameters) ?><? echo (count($operation->parameters) > 0) ? ', ' : ''; ?>proxy:<?= Utils::getASType($model->proxyClassName) ?>, setBusyStatus:Boolean=false)
		{
<? if (count($operation->parameters) > 0): ?>
<? foreach($operation->parameters as $name => $type): ?>
			this.<?= $name ?> = <?= $name ?>;
<? endforeach; ?>
<? endif; ?>
			this.proxy = proxy;
			super(setBusyStatus);
		}

		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------

		/**
		 * @see org.foomo.zugspitze.commands.ICommand
		 */
		public function execute():void
		{
			this._methodCall = this.proxy.<?= $operation->name ?>(<?= Utils::renderParameters($operation->parameters, false, true) ?>);
			this._methodCall.addEventListener(<?= $model->operationToMethodCallEventName($operation->name) ?>.<?= $model->toConstantName($operation->name) ?>_CALL_ERROR, this.abstractErrorHandler);
			this._methodCall.addEventListener(<?= $model->operationToMethodCallEventName($operation->name) ?>.<?= $model->toConstantName($operation->name) ?>_CALL_PROGRESS, this.abstractProgressHandler);
			this._methodCall.addEventListener(<?= $model->operationToMethodCallEventName($operation->name) ?>.<?= $model->toConstantName($operation->name) ?>_CALL_COMPLETE, this.abstractCompleteHandler);
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
			this._methodCall.addEventListener(<?= $model->toEventName($model->getVOClassName($dataClass)) ?>.<?= $model->toConstantName(lcfirst($model->getVOClassName($dataClass))) ?>, this.abstract<?= $model->getVOClassName($dataClass) ?>Handler);
<? endforeach; ?>
<? endif; ?>
		}

		/**
		 * @see org.foomo.zugspitze.core.IUnload
		 */
		public function unload():void
		{
			this.proxy = null;
<? if (count($operation->parameters) > 0): ?>
<? foreach ($operation->parameters as $name => $type): ?>
			this.<?= $name ?> = <?= Utils::getASTypeDefaultValue($type); ?>;
<? endforeach; ?>
<? endif; ?>
			if (this._methodCall) {
				this._methodCall.removeEventListener(<?= $model->operationToMethodCallEventName($operation->name) ?>.<?= $model->toConstantName($operation->name) ?>_CALL_ERROR, this.abstractErrorHandler);
				this._methodCall.removeEventListener(<?= $model->operationToMethodCallEventName($operation->name) ?>.<?= $model->toConstantName($operation->name) ?>_CALL_PROGRESS, this.abstractProgressHandler);
				this._methodCall.removeEventListener(<?= $model->operationToMethodCallEventName($operation->name) ?>.<?= $model->toConstantName($operation->name) ?>_CALL_COMPLETE, this.abstractCompleteHandler);
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
				this._methodCall.removeEventListener(<?= $model->toEventName($model->getVOClassName($dataClass)) ?>.<?= $model->toConstantName(lcfirst($model->getVOClassName($dataClass))) ?>, this.abstract<?= $model->getVOClassName($dataClass) ?>Handler);
<? endforeach; ?>
<? endif; ?>
				this._methodCall = null;
			}
		}

		//-----------------------------------------------------------------------------------------
		// ~ Protected eventhandler
		//-----------------------------------------------------------------------------------------

		/**
		 * Handle method call progress
		 *
		 * @param event Method call event
		 */
		protected function abstractProgressHandler(event:<?= $model->operationToMethodCallEventName($operation->name) ?>):void
		{
			// Overwrite this method in your implementation class
		}

		/**
		 * Handle method call result
		 *
		 * @param event Method call event
		 */
		protected function abstractCompleteHandler(event:<?= $model->operationToMethodCallEventName($operation->name) ?>):void
		{
			// Overwrite this method in your implementation class
			this.dispatchCommandCompleteEvent();
		}

		/**
		 * Handle method call error
		 *
		 * @param event Method call event
		 */
		protected function abstractErrorHandler(event:<?= $model->operationToMethodCallEventName($operation->name) ?>):void
		{
			// Overwrite this method in your implementation class
			this.dispatchCommandErrorEvent(event.error);
		}
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>

		/**
		 * Handle exception
		 *
		 * @param event Exception event
		 */
		protected function abstract<?= $model->getVOClassName($dataClass) ?>Handler(event:<?= $model->toEventName($model->getVOClassName($dataClass)) ?>):void
		{
			this.dispatchCommandErrorEvent(event.toString());
		}
<? endforeach; ?>
<? endif; ?>
	}
}