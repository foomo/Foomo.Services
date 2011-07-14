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

<div class="toggleBox">
	<div class="toogleButton">
		<div class="toggleOpenIcon">+</div>
		<div class="toggleOpenContent"><?php echo $op->name ?>(<span style="font-weight: normal;"><?php echo $parms ?></span>) : <?php echo $returnType ?></div>
	</div>
	<div class="toggleContent">
		
		<table>
			<tr>
				<td colspan="3">
					<span class="textHead"><?php echo $op->name ?></span>(<?php echo $parms ?>)
					: <b><?php echo $returnType ?></b>
					<p><?php echo $op->comment; ?></p>
				</td>
			</tr>
			<tr>
				<th>name</th>
				<th>type</th>
				<th>comment</th>
			</tr>
		<?php if(count($op->parameterDocs)>0): ?>
			<tr>
				<td colspan="3" class="tableInnerHead">
					<h3>Parameters</h3>
				</td>
			</tr>
		<?php endif; ?>
		<?php foreach($op->parameterDocs as $parmName => $parmType): ?>
			<tr>
				<td>
					<b><?php echo $parmName ?></b>
				</td>
				<td>
					<?php if(!$renderer->isBaseType($parmType->type)): ?>
						<a href="#<?php echo $renderer->typeLink($parmType) ?>"><span><?php echo $parmType->type ?></span></a>
					<?php else: ?>
						<span><?php echo $parmType->type ?></span>
					<?php endif; ?>
				</td>
				<td>
					<span><?php echo $parmType->comment ?></span>
				</td>
			</tr>
		<?php endforeach; ?>
			<tr>
				<td colspan="3" class="tableInnerHead">
					<h3>Return</h3>
				</td>
			</tr>
			<tr>
				<td>
					&nbsp;
				</td>
		<?php if($op->returnType): ?>
				<td>
					<?php if(!$renderer->isBaseType($op->returnType->type)): ?>
						<a href="#<?= $renderer->typeLink($op->returnType) ?>"><span><?php echo $op->returnType->type ?></span></a>
					<?php else: ?>
						<span><?php echo $op->returnType->type ?></span>
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
				<td colspan="3" class="tableInnerHead">
					<h3>Throws</h3>
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
				<td colspan="3" class="tableInnerHead">
					<h3>Service messages</h3>
				</td>
			</tr>
			<? foreach($op->messageTypes as $messageType): ?>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2"><a href="#<?= $renderer->typeLink($messageType) ?>"><?= $messageType->type ?></a></td>
			</tr>
			<? endforeach; ?>
		<? endif; ?>
		</table>
	
	</div>
</div>