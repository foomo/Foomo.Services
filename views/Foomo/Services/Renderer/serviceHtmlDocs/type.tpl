<?php
/* @var $renderer Foomo\Services\Renderer\HtmlDocs */
/* @var $type Foomo\Services\Reflection\ServiceObjectType */
$type = $model['type'];
$renderer = $model['renderer'];
$level = $model['level'];
$propName = $model['propName'];
if($level == 1) {
	$codeClass = 'codeToplevel';
	$divClass = 'topLevelDiv';
} else {
	$codeClass = 'code';
	$divClass  = 'nestedDiv';
}

// constants
if(count($type->constants)>0) {
	$renderConstants = true;
	$filteredConstants = array();
	$searchPrefixes = array('CODE_', 'KEY_');
	foreach($type->constants as $constName => $constValue) {
		foreach($searchPrefixes as $searchPrefix) {
			if(true || strpos($constName, $searchPrefix) === 0) { // for now exporting all of them
				$filteredConstants[$constName] = $constValue;
				continue;
			}
		}
	}
	ksort($filteredConstants);
} else {
	$renderConstants = false;
}
?><div style="padding-left:<?php echo ($level * 20); ?>px;margin:0;" class="<?php echo $divClass ?>">
<?php if($propName): ?>
	<?php if($renderer->isBaseType($type->type)): ?>
		<span class="propName"> <?php echo $propName ?></span><code class="<?php echo $codeClass ?>"> : <?php echo $type->type ?></code>
	<?php else: ?>
		<?php if($renderer->typeIsInRecursion($type) || $level > 0): ?>
			<span class="propName"> <?php echo  $propName  ?></span><code class="<?php echo $codeClass ?>"> : <?php if($type->isArrayOf) {echo 'array of';} ?> <a href="#<?php echo  $renderer->typeLink($type)  ?>"><?php echo $type->type ?></a></code>
		<?php else: ?>
			<code class="<?php echo $codeClass ?>"><?php echo $type->type ?></code>
		<?php endif; ?>
	<?php endif; ?>
<?php else: ?>
	<a name="<?php echo  $renderer->typeLink($type)  ?>"><code class="<?php echo $codeClass ?>"><?php echo $type->type;  ?></code></a>
<?php endif; ?>

<?php if(!empty($type->phpDocEntry->asClass)): ?>
	(actionsript counter part of <?php echo $type->phpDocEntry->asClass ?>)
<?php endif; ?>
<?php if(!empty($type->phpDocEntry->comment)): ?>
	<span class="typeDocs"> - <?php echo $type->phpDocEntry->comment ?></span>
<?php else: ?>
	<span class="typeDocs"> - no doc comment</span>
<?php endif; ?>

<?php if($renderer->typeIsInRecursion($type)): ?>
	<p>see top level docs</p>
<?php endif; ?>
<?php if($renderConstants): ?>
<div style="padding-left:<?php echo ($level * 20); ?>px;margin:0;" class="<?php echo $divClass ?>">
	<?php foreach($filteredConstants as $constName => $constValue): ?>
		<div>
			<span class="propName"> <?php echo $constName ?></span>
			<span class="<?php echo $codeClass ?>"> : <?php echo gettype($constValue) ?></span>
			<span class="constValue"> <?php echo $constValue ?></span>
		</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>
</div>