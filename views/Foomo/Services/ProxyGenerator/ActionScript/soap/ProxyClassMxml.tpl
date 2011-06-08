<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
$operationVarNames = array();
foreach($model->operations as $operation) {
  $operationVarNames[$model->operationToOperationName($operation->name)] = $model->operationToOperationVarName($operation->name);
}

?>package <?php echo $model->myPackage ?>.model.mxml {

	import <?php echo $model->myPackage ?>.vo.*;
	import <?php echo $model->myPackage ?>.model.<?php echo $model->proxyClassName ?>;
	import mx.core.IMXMLObject;

	public class <?php echo $model->proxyClassName ?> extends <?php echo $model->myPackage ?>.model.<?php echo $model->proxyClassName ?> implements IMXMLObject {
		public static var defaultWsdl:String = '<?php echo $model->wsdl ?>';
//		private static var _instance:<?php echo $model->myPackage ?>.model.mxml.<?php echo $model->proxyClassName ?>;
//		protected var _ws:WebService;
    public var wsdl:String;
		public function <?php echo $model->proxyClassName ?> ()
		{
				super();
		}
		/*


		public function set wsdl(wsdl:String):void
		{
			this._ws = new WebService();
			this._ws.loadWSDL(wsdl);
			this._ws.addEventListener(LoadEvent.LOAD, this.wireWebService);
			this._ws.addEventListener(FaultEvent.FAULT, this.webServiceFault);
			if(wsdl == defaultWsdl && !_instance) {
			  _instance = this;
			}
		}
		public static function get defaultInstance():<?php echo $model->proxyClassName ?>
		{
			if(!_instance) {
				_instance = new <?php echo $model->proxyClassName ?>;
				_instance.wsdl = <?php echo $model->proxyClassName ?>.defaultWsdl;
			}
			return _instance;
		}

		*/

	    /**
	     *  Called after the implementing object has been created and all
	     *  component properties specified on the MXML tag have been initialized.
	     *
	     *  @param document The MXML document that created this object.
	     *
	     *  @param id The identifier used by <code>document</code> to refer
	     *  to this object.
	     *  If the object is a deep property on <code>document</code>,
	     *  <code>id</code> is null.
	     */
	    public function initialized(document:Object, id:String):void
	    {
	    	if(this.wsdl) {
	    		this.setUpService(this.wsdl);
	    	} else {
	    	  this.setUpService(defaultWsdl);
	    	  //throw new Error('no wsdl set');
	    	}
<?php foreach($operationVarNames as $varType => $varName): ?>
        if(!this.<?php echo $varName ?>.proxy) {
  			   this.<?php echo $varName ?>.proxy = this as <?php echo $model->myPackage ?>.model.<?php echo $model->proxyClassName ?>;
        }
<?php endforeach; ?>
	    }
	}
}