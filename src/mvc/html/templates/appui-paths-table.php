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
                title=" "
                cls="bbn-c"
                :buttons="[{
                  notext: true,
                  command: find_new_strings,
                  icon: 'zmdi zmdi-flash',
                  title:'Check for new strings in this path',
                  text: 'Check for new strings in this path',
                  },{
                  notext: true,
                  command: open_strings_table,
                  icon: 'fa fa-book',
                  text: 'Go to the table of strings of this path',
                  title:'Go to the table of strings of this path',
           	  	 }]"
    ></bbn-column>

  </bbn-table>
</div>