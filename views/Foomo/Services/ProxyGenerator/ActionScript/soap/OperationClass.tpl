<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
$operation = $model->currentOperation;
$className = $model->operationToOperationName($operation->name);
$eventClassName = $model->operationToEventClassName($operation->name);
$eventClassImport = $model->myPackage . '.events.' . $eventClassName;

?>package <?php echo $model->myPackage ?>.model {

	import mx.rpc.events.ResultEvent;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.soap.Operation;
	import flash.events.EventDispatcher;
	import mx.rpc.AsyncToken;
	import mx.rpc.IResponder;
	import mx.collections.ArrayCollection;
	import com.bestbytes.zugspitze.services.ResponseRouter;
	import com.bestbytes.zugspitze.services.ITranslator;

	import <?php echo $model->myPackage ?>.*;
	import <?php echo $model->myPackage ?>.vo.*;
	import <?php echo $model->myPackage ?>.events.<?php echo $model->operationToEventClassName($operation->name) ?>;
	import <?php echo $model->myPackage ?>.responders.<?php echo $model->operationToResponderInterfaceName($operation->name) ?>;

	[Event(name="<?php echo $model->operationToEventResultName($operation->name) ?>", type="<?php echo $eventClassImport ?>")]
	[Event(name="<?php echo $model->operationToEventFaultName($operation->name) ?>", type="<?php echo $eventClassImport ?>")]

	public class <?php echo $className ?> extends EventDispatcher implements ITranslator {
		[Bindable]
		public var pending:Boolean = false;
<?php foreach($operation->parameters as $parameterName => $parameterType): ?>
		[Bindable]
		public var <?php echo $parameterName ?>:<?php echo $model->typeToASType($parameterType) ?>;
<?php endforeach; ?>
		[Bindable]
		public var lastResult:<?php echo $model->typeToASType($operation->returnType->type) ?>;

		internal var operation:Operation;

		public var proxy:<?php echo $model->proxyClassName ?>;

		public function handleFault(fault:FaultEvent):void
		{
			this.pending = false;
			this.dispatchEvent(new <?php echo $eventClassName ?>(<?php echo $eventClassName ?>.FAULT, null, fault));
		}
		public function handleResult(event:ResultEvent):void
		{
            this.lastResult = this.translateData(event.result) as <?php echo $model->typeToASType($operation->returnType->type) ?>;
			this.pending = false;
            this.dispatchEvent(new <?php echo $eventClassName ?>(<?php echo $eventClassName ?>.RESULT, this.lastResult));
		}
		public function translateData(data:Object):Object
		{
			var result:<?php echo $model->typeToASType($operation->returnType->type) ?>;
            <?php if(isset($model->complexTypes[str_replace('[]', '',$operation->returnType->type)])): ?>
              <?php if($model->typeToASType($operation->returnType->type) == 'Array'): ?>result = new Array;
				this.proxy.webServiceHelper.helpWith<?php echo ucfirst(str_replace('[]', '',$operation->returnType->type)) ?>(data, result, this.proxy.<?php echo $model->complexTypeToImplClassName($operation->returnType) ?>);
              <?php else: ?>
              	result = new this.proxy.<?php echo $model->complexTypeToImplClassName($operation->returnType);  ?> as <?php echo $model->typeToASType($operation->returnType->type) ?>;
              	this.proxy.webServiceHelper.helpWith<?php echo ucfirst(str_replace('[]', '',$operation->returnType->type)) ?>(data, result, this.proxy.<?php echo $model->complexTypeToImplClassName($operation->returnType) ?>);
              <?php endif; ?>
              <?php elseif($model->typeToASType($operation->returnType->type) == 'Array'): ?>
              result = data.toArray();
            <?php else: ?>
            	result = data as <?php echo $model->typeToASType($operation->returnType->type) ?>;
            <?php endif; ?>
            return result;
		}
		public function send(responder:<?php echo $model->operationToResponderInterfaceName($model->currentOperation->name)  ?> = null):AsyncToken
		{
			this.pending = true;
			var token:AsyncToken = this.operation.send(<?php $parms = array(); foreach($operation->parameters as $parameterName => $parameterType) { $parms[] = 'this.' . $parameterName; }; echo implode(', ', $parms); ?>);
			if(responder) {
				ResponseRouter.route(token, this, responder);
			}
			return token;
		}
	}
}