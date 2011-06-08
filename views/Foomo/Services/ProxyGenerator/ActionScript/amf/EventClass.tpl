<?
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */

$operation = $model->currentOperation;
if(!$operation->returnType) {
	trigger_error('no return type for ' . $model->currentOperation->name);
}
$className = $model->operationToEventClassName($operation->name);
?>package <?= $model->myPackage ?>.events {
	<?php echo $model->getAllClientClassImports(); ?>
	import flash.events.Event;
	import <?= $model->myPackage ?>.vo.*;
<?
if($operation->returnType) {
	echo $model->getClientAsClassImport($operation->returnType->type);
}
?>

	public class <?= $className ?> extends Event {
		public static const RESULT:String = '<?= $model->operationToEventResultName($operation->name) ?>';
		public static const FAULT:String  = '<?= $model->operationToEventFaultName($operation->name) ?>';
<? foreach($operation->throwsTypes as $throwsType): ?>
		public static const <?= $model->exceptionTypeToEventConstName($throwsType->type) ?>:String  = '<?= $model->operationExceptionName($operation->name, $throwsType->type) ?>';
		public var <?= $model->exceptionTypeToEventPropName($throwsType->type) ?>:<?= $model->typeToASType($throwsType->type) ?>;
<? endforeach; ?>
<? if($operation->returnType): ?>
		public var result:<?= $model->typeToASType($operation->returnType->type) ?>;
<? else: ?>
		public var result:void = void;
<? endif; ?>
		public var messages:Array;
		public function <?= $className ?>(type:String, result:*, messages:Array = null)
		{
			this.result = result as <?= $model->typeToASType($operation->returnType->type) ?>;
			if(messages) {
				this.messages = messages;
			} else {
				this.messages = [];
			}
			super(type);
		}
	}
}