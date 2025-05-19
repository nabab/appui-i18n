<div class="appui-i18n-translations bbn-overlay">
  <bbn-table bbn-if="source.res?.languages?.length && !showAlert"
             :source="source.res?.strings || []"
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
             :order="[{field: 'expression', dir: 'ASC'}]"
             :toolbar="$options.components.toolbar"
             @change="insertTranslation">
   <bbns-column field="exp"
               :label="_('Original expression in %s', source_lang)"
               :index="1"
               :editable="false"
               cls="bbn-i"
               :sortable="true"/>
  </bbn-table>
  <div bbn-elseif="showAlert"
       class="bbn-middle bbn-overlay">
    <h1><?=_("Wait for the ending of the process before to make other actions in this tab")?></h1>
  </div>
  <div bbn-elseif="!source.res?.languages?.length && columnLength">
    <h5 class="bbn-c"><?=_('Close this tab and configure translation files from the widget')?> <i class="nf nf-fa-flag"/> <?=_('button before to open the table of strings')?>.</h5>
    <br>
    <h5 class="bbn-c"><?=_('If the widget seems to have translation files configured but you see this message, try to reload the widget from')?> <i class="nf nf-fa-tasksfa_retweet"/> <?=_('button and then configure files') ?></h5>
  </div>
  <div bbn-elseif="source.res?.languages?.length && !columnLength">
    <bbn-loader loading-text="_('Updating table data')"/>
  </div>
</div>
