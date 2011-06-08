<?
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $propType ServiceObjectType */
?><? if('' != $remotePackage = $model->currentDataClass->getRemotePackage()): ?>
package <?= $remotePackage ?> 
{
	import <?= $model->myPackage ?>.vo.*
<? else: ?>
package <?= $model->myPackage ?>.vo {
<? endif; ?>
	<?= $model->getAllClientClassImports(); ?>

<? if(isset($model->currentDataClass->phpDocEntry) && !empty($model->currentDataClass->phpDocEntry->comment)): ?>
<?= Foomo\Services\ProxyGenerator\ActionScript\Utils::renderComment($model->currentDataClass->phpDocEntry->comment, 1); ?>
<? endif; ?>
<? if('' != $remoteClass = $model->currentDataClass->getRemoteClass()):?>
	// this class is "abstract" - use  <?= $remoteClass ?>
	
	// and copy this to <?= $remoteClass ?> [RemoteClass(alias="<?= $model->currentDataClass->type ?>")]
<? else: ?>
	[RemoteClass(alias='<?= $model->getVORemoteAliasName($model->currentDataClass) ?>')]
<? endif; ?>	
	[Bindable]
	public class <?= $model->getVOClassName($model->currentDataClass) ?> 
	{
		//-----------------------------------------------------------------------------------------
		// ~ Variables
		//-----------------------------------------------------------------------------------------
<? foreach($model->currentDataClass->constants as $constName => $constValue): ?>
	<?
		switch(gettype($constValue)) {
			case 'bool':
			case 'boolean':
				$constType = 'Boolean';
				$constValue = ($constValue) ? 'true' : 'false';
				break;
			case 'int':
			case 'integer':
				$constType = 'int';
				break;
			case 'float':
			case 'double':
				$constType = 'Number';
				break;
			default:
				$constType = 'String';
				$constValue = "'" . $constValue . "'";
				break;
		}
	?>
	public static const <?=  $constName ; ?>:<?= $constType ?> = <?= $constValue ?>;
<? endforeach; ?>
<? foreach($model->currentDataClass->props as $propName => $propType): ?>
<? if(isset($propType->phpDocEntry) && !empty($propType->phpDocEntry->comment)): ?>
<?= Foomo\Services\ProxyGenerator\ActionScript\Utils::renderComment($propType->phpDocEntry->comment, 2); ?>
<? endif; ?>
		public var <?= $propName ?>:<? if($propType->isArrayOf) { echo 'Array'; } else { echo	$model->typeToASType($propType->type); } ;?>;
<? endforeach; ?>
	}
}