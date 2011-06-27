<?
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $dataClass ServiceObjectType */
/* @var $type ServiceObjectType */
/* @var $view Foomo\MVC\View */
use Foomo\Flash\ActionScript\PHPUtils;
use Foomo\Flash\ActionScript\ViewHelper;
$dataClass = $model->currentDataClass;
?>package <?= $model->myPackage; ?>.events
{
	import flash.events.Event;

<?= $view->indent(ViewHelper::renderComment((isset($dataClass->phpDocEntry) && !empty($dataClass->phpDocEntry->comment)) ? $dataClass->phpDocEntry->comment : ''), 1) . PHP_EOL; ?>
	public class <?= ViewHelper::toClassName($model->getVOClassName($dataClass), 'Event') ?> extends Event
	{
		//-----------------------------------------------------------------------------------------
		// ~ Constants
		//-----------------------------------------------------------------------------------------

		public static const <?= ViewHelper::toConstantName($model->getVOClassName($dataClass)) ?>:String = "<?= lcfirst($model->getVOClassName($dataClass)) ?>";

<? if (count($dataClass->props) > 0): ?>
		//-----------------------------------------------------------------------------------------
		// ~ Variables
		//-----------------------------------------------------------------------------------------
<? foreach($dataClass->props as $name => $type): ?>

<?= $view->indent(ViewHelper::renderComment((isset($type->phpDocEntry) && !empty($type->phpDocEntry->comment)) ? $type->phpDocEntry->comment : ''), 2) . PHP_EOL; ?>
<? if ($type->isArrayOf): ?>
		[ArrayElementType("<?= PHPUtils::getASType($type->type); ?>")]
		private var _<?= $name ?>:Array;
<? else: ?>
		private var _<?= $name ?>:<?= PHPUtils::getASType($type->type); ?>;
<? endif; ?>
<? endforeach; ?>
<?php endif; ?>

		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------

		public function <?= ViewHelper::toClassName($model->getVOClassName($dataClass), 'Event') ?>(type:String, <?= ViewHelper::renderProperties($dataClass->props) ?>)
		{
<? if (count($dataClass->props) > 0): ?>
<? foreach($dataClass->props as $name => $type): ?>
			this._<?= $name ?> = <?= $name ?>;
<? endforeach; ?>
			super(type);
		}
<?php endif; ?>

<? if (count($dataClass->props) > 0): ?>
		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------
<? foreach($dataClass->props as $name => $type): ?>

<?= $view->indent(ViewHelper::renderComment((isset($type->phpDocEntry) && !empty($type->phpDocEntry->comment)) ? $type->phpDocEntry->comment : ''), 2) . PHP_EOL; ?>
		public function get <?= $name ?>():<?= (($type->isArrayOf) ? 'Array' : PHPUtils::getASType($type->type)) . PHP_EOL ?>
		{
			return this._<?= $name ?>;
		}
<? endforeach; ?>
<?php endif; ?>

		//-----------------------------------------------------------------------------------------
		// ~ Overriden methods
		//-----------------------------------------------------------------------------------------

		/**
		 * @inherit
		 */
		override public function clone():Event
		{
			return new <?= ViewHelper::toClassName($model->getVOClassName($dataClass), 'Event') ?>(this.type, <?= ViewHelper::renderProperties($dataClass->props, false, true) ?>);
		}

		/**
		 * @inherit
		 */
		override public function toString():String
		{
			return formatToString("<?= ViewHelper::toClassName($model->getVOClassName($dataClass), 'Event') ?>");
		}
	}
}