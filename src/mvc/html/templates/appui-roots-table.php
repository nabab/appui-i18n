<div style="min-height: 500px">
  <bbn-table :source="source.path">
    <bbn-column field="text"
                title="Path"
                ftitle="All different paths of this project"
                ref="paths_table"
    ></bbn-column>
<!--cambiare il nome a questo file in paths_table-->
    <bbn-column field=""
                title="<?=_('Progress')?>"
                width="40%"
                class="bbn-xl"
                component="bbn-progressbar"
                ftitle="<?=_('Progress in translation for all languages configured for this root')?>"
    ></bbn-column>

    <bbn-column field="id_option"
                width="50"
                :buttons="[{
                command: run_script,
                icon: 'fa fa-superpowers',
								title:'Check for new untranslated strings in this path'
								},{
                command: open_strings_table,
                icon: 'fa fa-book',
								title:'View the table of strings to translate'
								}]"
    ></bbn-column>

  </bbn-table>
</div>