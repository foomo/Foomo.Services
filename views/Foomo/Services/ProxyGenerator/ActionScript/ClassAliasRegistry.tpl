<?
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $operation Foomo\Services\Reflection\ServiceOperation */
/* @var $complexType Foomo\Services\Reflection\ServiceObjectType */
?>package <?= $model->myPackage ?>.model {

	import flash.net.registerClassAlias;
<? foreach($model->complexTypes as $complexType): ?>
	import <?= (($remotePackage = $complexType->getRemotePackage())?$remotePackage.'.':'') . $model->typeToASType($complexType->type) ?>;
<? endforeach; ?>

	public class ClassAliasRegistry 
	{
		//-----------------------------------------------------------------------------------------
		// ~ Public static methods
		//-----------------------------------------------------------------------------------------
	
		/**
		 *
		 */
		public static function registerClassAliases():void
		{
<? foreach($model->complexTypes as $complexType): ?>
			registerClassAlias('<?= $model->getVORemoteAliasName($complexType) ?>', <?= $model->getVOClassName($complexType) ?>);
<? endforeach; ?>
		}
	}
}