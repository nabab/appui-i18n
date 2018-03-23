<div class="appui-strings-table" style="min-height: 500px; width:100%">

    <bbn-table v-if="source.res.strings.length && column_length"
               :source="source.res.strings"
               :columns="columns"
               editable="inline"
               :pageable="true"
               :sortable="true"
               :limit="25"
               :map="mapData"
               :info="true"
               :filterable="true"
               :multifilter="true"
               ref="strings_table"
               :order="[{field: 'expression', dir: 'ASC'}]"
               :expander="$options.components['file_linker']"
               :toolbar="[{
                         command: remake_cache,
                         icon: 'fa fa-retweet',
                         title: 'Check for new strings and translations in this path',
                         class:'bbn-l'
                         }, {
                         command: generate,
                         icon: 'fa fa-exchange',
                         title: 'Update translations\' files',
                         class:'bbn-l'
                         }]"
               @change="insert_translation"
  >
    <bbn-column field="id_exp"
                :hidden="true"
    ></bbn-column>

   <bbn-column field="original_exp"
               title="<?=_('Original expression')?>"
               :index="1"
               width="20%"
               :editable="false"
               cls="bbn-i"
               ftitle="'The original language of strings is' + source.langs[source_lang].text"
    ></bbn-column>

  </bbn-table>
  <div v-else>
    <h1 class="bbn-c">No strings found in files of this path!</h1>
  </div>
</div>