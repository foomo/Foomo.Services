<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */

$operation = $model->currentOperation;
$className = $model->operationToEventClassName($operation->name);
if(!$operation->returnType) {
	trigger_error('no return type for ' . $model->currentOperation->name);
}
?>package <?php echo $model->myPackage ?>.events {

	import flash.events.Event;
	import mx.rpc.events.FaultEvent;
	import <?php echo $model->myPackage ?>.vo.*;
<?php
if($operation->returnType) {
	echo $model->getClientAsClassImport($operation->returnType->type);
}
?>

	public class <?php echo $className ?> extends Event {
		public static const RESULT:String = '<?php echo $model->operationToEventResultName($operation->name) ?>';
		public static const FAULT:String  = '<?php echo $model->operationToEventFaultName($operation->name) ?>';
<? foreach($operation->throwsTypes as $throwsType): ?>
		public static const EXCEPTION_<?= $model->typeToUpper($throwsType->type) ?>:String  = '<?= $model->operationExceptionName($operation->name, $excetionType->type) ?>';
		public var exception<?= ucfirst($throwsType->type) ?>:<?= $throwsType->type ?>;
<? endforeach; ?>
<?php if($operation->returnType): ?>
		public var result:<?php echo $model->typeToASType($operation->returnType->type) ?>;
<?php else: ?>
		public var result:void = void;
<?php endif; ?>

		public var result:<?php echo $model->typeToASType($operation->returnType->type) ?>;
		public var fault:FaultEvent;
		public function <?php echo $className ?>(type:String, result:*, fault:FaultEvent = null)
		{
			this.result = result as <?php echo $model->typeToASType($operation->returnType->type) ?>;
			this.fault = fault;
			super(type);
		}
	}
}