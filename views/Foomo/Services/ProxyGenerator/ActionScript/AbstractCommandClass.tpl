<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
/* @var $view Foomo\MVC\View */
use Foomo\Flash\ActionScript\PHPUtils;
use Foomo\Flash\ActionScript\ViewHelper;
$operation = $model->currentOperation;
?>package <?= $model->myPackage; ?>.commands
{
	import <?= $model->myPackage; ?>.<?= PHPUtils::getASType($model->proxyClassName) ?>;
	import <?= $model->myPackage; ?>.calls.<?= ViewHelper::toClassName($operation->name, 'Call') ?>;
	import <?= $model->myPackage; ?>.events.<?= ViewHelper::toClassName($operation->name, 'CallEvent') ?>;
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
	import <?= $model->myPackage; ?>.events.<?= ViewHelper::toClassName($model->getVOClassName($dataClass), 'Event') ?>;
<? endforeach; ?>
<? endif; ?>
<? if (count($operation->parameters) > 0): ?>
<? foreach($operation->parameters as $name => $type): ?>
	<? if (!PHPUtils::isASStandardType($type)) echo $model->getClientAsClassImport($type); ?>
<? endforeach; ?>
<? endif; ?>

	import org.foomo.zugspitze.commands.Command;
	import org.foomo.zugspitze.commands.ICommand;
	import org.foomo.zugspitze.core.IUnload;

	/**
	 * Create your own command instance and override the protected event handlers
	 */
	public class <?= ViewHelper::toClassName($operation->name, 'Command', 'Abstract') ?> extends Command implements ICommand, IUnload
	{
		//-----------------------------------------------------------------------------------------
		// ~ Variables
		//-----------------------------------------------------------------------------------------

		/**
		 * Service proxy
		 */
		public var proxy:<?= PHPUtils::getASType($model->proxyClassName) ?>;
<? foreach ($operation->parameters as $name => $type): ?>
<?= $view->indent(ViewHelper::renderComment(isset($operation->parameterDocs[$name]) ? $operation->parameterDocs[$name]->comment : ''), 2) . PHP_EOL ?>
		public var <?= $name ?>:<?= PHPUtils::getASType($type) ?>;
<? endforeach; ?>
		/**
		 * Returned call from the proxy
		 */
		protected var _methodCall:<?= ViewHelper::toClassName($operation->name, 'Call') ?>;

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
		public function <?= ViewHelper::toClassName($operation->name, 'Command', 'Abstract') ?>(<?= ViewHelper::renderParameters($operation->parameters) ?><? echo (count($operation->parameters) > 0) ? ', ' : ''; ?>proxy:<?= PHPUtils::getASType($model->proxyClassName) ?>, setBusyStatus:Boolean=false)
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
			this._methodCall = this.proxy.<?= $operation->name ?>(<?= ViewHelper::renderParameters($operation->parameters, false, true) ?>);
			this._methodCall.addEventListener(<?= ViewHelper::toClassName($operation->name, 'CallEvent') ?>.<?= ViewHelper::toConstantName($operation->name) ?>_CALL_ERROR, this.abstractErrorHandler);
			this._methodCall.addEventListener(<?= ViewHelper::toClassName($operation->name, 'CallEvent') ?>.<?= ViewHelper::toConstantName($operation->name) ?>_CALL_PROGRESS, this.abstractProgressHandler);
			this._methodCall.addEventListener(<?= ViewHelper::toClassName($operation->name, 'CallEvent') ?>.<?= ViewHelper::toConstantName($operation->name) ?>_CALL_COMPLETE, this.abstractCompleteHandler);
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
			this._methodCall.addEventListener(<?= ViewHelper::toClassName($model->getVOClassName($dataClass), 'Event') ?>.<?= ViewHelper::toConstantName($model->getVOClassName($dataClass)) ?>, this.abstract<?= $model->getVOClassName($dataClass) ?>Handler);
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
			this.<?= $name ?> = <?= PHPUtils::getASTypeDefaultValue($type); ?>;
<? endforeach; ?>
<? endif; ?>
			if (this._methodCall) {
				this._methodCall.removeEventListener(<?= ViewHelper::toClassName($operation->name, 'CallEvent') ?>.<?= ViewHelper::toConstantName($operation->name) ?>_CALL_ERROR, this.abstractErrorHandler);
				this._methodCall.removeEventListener(<?= ViewHelper::toClassName($operation->name, 'CallEvent') ?>.<?= ViewHelper::toConstantName($operation->name) ?>_CALL_PROGRESS, this.abstractProgressHandler);
				this._methodCall.removeEventListener(<?= ViewHelper::toClassName($operation->name, 'CallEvent') ?>.<?= ViewHelper::toConstantName($operation->name) ?>_CALL_COMPLETE, this.abstractCompleteHandler);
<? if (count($operation->throwsTypes) > 0): ?>
<? foreach ($operation->throwsTypes as $throwType): ?>
<? $dataClass = $model->complexTypes[$throwType->type]; ?>
				this._methodCall.removeEventListener(<?= ViewHelper::toClassName($model->getVOClassName($dataClass), 'Event') ?>.<?= ViewHelper::toConstantName($model->getVOClassName($dataClass)) ?>, this.abstract<?= $model->getVOClassName($dataClass) ?>Handler);
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
		protected function abstractProgressHandler(event:<?= ViewHelper::toClassName($operation->name, 'CallEvent') ?>):void
		{
			// Overwrite this method in your implementation class
		}

		/**
		 * Handle method call result
		 *
		 * @param event Method call event
		 */
		protected function abstractCompleteHandler(event:<?= ViewHelper::toClassName($operation->name, 'CallEvent') ?>):void
		{
			// Overwrite this method in your implementation class
			this.dispatchCommandCompleteEvent();
		}

		/**
		 * Handle method call error
		 *
		 * @param event Method call event
		 */
		protected function abstractErrorHandler(event:<?= ViewHelper::toClassName($operation->name, 'CallEvent') ?>):void
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
		protected function abstract<?= $model->getVOClassName($dataClass) ?>Handler(event:<?= ViewHelper::toClassName($model->getVOClassName($dataClass), 'Event') ?>):void
		{
			this.dispatchCommandErrorEvent(event.toString());
		}
<? endforeach; ?>
<? endif; ?>
	}
}