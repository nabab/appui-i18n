<div class="appui-strings-table" style="min-height: 500px; width:100%">

  <bbn-table v-if="source.res.languages.length && column_length"
             :source="mapData"
             :columns="columns"
             editable="inline"
             :pageable="true"
             :sortable="true"
             :limit="25"
             :showable="true"
             :info="true"
             :filterable="true"
             :multifilter="true"
             ref="strings_table"
             :order="[{field: 'expression', dir: 'ASC'}]"
             :expander="$options.components['file_linker'] "
             :toolbar="$options.components['toolbar-strings-table']"
             @change="insert_translation"
  >

   <bbn-column field="original_exp"
               :title="'<?=_('Original expression in ')?>' + source_lang "
               :index="1"
               width="20%"
               :editable="false"
               cls="bbn-i"
    ></bbn-column>

  </bbn-table>

  <div v-else-if="!source.res.languages.length && column_length">
    <h5 class="bbn-c"><?=_('Close this tab and configure translation files from the widget')?> <i class="fa fa-flag"></i> <?=_('button before to open the table of strings')?>.</h5>
    <br>
    <h5 class="bbn-c"><?=_('If the widget seems to have translation files configured but you see this message, try to reload the widget from')?> <i class="fa fa-retweet"></i> <?=_('button and then configure files')?></h5>
  </div>

  <div v-else-if="source.res.languages.length && !column_length">
    <bbn-loader loadingText="Updating table data"></bbn-loader>
  </div>

</div>