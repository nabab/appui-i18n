<div style="min-height: 500px">
  <bbn-table :source="source.path"
  >
    <bbn-column field="text"
                title="Path"
                ftitle="<?=_('All different paths of this project')?>"
                ref="paths_table"
                width="80%"
    ></bbn-column>

    <bbn-column field="language"
                title="<?=_('Language')?>"
                ftitle="<?=_('Original language of the path')?>"
                width="10%"
    ></bbn-column>

    <bbn-column field="code"
                title=" "
                cls="bbn-c"
                :buttons="[{
                  notext: true,
                  command: find_new_strings,
                  icon: 'zmdi zmdi-flash',
                  title: '<?=_('Check for new strings in this path')?>',
                  text: '<?=_('Check for new strings in this path')?>',
                  },{
                  notext: true,
                  command: open_strings_table,
                  icon: 'fa fa-book',
                  text: '<?=_('Go to the table of strings of this path')?>',
                  title: '<?=_('Go to the table of strings of this path')?>'
           	  	 }]"
    ></bbn-column>

  </bbn-table>
</div>