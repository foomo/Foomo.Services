<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
/* @var $complexType ServiceObjectType */

?>package <?php echo $model->myPackage ?>.model {
	import <?php echo $model->myPackage ?>.vo.*;
	import mx.collections.ArrayCollection;
	
	<?php echo $model->getAllClientClassImports() ?>

	internal class WebServiceHelper {
	  public var proxy:<?php echo $model->proxyClassName ?>;
	  public function WebServiceHelper(proxy:<?php echo $model->proxyClassName ?>)
	  {
	  	  this.proxy = proxy;
	  }
<?php foreach($model->complexTypes as $complexType): ?>
		public function helpWith<?php echo $complexType->type ?>(wsResult:Object, target:Object, impl:Class):void
		{
		    if(wsResult) {
				if(wsResult is ArrayCollection) {
					this.translateArrayCollection(wsResult as ArrayCollection, this.helpWith<?php echo $complexType->type ?>, impl, target as Array);
				} else {
  <?php $code = '';$model->renderTranslationCode($code, $complexType, 1); echo $code ?>			}
			}
		}
<?php endforeach; ?>
		public function translateArrayCollection(collection:ArrayCollection, helpFunction:Function, targetClass:Class, target:Array):void
		{
			var resultArray:Array = collection.toArray();
			for(var i:int = 0;i<resultArray.length;i++) {
				var entry:Object = resultArray[i];
				var newItem:Object = new targetClass;
				helpFunction(entry, newItem, targetClass);
				target.push(newItem);
			}
		}
	}
}