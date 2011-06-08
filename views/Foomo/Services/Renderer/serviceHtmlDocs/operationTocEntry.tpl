<?php
/* @var $renderer ServiceHtmlRenderer */
/* @var $op ServiceOperation  */

$op = $model['op'];
$renderer = $model['renderer'];
$parms = implode(', ', array_keys($op->parameters));
if($op->returnType) {
	$returnType = $op->returnType->type;
} else {
	$returnType = 'null';
}
$rowClass = 'rowA';
?>
		<li>
			<a href="#operation<?php echo $op->name ?>">
				<span class="operation"><?php echo $op->name ?>
			</a>
			</span>(<?php echo $parms ?>) : <span class="type"><?php echo $returnType ?></span> 
		</li>
