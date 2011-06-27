<?
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $dataClass ServiceObjectType */
/* @var $type ServiceObjectType */
/* @var $view Foomo\MVC\View */
use Foomo\Flash\ActionScript\PHPUtils;
use Foomo\Flash\ActionScript\ViewHelper;
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

<?= $view->indent(ViewHelper::renderComment((isset($dataClass->phpDocEntry) && !empty($dataClass->phpDocEntry->comment)) ? $dataClass->phpDocEntry->comment : ''), 1) . PHP_EOL; ?>
	public class <?= $model->getVOClassName($dataClass) . PHP_EOL ?>
	{
<?php if (count($dataClass->constants) > 0): ?>
		//-----------------------------------------------------------------------------------------
		// ~ Constants
		//-----------------------------------------------------------------------------------------

<?= $view->intend(ViewHelper::renderConstants($dataClass->constants), 2) . PHP_EOL ?>

<?php endif; ?>
<? if (count($dataClass->props) > 0): ?>
		//-----------------------------------------------------------------------------------------
		// ~ Variables
		//-----------------------------------------------------------------------------------------
<? foreach($dataClass->props as $name => $type): ?>

<?= $view->indent(ViewHelper::renderComment((isset($type->phpDocEntry) && !empty($type->phpDocEntry->comment)) ? $type->phpDocEntry->comment : ''), 2) . PHP_EOL; ?>
<? if ($type->isArrayOf): ?>
		[ArrayElementType("<?= PHPUtils::getASType($type->type); ?>")]
		public var <?= $name ?>:Array;
<? else: ?>
		public var <?= $name ?>:<?= PHPUtils::getASType($type->type); ?>;
<? endif; ?>
<? endforeach; ?>
<?php endif; ?>
	}
}