<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
?>package <?php echo $model->myPackage ?>.commands
{
    import mx.rpc.soap.LoadEvent;
    import com.bestbytes.zugspitze.commands.Command;
    import com.bestbytes.zugspitze.commands.ICommand;
    import com.bestbytes.zugspitze.events.CommandEvent;
    import <?php echo $model->myPackage ?>.model.<?php echo $model->proxyClassName ?>;

    public class <?php echo $model->loadServiceCommandClassName ?> extends Command implements ICommand
    {
        private var _proxy:<?php echo $model->proxyClassName ?>;
        private var _wsdl:String;

        public function <?php echo $model->loadServiceCommandClassName ?>(proxy:<?php echo $model->proxyClassName ?>, wsdl:String = '<?php echo $model->wsdl ?>')
        {
            this._proxy = proxy;
            this._wsdl = wsdl;
        }

        public function execute():void
        {
            this._proxy.addEventListener(LoadEvent.LOAD, this.onLoad);
            this._proxy.setUpService(this._wsdl);
        }

        private function onLoad(event:LoadEvent):void
        {
            this.dispatchEvent(new CommandEvent(CommandEvent.COMMAND_COMPLETE, this));
        }
    }
}