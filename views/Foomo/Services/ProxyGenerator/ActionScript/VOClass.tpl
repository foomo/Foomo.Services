<?
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $dataClass ServiceObjectType */
/* @var $type ServiceObjectType */
use Foomo\Services\ProxyGenerator\ActionScript\Utils;
$dataClass = $model->currentDataClass;
?>package <?= $dataClass->getRemotePackage() . PHP_EOL ?>
{
	[Bindable]
<? if ('' != $remoteClass = $dataClass->getRemoteClass()):?>
	// this class is "abstract" - use  <?= $remoteClass ?>

	// and copy this to <?= $remoteClass ?> [RemoteClass(alias="<?= $dataClass->type ?>")]
<? else: ?>
	[RemoteClass(alias='<?= $model->getVORemoteAliasName($dataClass) ?>')]
<? endif; ?>

<?= Utils::indentLines(Utils::renderComment((isset($dataClass->phpDocEntry) && !empty($dataClass->phpDocEntry->comment)) ? $dataClass->phpDocEntry->comment : ''), 1) . PHP_EOL; ?>
	public class <?= $model->getVOClassName($dataClass) . PHP_EOL ?>
	{
<?php if (count($dataClass->constants) > 0): ?>
		//-----------------------------------------------------------------------------------------
		// ~ Constants
		//-----------------------------------------------------------------------------------------

<?= Utils::renderConstants($dataClass->constants) . PHP_EOL ?>

<?php endif; ?>
<? if (count($dataClass->props) > 0): ?>
		//-----------------------------------------------------------------------------------------
		// ~ Variables
		//-----------------------------------------------------------------------------------------
<? foreach($dataClass->props as $name => $type): ?>

<?= Utils::indentLines(Utils::renderComment((isset($type->phpDocEntry) && !empty($type->phpDocEntry->comment)) ? $type->phpDocEntry->comment : ''), 2) . PHP_EOL; ?>
<? if ($type->isArrayOf): ?>
		[ArrayElementType("<?= Utils::getASType($type->type); ?>")]
		public var <?= $name ?>:Array;
<? else: ?>
		public var <?= $name ?>:<?= Utils::getASType($type->type); ?>;
<? endif; ?>
<? endforeach; ?>
<?php endif; ?>
	}
}