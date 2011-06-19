<?
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $dataClass ServiceObjectType */
/* @var $type ServiceObjectType */
use Foomo\Services\ProxyGenerator\ActionScript\Utils;
$dataClass = $model->currentDataClass;
?>package <?= $model->myPackage; ?>.events
{
	import flash.events.Event;

<?= Utils::indentLines(Utils::renderComment((isset($dataClass->phpDocEntry) && !empty($dataClass->phpDocEntry->comment)) ? $dataClass->phpDocEntry->comment : ''), 1) . PHP_EOL; ?>
	public class <?= $model->toEventName($model->getVOClassName($dataClass)) ?> extends Event
	{
		//-----------------------------------------------------------------------------------------
		// ~ Constants
		//-----------------------------------------------------------------------------------------

		public static const <?= $model->toConstantName(lcfirst($model->getVOClassName($dataClass))) ?>:String = "<?= lcfirst($model->getVOClassName($dataClass)) ?>";

<? if (count($dataClass->props) > 0): ?>
		//-----------------------------------------------------------------------------------------
		// ~ Variables
		//-----------------------------------------------------------------------------------------
<? foreach($dataClass->props as $name => $type): ?>

<?= Utils::indentLines(Utils::renderComment((isset($type->phpDocEntry) && !empty($type->phpDocEntry->comment)) ? $type->phpDocEntry->comment : ''), 2) . PHP_EOL; ?>
<? if ($type->isArrayOf): ?>
		[ArrayElementType("<?= Utils::getASType($type->type); ?>")]
		private var _<?= $name ?>:Array;
<? else: ?>
		private var _<?= $name ?>:<?= Utils::getASType($type->type); ?>;
<? endif; ?>
<? endforeach; ?>
<?php endif; ?>

		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------

		public function <?= $model->toEventName($model->getVOClassName($dataClass)) ?>(type:String, <?= Utils::renderProperties($dataClass->props) ?>)
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

<?= Utils::indentLines(Utils::renderComment((isset($type->phpDocEntry) && !empty($type->phpDocEntry->comment)) ? $type->phpDocEntry->comment : ''), 2) . PHP_EOL; ?>
		public function get <?= $name ?>():<?= (($type->isArrayOf) ? 'Array' : Utils::getASType($type->type)) . PHP_EOL ?>
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
			return new <?= $model->toEventName($model->getVOClassName($dataClass)) ?>(this.type, <?= Utils::renderProperties($dataClass->props, false, true) ?>);
		}

		/**
		 * @inherit
		 */
		override public function toString():String
		{
			return formatToString("<?= $model->toEventName($model->getVOClassName($dataClass)) ?>");
		}
	}
}