<div class="strings-table" style="min-height: 500px; width:100%">
  <bbn-table v-if="source.res.languages.length && !showAlert"
             v-show="showAlert === false"
             :source="source.res.strings"
             :columns="columns"
             editable="nobuttons"
             :pageable="true"
             :limit="25"
             :showable="true"
             :server-filtering="false"
             :info="true"
             :filterable="true"
             :multifilter="true"
             ref="strings_table"
             :order="[{field: 'expression', Dir: 'ASC'}]"
             :toolbar="$options.components['toolbar-strings-table']"
             @change="insert_translation">

   <bbns-column field="exp"
               :title="'<?=_('Original expression in')?>' +' '+ source_lang "
               :index="1"
               width="20%"
               :editable="false"
               cls="bbn-i"
               :sortable="true"
    ></bbns-column>

  </bbn-table>
  <div v-else-if="showAlert" class="bbn-middle bbn-overlay">
    <h1>Wait for the ending of the process before to make other actions in this tab</h1>
  </div>
  <div v-else-if="!source.res.languages.length && column_length">
    <h5 class="bbn-c"><?=_('Close this tab and configure translation files from the widget')?> <i class="nf nf-fa-flag"></i> <?=_('button before to open the table of strings')?>.</h5>
    <br>
    <h5 class="bbn-c"><?=_('If the widget seems to have translation files configured but you see this message, try to reload the widget from')?> <i class="nf nf-fa-tasksfa_retweet"></i> <?=_('button and then configure files')?></h5>
  </div>
  
  <div v-else-if="source.res.languages.length && !column_length">
    <bbn-loader loadingText="Updating table data"></bbn-loader>
  </div>

</div>
