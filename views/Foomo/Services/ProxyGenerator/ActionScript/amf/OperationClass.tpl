<?
/* @var $model RPCServiceASProxyRenderer */
/* @var $operation ServiceOperation */
$operation = $model->currentOperation;
$className = $model->operationToOperationName($operation->name);
$eventClassName = $model->operationToEventClassName($operation->name);
$eventClassImport = $model->myPackage . '.events.' . $eventClassName;

?>package <?= $model->myPackage ?>.model 
{
	import flash.events.EventDispatcher;
	import mx.collections.ArrayCollection;
	import com.bestbytes.zugspitze.services.rpc.RPCMethodCallToken;
	import com.bestbytes.zugspitze.services.rpc.events.RPCMethodCallTokenEvent;
	import <?= $model->myPackage ?>.*;
	import <?= $model->myPackage ?>.vo.*;
	import <?= $model->myPackage ?>.events.<?= $eventClassName ?>;
	import <?= $model->myPackage ?>.responders.<?= $model->operationToResponderInterfaceName($operation->name) ?>;

	<?= $model->getAllClientClassImports() ; ?>

	[Event(name="<?= $model->operationToEventResultName($operation->name) ?>", type="<?= $eventClassImport ?>")]
	[Event(name="<?= $model->operationToEventFaultName($operation->name) ?>", type="<?= $eventClassImport ?>")]

	public class <?= $className ?> extends EventDispatcher 
	{
		//-----------------------------------------------------------------------------------------
		// ~ Variables
		//-----------------------------------------------------------------------------------------

		/**
		 * Command is pending
		 */
		[Bindable]
		public var pending:Boolean = false;
<? foreach($operation->parameters as $parameterName => $parameterType): ?>
		/**
		 * 
		 */
		public var <?= $parameterName ?>:<?= $model->typeToASType($parameterType) ?>;
<? endforeach; ?>
		/**
		 * Last opertion result
		 */
		[Bindable]
		public var lastResult:<?= $model->typeToASType($operation->returnType->type) ?>;
		/**
		 * Last messages
		 */
		[Bindable]
		public var lastMessages:Array;
		/**
		 * Service proxy
		 */
		public var proxy:<?= $model->typeToASType($model->proxyClassName) ?>;
		/**
		 * Service responders
		 */
		protected var _responders:Object = new Object;
		
		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------
		
		/**
		 * 
		 */
		public function send(responder:<?= $model->operationToResponderInterfaceName($model->currentOperation->name)  ?>=null):RPCMethodCallToken
		{
			this.pending = true;
			var token:RPCMethodCallToken = this.proxy.rpcClient.addMethodCall('<?= $operation->name; ?>', [<? $parms = array(); foreach($operation->parameters as $parameterName => $parameterType) { $parms[] = 'this.' . $parameterName; }; echo implode(', ', $parms); ?>]);
			if (responder) this._responders[token.methodCallId] = responder;
			token.addEventListener(RPCMethodCallTokenEvent.METHOD_CALL_COMPLETE, this.token_methodCallCompleteHandler);
			token.addEventListener(RPCMethodCallTokenEvent.METHOD_CALL_FAULT, this.token_methodCallFaultHandler);
			this.proxy.rpcClient.sendCall();
			return token;
		}
		
		//-----------------------------------------------------------------------------------------
		// ~ Protected eventhandlers
		//-----------------------------------------------------------------------------------------

		/**
		 * 
		 */
		protected function token_methodCallCompleteHandler(event:RPCMethodCallTokenEvent):void
		{
			this.pending = false;
			
			var token:RPCMethodCallToken = RPCMethodCallToken(event.currentTarget);
			token.removeEventListener(RPCMethodCallTokenEvent.METHOD_CALL_COMPLETE, this.token_methodCallCompleteHandler);
			token.removeEventListener(RPCMethodCallTokenEvent.METHOD_CALL_FAULT, this.token_methodCallFaultHandler);
		
			if (this._responders[event.methodReply.id]) {
				var responder:<?= $model->operationToResponderInterfaceName($operation->name) ?> = this._responders[event.methodReply.id];

				this._responders[event.methodReply.id] = null;
				this.lastMessages = event.methodReply.messages;
				responder.messages(this.lastMessages);
				if (event.methodReply.exception) {
					// TODO: should the lastResult be nulled
					// this.lastResult = null;
					var exceptionEvent:<?= $eventClassName ?>;
<? foreach($operation->throwsTypes as $throwsType): ?>

					if (event.methodReply.exception is <?= $model->typeToASType($throwsType->type) ?>) {
						exceptionEvent = new <?= $eventClassName ?>(<?= $eventClassName ?>.<?= $model->exceptionTypeToEventConstName($throwsType->type) ?>, null, this.lastMessages);
						exceptionEvent.<?= $model->exceptionTypeToEventPropName($throwsType->type) ?> = event.methodReply.exception as <?= $model->typeToASType($throwsType->type) ?>;
						this.dispatchEvent(exceptionEvent);
						responder.exception(event.methodReply.exception);
						this.dispatchEvent(new <?= $eventClassName ?>(<?= $eventClassName ?>.FAULT, null, this.lastMessages));
						return;
					}
<? endforeach; ?>
					throw new Error('unhandled exception ' + event.methodReply.exception);
				} else {
					this.lastResult = event.methodReply.value as <?= $model->typeToASType($operation->returnType->type) ?>;
					this.dispatchEvent(new <?= $eventClassName ?>(<?= $eventClassName ?>.RESULT, this.lastResult, this.lastMessages));
					responder.result(this.lastResult);
				}
			}
		}
		
		/**
		 * 
		 */
		protected function token_methodCallFaultHandler(event:RPCMethodCallTokenEvent):void
		{
			this.pending = false;
			
			var token:RPCMethodCallToken = RPCMethodCallToken(event.currentTarget);
			token.removeEventListener(RPCMethodCallTokenEvent.METHOD_CALL_COMPLETE, this.token_methodCallCompleteHandler);
			token.removeEventListener(RPCMethodCallTokenEvent.METHOD_CALL_FAULT, this.token_methodCallFaultHandler);

			this.dispatchEvent(new <?= $eventClassName ?>(<?= $eventClassName ?>.FAULT, null, null));
			if(this._responders[event.methodCall.id]) {
				var responder:<?= $model->operationToResponderInterfaceName($operation->name) ?> = this._responders[event.methodCall.id];
				responder.fault(null);
				this._responders[event.methodCall.id] = null;
			}
		}
	}
}