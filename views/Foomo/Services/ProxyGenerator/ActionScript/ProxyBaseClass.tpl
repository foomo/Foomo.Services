<?
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation ServiceOperation */
$operationVarNames = array();
$eventMap = array(
  'handleResult' => 'ResultEvent.RESULT',
  'handleFault'  => 'FaultEvent.FAULT'
);
foreach($model->operations as $operation) {
  $operationVarNames[$model->operationToOperationName($operation->name)] = $model->operationToOperationVarName($operation->name);
}
$complexTypes = array();
foreach($model->complexTypes as $complexType) {
	/* @var $complexType ServiceObjectType */
	$complexTypes[] = $model->typeToASType($complexType->type);
}
?>package <?= $model->myPackage ?>.model 
{
	import flash.events.EventDispatcher;
	import <?= $model->myPackage ?>.vo.*;
	
	<?= $model->getAllClientClassImports() ?>
	
	[Event(name='load', type='mx.rpc.soap.LoadEvent')]
	
	public class <?= $model->typeToASType($model->proxyBaseClassName) ?> extends EventDispatcher 
	{
		//-----------------------------------------------------------------------------------------
		// ~ Variables
		//-----------------------------------------------------------------------------------------

<? foreach($operationVarNames as $varType => $varName): ?>
		/**
		 *
		 */
		[Bindable]
		public var <?= $varName ?>:<?= $varType ?>;
<? endforeach; ?>

		//-----------------------------------------------------------------------------------------
		// ~ Public static methods
		//-----------------------------------------------------------------------------------------
		
		/**
		 * Forcing the compiler to include "vectors".
		 * All vector base classes have to be loaded here.
		 */
		public static function forceCompileVectors():void
		{
			var vectors:Array = [<?= implode(', ', $complexTypes) ?>];
		}
	}
}