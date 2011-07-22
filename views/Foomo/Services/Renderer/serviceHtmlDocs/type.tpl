<?
/* @var $renderer Foomo\Services\Renderer\HtmlDocs */
/* @var $type Foomo\Services\Reflection\ServiceObjectType */
extract($model);
/*
$type = $model['type'];
$renderer = $model['renderer'];
$level = $model['level']-1;
$propName = $model['propName'];
*/
?>


<div class="toggleBox">
	<div class="toogleButton">
		<div class="toggleOpenIcon">+</div>
		<div class="toggleOpenContent"><a name="<?=  $renderer->typeLink($type)  ?>" style="text-decoration: none;"><code><?= $type->type;  ?></code></a></div>
	</div>
	<div class="toggleContent">
		<p>
			<?= $view->escape($type->phpDocEntry->comment) ?>
		</p>
		<? if(count($type->constants) > 0 || count($type->props) > 0): ?>
			<table>
				<thead>
					<th>Name</th>
					<th>Type</th>
					<th>Comment</th>
				</thead>
				<tbody>
					<? if(count($type->constants)>0): ?>
						<tr>
							<td colspan="3" class="tableInnerHead">Constants</td>
						</tr>
						<? foreach($type->constants as $constName => $constValue): ?>
							<tr>
								<td><?= $view->escape($constName) ?></td>
								<td><?= $view->escape(gettype($constValue)) ?></td>
								<td><?= $view->escape($constValue) ?></td>
							</tr>
						<? endforeach; ?>
					<? endif; ?>

					<? if(count($type->props) > 0): ?>
						<tr>
							<td colspan="3" class="tableInnerHead">Members</td>
						</tr>
						<? 
							foreach($type->props as $propName => $prop): 
								/* @var $prop Foomo\Services\Reflection\ServiceObjectType */
						?>
							<tr>
								<td><?= $view->escape($propName) ?></td>
								<td><?= $view->escape($prop->type) ?></td>
								<td><?= $view->escape($prop->phpDocEntry->comment) ?></td>
							</tr>
						<? endforeach; ?>
					<? endif; ?>
				</tbody>
			</table>
		<? else: ?>
			<p>No members or constants</p>
		<? endif; ?>
	</div>
</div>
