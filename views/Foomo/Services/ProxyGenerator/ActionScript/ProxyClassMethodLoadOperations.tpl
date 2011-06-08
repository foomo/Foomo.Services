<?php
$operationVarNames = array();
foreach($model->operations as $operation) {
  $operationVarNames[$model->operationToOperationName($operation->name)] = $model->operationToOperationVarName($operation->name);
}
?>
		/**
		 * Load and wire operations
		 */
		public function loadOperations():void
		{
<?php foreach($operationVarNames as $varType => $varName): ?>
			this.<?php echo $varName ?> = new <?php echo $varType ?>;
			this.<?php echo $varName ?>.proxy = this;
<?php endforeach; ?>
		}