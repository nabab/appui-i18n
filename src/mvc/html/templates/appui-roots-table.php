<div style="min-height: 500px">
  <bbn-table :source="source.path"
  >
    <bbn-column field="text"
                title="Path"
                ftitle="All different paths of this project"
                ref="paths_table"
                width="35%"
    ></bbn-column>
<!--cambiare il nome a questo file in paths_table-->
    

    <bbn-column field="code"
                :buttons="[{
                  class:'path_button_expander',          
                  command: run_script,
                  icon: 'fa fa-superpowers',
                  title:'Check for new untranslated strings',
                  text: 'Find new strings in this path',
                  },{
                  class:'path_button_expander',      
                  command: open_strings_table,
                  icon: 'fa fa-book',
                  text: 'Table of translations for this path',
                  title:'View the table of strings to translate',
           	  	 }]"
    ></bbn-column>

  </bbn-table>
</div>