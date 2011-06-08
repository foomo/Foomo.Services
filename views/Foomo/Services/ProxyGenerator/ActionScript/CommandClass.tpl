<?php
/* @var $model ServiceProxyGeneratorBase */
$operation = $model->currentOperation;
$eventClassName = $model->operationToEventClassName($model->currentOperation->name);
$eventClassImport = $model->myPackage . '.events.' . $eventClassName;

$executeParms = array();
$opVar = 'this._proxy.' . $model->operationToOperationVarName($model->currentOperation->name);
$opComments = '';
if(count($model->currentOperation->parameters) > 0) {
	foreach($model->currentOperation->parameters as $parameterName => $parameterType) {
		$executeParms[] = $parameterName . ':' . $model->typeToASType($parameterType);
		if(!empty($model->currentOperation->parameterDocs[$parameterName]->comment)) {
			$opComments .= '		  * @param ' . $parameterName . ' ' . $model->currentOperation->parameterDocs[$parameterName]->comment . PHP_EOL;
		}
	}
	if(!empty($opComments)) {
		$opComments = '		 /**' . PHP_EOL . $opComments . '	 	  */' . PHP_EOL;
	}
}
$executeParms[] = 'proxy:' . $model->typeToASType($model->proxyClassName) . '=null';
$executeParms[] = 'setBusyStatus:Boolean=false';
$executeParms = implode(', ', $executeParms);

$implementations = array('ICommand');
if(method_exists($model, 'operationToResponderInterfaceName')) {
	$implementations[] = $model->operationToResponderInterfaceName($operation->name);
}

?>package <?= $model->myPackage ?>.commands 
{
	import flash.events.EventDispatcher;
	import <?= $model->myPackage ?>.*;
	import <?= $model->myPackage ?>.vo.*;
	import <?= $model->myPackage ?>.events.<?= $model->operationToEventClassName($operation->name) ?>;
<? if(method_exists($model, 'operationToResponderInterfaceName')): ?>
	import <?= $model->myPackage ?>.responders.<?= $model->operationToResponderInterfaceName($operation->name) ?>;
<? endif; ?>
	import <?= $model->myPackage ?>.model.<?= $model->typeToASType($model->proxyClassName) ?>;
	import com.bestbytes.zugspitze.commands.ICommand;
	import com.bestbytes.zugspitze.events.CommandEvent;
	import com.bestbytes.zugspitze.commands.Command;
	
	<?= $model->getAllClientClassImports() ; ?>

	[Event(name="<?= $model->operationToEventResultName($operation->name);?>", type="<?= $eventClassImport ?>")]
	[Event(name="<?= $model->operationToEventFaultName($operation->name);?>", type="<?= $eventClassImport ?>")]
<? foreach($operation->throwsTypes as $throwsType): ?>
	[Event(name="<?= $model->operationExceptionName($operation->name, $throwsType->type) ?>", type="<?= $eventClassImport ?>")]
<? endforeach; ?>
<? if(!empty($operation->comment)): ?>
<?= Foomo\Services\ProxyGenerator\ActionScript\Utils::renderComment($operation->comment, 1); ?>
<? endif; ?>
	public class <?= $model->operationToCommandName($model->currentOperation->name) ?> extends Command implements <?= implode(', ', $implementations) ?> 
	{
		//-----------------------------------------------------------------------------------------
		// ~ Variables
		//-----------------------------------------------------------------------------------------
		
		/**
		 * Sevice proxy
		 */
		private var _proxy:<?= $model->typeToASType($model->proxyClassName) ?>;
		/**
		 * Last service message
		 */
		[Bindable] 
		public var lastMessages:Array;
		/**
		 * Last service result
		 */
		[Bindable]
		public var lastResult:<?= $model->typeToASType($model->currentOperation->returnType->type) ?>;
<? foreach($model->currentOperation->parameters as $parameterName => $parameterType): ?>
		/**
		 * 
		 */
		public var parameter<?= ucfirst($parameterName) ?>:<?= $model->typeToASType($parameterType) ?>;
<? endforeach; ?>
		
		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------
		
<?= $opComments ?>
		public function <?= $model->operationToCommandName($model->currentOperation->name) ?>(<?=	$executeParms; ?>)
		{
			super(setBusyStatus);
			this._proxy = (proxy) ? proxy : <?= $model->typeToASType($model->proxyClassName) ?>.defaultInstance; 
<? foreach($model->currentOperation->parameters as $parameterName => $parameterType): ?>
			this.parameter<?= ucfirst($parameterName) ?> = <?= $parameterName ?>;
<? endforeach; ?>
		}
		
		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------
		
		/**
		 * @inherit
		 */
		public function execute():void
		{
<? foreach($model->currentOperation->parameters as $parameterName => $parameterType): ?>
			<?= $opVar .'.' . $parameterName ?> = this.parameter<?= ucfirst($parameterName) ?>;
<? endforeach; ?>
			<?= $opVar ?>.send(this);
		}

		/**
		 * @inherit
		 */
		public function result(data:<?= $model->typeToASType($model->currentOperation->returnType->type) ?>):void
		{
			this.lastResult = <?= $opVar ?>.lastResult = data;
			this.dispatchEvent(new <?= $eventClassName ?>(<?= $eventClassName ?>.RESULT, data, this.lastMessages));
			this.dispatchEvent(new CommandEvent(CommandEvent.COMMAND_COMPLETE, this));
		}

		/**
		 * @inherit
		 */
		public function fault(info:Object):void
		{
			this.dispatchEvent(new CommandEvent(CommandEvent.COMMAND_FAULT, this));
			this.dispatchEvent(new <?= $eventClassName ?>(<?= $eventClassName ?>.FAULT, null, null));
		}
		
		/**
		 * @inherit
		 */
		public function messages(messages:Array):void
		{
			this.lastMessages = messages;
		}
		
		/**
		 * @inherit
		 */
		public function exception(exception:Object):void
		{
<? if (count($operation->throwsTypes)): ?>			
			var exceptionEvent:<?= $eventClassName ?>;
			switch(true) {
<? foreach($operation->throwsTypes as $throwsType): ?>
				case (exception is <?= $model->typeToASType($throwsType->type) ?>):
					exceptionEvent = new <?= $eventClassName ?>(<?= $eventClassName ?>.<?= $model->exceptionTypeToEventConstName($throwsType->type) ?>, null, this.lastMessages);
					exceptionEvent.<?= $model->exceptionTypeToEventPropName($throwsType->type) ?> = exception as <?= $model->typeToASType($throwsType->type) ?>;
					break
<? endforeach; ?>
			}
			if (exceptionEvent) this.dispatchEvent(exceptionEvent);
<? endif; ?>
			this.dispatchCommandCompleteEvent();
		}
	}
}