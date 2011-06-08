<?php
/* @var $model Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator */
?>package <?php echo $model->myPackage ?>.commands
{
    import mx.rpc.soap.LoadEvent;
    import com.bestbytes.zugspitze.commands.Command;
    import com.bestbytes.zugspitze.commands.ICommand;
    import com.bestbytes.zugspitze.events.CommandEvent;
    import <?php echo $model->myPackage ?>.model.<?= $model->typeToASType($model->proxyClassName) ?>;

    public class <?php echo $model->loadServiceCommandClassName ?> extends Command implements ICommand
    {
        private var _proxy:<?= $model->typeToASType($model->proxyClassName) ?>;

        public function <?php echo $model->loadServiceCommandClassName ?>(proxy:<?= $model->typeToASType($model->proxyClassName) ?>, wsdl:String = 'deprecated')
        {
            this._proxy = proxy;
        }
        public function execute():void
        {
            this.dispatchEvent(new CommandEvent(CommandEvent.COMMAND_COMPLETE, this));
        }
    }
}