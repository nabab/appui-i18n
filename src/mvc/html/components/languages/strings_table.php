<div class="appui-strings-table" style="min-height: 500px">


  <bbn-table :source="source.strings_in_db"
             :columns="languages"

  >

    <bbn-column field="expression"
                :title="first_column_title"
                width="40%"
                class="bbn-xl"
                ftitle="<?=_('The strings in their original language')?>"
                :fixed="true"

    ></bbn-column>



    <bbn-column field="id_option"
                width="50"
                :buttons="[{

                icon: 'fa fa-superpowers',
								title:'Check for new untranslated strings in this path'
								},{

                icon: 'fa fa-book',
								title:'View the table of strings to translate'
								}]"
    ></bbn-column>

  </bbn-table>
</div>