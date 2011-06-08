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
	<tr>
		<td colspan="3" class="header1">
			<a name="operation<?php echo $op->name ?>">
				<span class="operation"><?php echo $op->name ?></span>(<?php echo $parms ?>)
			</a>
			: <span class="type"><?php echo $returnType ?></span>
			<p class="operationComment"><?php echo $op->comment; ?></p>
		</td>
	</tr>
<?php if(count($op->parameterDocs)>0): ?>
	<tr>
		<td colspan="3" class="header2">
			Parameters
		</td>
	</tr>
	<tr>
		<td class="indent parameterHeader">name</td>
		<td class="parameterHeader">type</td>
		<td class="parameterHeader">comment</td>
	</tr>
<?php endif; ?>
<?php foreach($op->parameterDocs as $parmName => $parmType): ?>
<?php
	if($rowClass == 'rowA') {
		 $rowClass = 'rowB';
	} else {
		 $rowClass = 'rowA';
	}
 ?>
	<tr>
		<td class="<?php echo $rowClass ?>">
			<span class="parameter indent"><?php echo $parmName ?></span>
		</td>
		<td class="<?php echo $rowClass ?>">
			<?php if(!$renderer->isBaseType($parmType->type)): ?>
				<a href="#<?php echo $renderer->typeLink($parmType) ?>"><span class="type"><?php echo $parmType->type ?></span></a>
			<?php else: ?>
				<span class="type"><?php echo $parmType->type ?></span>
			<?php endif; ?>
		</td>
		<td class="<?php echo $rowClass ?>">
			<span class="comment"><?php echo $parmType->comment ?></span>
		</td>
	</tr>
<?php endforeach; ?>
	<tr>
		<td colspan="3" class="header2">
			Return
		</td>
	</tr>
	<tr>
		<td>
			&nbsp;
		</td>
<?php if($op->returnType): ?>
		<td>
			<?php if(!$renderer->isBaseType($op->returnType->type)): ?>
				<a href="#<?= $renderer->typeLink($op->returnType) ?>"><span class="type"><?php echo $op->returnType->type ?></span></a>
			<?php else: ?>
				<span class="type"><?php echo $op->returnType->type ?></span>
			<?php endif; ?>
		</td>
		<td><?php echo $op->returnType->comment ?></td>
<?php else: ?>
		<td>null</td>
		<td>---</td>
<?php endif; ?>
	</tr>
<? if(count($op->throwsTypes) > 0): ?>
	<tr>
		<td colspan="3" class="header2">
			Throws
		</td>
	</tr>
	<? foreach($op->throwsTypes as $throwsType): ?>
	<tr>
		<td>&nbsp;</td>
		<td colspan="2"><a href="#<?= $renderer->typeLink($throwsType) ?>"><?= $throwsType->type ?></a></td>
	</tr>
	<? endforeach; ?>
<? endif; ?>
<? if(count($op->messageTypes) > 0): ?>
	<tr>
		<td colspan="3" class="header2">
			Service messages
		</td>
	</tr>
	<? foreach($op->messageTypes as $messageType): ?>
	<tr>
		<td>&nbsp;</td>
		<td colspan="2"><a href="#<?= $renderer->typeLink($messageType) ?>"><?= $messageType->type ?></a></td>
	</tr>
	<? endforeach; ?>
<? endif; ?>
	<tr>
		<td colspan="3">&nbsp</td>
	</tr>
