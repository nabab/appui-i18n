<div class="appui-strings-table" style="min-height: 500px">



  <bbn-table :source="source">

    <bbn-column field="source.todo"
                title="<?=_('Source language')?>"
                width="40%"
                class="bbn-xl"
                ftitle="<?=_('The strings in their original language')?>"
    ></bbn-column>

    <bbn-column field=""
                title="Language"

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