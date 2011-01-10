{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl'}

<div class="pageTitle">
	<h2>{$url}</h2>
</div>

{form:add}
	<div class="dataFilter">
		<table cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<tr>
					<td>
						<div class="options">
							<p>
								<label for="group">{$lblGroup|ucfirst}</label>
								{$txtGroup} {$txtGroupError}
							</p>
						</div>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="99">
						<div class="options">
							<div class="buttonHolder">
								<input id="search" class="inputButton button mainButton" type="submit" name="search" value="{$msgCreateGroupByAddresses|ucfirst}" />
							</div>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
{/form:add}

{option:datagrid}
<div class="datagridHolder">
	{$datagrid}
</div>
{/option:datagrid}

<div class="buttonHolderLeft">
	<a href="{$var|geturl:'statistics'}&amp;id={$mailing['id']}" class="button" title="{$lblAddGroup|ucfirst}">
		<span>{$msgBackToMailings|sprintf:{$mailing['name']}}</span>
	</a>
</div>

{include:file='{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}