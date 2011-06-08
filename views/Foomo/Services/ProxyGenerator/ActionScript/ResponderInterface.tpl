<?php
/* @var $model */
$operation = $model->currentOperation;
?>package <?php echo $model->myPackage ?>.responders 
{
	import mx.rpc.IResponder;
	import <?php echo $model->myPackage ?>.vo.*;
<?=	$model->getClientAsClassImport($model->currentOperation->returnType->type); ?> 
	
	public interface <?php echo $model->operationToResponderInterfaceName($model->currentOperation->name) ?>
	
	{
		/**
		 * Handle result
		 */
		function result(data:<?php echo $model->typeToASType($model->currentOperation->returnType->type) ?>):void;
		/**
		 * Handle fault
		 */
		function fault(info:Object):void;
		/**
		 * Handle messages
		 */
		function messages(messages:Array):void;
		/**
		 * Handle exception
		 */
		function exception(exception:Object):void;
	}
}