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
?>package <?= $model->myPackage ?>.model 
{
	import <?= $model->myPackage ?>.vo.*;
	import com.bestbytes.zugspitze.services.rpc.RPCClient;

	public class <?= $model->typeToASType($model->proxyClassName) ?> extends <?= $model->typeToASType($model->proxyBaseClassName) ?> 
	{
		//-----------------------------------------------------------------------------------------
		// ~ Constants
		//-----------------------------------------------------------------------------------------

		public static const VERSION:Number = <?= constant($model->serviceName.'::VERSION') ?>;

		
		//-----------------------------------------------------------------------------------------
		// ~ Static variables
		//-----------------------------------------------------------------------------------------
		
		/**
		 *
		 */
		private static var _instance:<?= $model->typeToASType($model->proxyClassName) ?>;
		/**
		 *
		 */
		public static var defaultEndPoint:String = '<?= Foomo\Utils::getServerUrl() . \Foomo\MVC::getCurrentUrlHandler()->renderMethodUrl('serve') ?>';

		//-----------------------------------------------------------------------------------------
		// ~ Variables
		//-----------------------------------------------------------------------------------------
		
		/**
		 *
		 */
		public var rpcClient:RPCClient;
		
		//-----------------------------------------------------------------------------------------
		// ~ Constructor
		//-----------------------------------------------------------------------------------------
		
		public function <?= $model->typeToASType($model->proxyClassName) ?>(endPoint:String=null)
		{
			if(!_instance) _instance = this;
			if(!endPoint) endPoint = defaultEndPoint;
			this.rpcClient = new RPCClient(endPoint, '<?= str_replace('\\', '\\\\', $model->serviceName); ?>', VERSION);
			this.loadOperations();
		}
		
		//-----------------------------------------------------------------------------------------
		// ~ Public methods
		//-----------------------------------------------------------------------------------------
		
		/**
		 *
		 */
		public function get endPoint():String
		{
			return this.rpcClient.endPoint;
		}
		
<? $view->includePhp('../ProxyClassMethodLoadOperations.tpl'); ?>
		
		
		//-----------------------------------------------------------------------------------------
		// ~ Public static methods
		//-----------------------------------------------------------------------------------------

		/**
		 *
		 */
		public static function get defaultInstance():<?= $model->typeToASType($model->proxyClassName) ?>
		{
			if (!_instance) _instance = new <?= $model->typeToASType($model->proxyClassName) ?>(defaultEndPoint);
			return _instance;
		}
	}
}
