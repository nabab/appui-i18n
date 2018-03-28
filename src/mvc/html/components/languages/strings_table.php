<div class="appui-strings-table" style="min-height: 500px; width:100%">

    <bbn-table v-if="source.res.languages.length"
               :source="mapData"
               :columns="columns"
               editable="inline"
               :pageable="true"
               :sortable="true"
               :limit="25"

               :info="true"
               :filterable="true"
               :multifilter="true"
               ref="strings_table"
               :order="[{field: 'expression', dir: 'ASC'}]"
               :expander="$options.components['file_linker']"
               :toolbar="[{
                         command: remake_cache,
                         icon: 'fa fa-retweet',
                         title: '<?=addslashes(_('Update table'))?>',
                         class:'bbn-l'
                         }, {
                         command: generate,
                         icon: 'fa fa-exchange',
                         title: '<?=addslashes(_('Update translations\' files'))?>',
                         class:'bbn-l'
                         }, {
                         command: find_strings,
                         icon: 'fa fa-search',
                         title: '<?=addslashes(_('Check into the files for new strings'))?>',
                         class:'bbn-l'
                         }]"
               @change="insert_translation"
  >
    <!--<bbn-column field="id_exp"
                :hidden="true"
    ></bbn-column>-->

   <bbn-column field="original_exp"
               title="<?=_('Original expression')?>"
               :index="1"
               width="20%"
               :editable="false"
               cls="bbn-i"
    ></bbn-column>

  </bbn-table>

  <div v-else>
    <h5 class="bbn-c"><?=_('Close the tab and configure translation files from the widget')?> <i class="fa fa-flag"></i> <?=_('button before to open the table of strings')?>.</h5>
    <br>
    <h5 class="bbn-c"><?=_('If the widget seems to have translation files configured but you see this message, reload the widget from')?> <i class="fa fa-retweet"></i> <?=_('button and then configure files using')?></h5>
  </div>

</div>