<?php
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
?>package <?php echo $model->myPackage ?>.model {

	import mx.rpc.soap.WebService;
	import mx.rpc.soap.LoadEvent;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.events.FaultEvent;
	import <?php echo $model->myPackage ?>.vo.*;
	
	<?php echo $model->getAllClientClassImports() ?>
	
	// import <?php echo $model->myPackage ?>.operations.*;
	[Event(name='load', type='mx.rpc.soap.LoadEvent')]
	public class <?php echo $model->proxyClassName ?> extends <?php echo $model->proxyBaseClassName ?> {
	
		// implementation classes
<?php foreach($model->complexTypes as $key => $complexType): ?>
		public var <?php echo $model->complexTypeToImplClassName($complexType); ?>:Class = <?php echo $model->typeToASType($complexType->type) ?>;
<?php endforeach; ?>

	
		public static var defaultWsdl:String = '<?php echo $model->wsdl ?>';
		private static var _instance:<?php echo $model->proxyClassName ?>;
		private var _ws:WebService;
		internal var webServiceHelper:WebServiceHelper;
		public function <?php echo $model->proxyClassName ?>()
		{
      this.webServiceHelper = new WebServiceHelper(this);
			if(!_instance) {
				_instance = this;
			}
			this.loadOperations();
		}
<?php $view->includePhp('../ProxyClassMethodLoadOperations.tpl'); ?>
		public function setUpService(wsdl:String):void
		{
			this._ws = new WebService();
			this._ws.loadWSDL(wsdl);
			this._ws.addEventListener(LoadEvent.LOAD, this.wireWebService);
			this._ws.addEventListener(FaultEvent.FAULT, this.webServiceFault);
		}

		public static function get defaultInstance():<?php echo $model->proxyClassName ?>
		{
			if(!_instance) {
				_instance = new <?php echo $model->proxyClassName ?>;
			}
			return _instance;
		}

		private function webServiceFault(faultEvent:FaultEvent):void
		{
			throw new Error('Webservice Error');
		}
		private function wireWebService(event:LoadEvent):void
		{
<?php foreach($model->operations as $operation): ?><?php foreach($eventMap as $eventOp => $event): ?>
			this._ws.<?php echo $operation->name ?>.addEventListener(<?php echo $event; ?>, this.operation<?php echo ucfirst($operation->name); ?>.<?php echo $eventOp; ?>);
			this.operation<?php echo ucfirst($operation->name) ?>.operation = this._ws.<?php echo $operation->name ?>;
<?php endforeach; ?><?php endforeach; ?>
			this.dispatchEvent(event);
		}
	}
}