<div style="min-height: 500px">
  <bbn-table :source="source.path"
  >
    <bbn-column field="text"
                title="Path"
                ftitle="All different paths of this project"
                ref="paths_table"
                width="90%"
    ></bbn-column>

    <bbn-column field="code"
                :buttons="[{
                  notext: true,
                  command: run_script,
                  icon: 'fa fa-superpowers',
                  title:'Check for new untranslated strings',
                  text: 'Find new strings in this path',
                  },{
                  notext: true,
                  command: open_strings_table,
                  icon: 'fa fa-book',
                  text: 'Go to the table of translations of this path',
                  title:'Go to the table of translations of this path',
           	  	 }]"
    ></bbn-column>

  </bbn-table>
</div>