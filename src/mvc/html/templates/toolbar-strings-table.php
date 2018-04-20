<div class="k-header bbn-w-100"
     ref="toolbar-strings-table"
     style="min-height: 60px;">
  <div style="padding: 6px;"
       v-if="id_project !=='options'"
  >



    <bbn-button title="<?=_('Update table')?>"
                class="k-button bbn-button bbn-events-component"
                @click="remake_cache"
                icon="fa fa-retweet"
                text="<?=_('Update table')?>"
    >
    </bbn-button>

    <bbn-button title="<?=_('Force translation files update')?>"
                class="k-button bbn-button bbn-events-component"
                @click="generate"
                icon="fa fa-exchange"
                text="<?=_('Force translation files update')?>"
    >
    </bbn-button>

    <bbn-button title="<?=_('Check into the files for new strings')?>"
                class="k-button bbn-button bbn-events-component"
                @click="find_strings"
                icon="fa fa-search"
                text="<?=_('Check into the files for new strings')?>"
    >
    </bbn-button>
    <div style="display:inline;" >

      <bbn-switch v-model="hide_source_language"
                  :value="true"
                  :novalue="false"
                  style="float: right;display: inline;"
      ></bbn-switch>

      <div style="display:inline; float: right; padding-right:6px"
      >
        <span style="vertical-align: sub;"
              v-text="hide_source_language ? 'Show source language column' : 'Hide source language column'"></span>
      </div>
    </div>

  </div>
  <div style="font-size:9px; text-align: right; padding-right: 6px;padding-bottom:3px"
       v-if="id_project !=='options'"
  ><?=_("If the column with ")?><i class="fa fa-asterisk"></i> <?=_("is empty be sure to force translation files update and then update the table")?></div>

  <div v-if="id_project ==='options'"
       class="bbn-padded bbn-c"

  >
    <div><?=_("Select column you want to hide from the table")?></div>
    <bbn-multiselect :placeholder="_('')"
                     :source="languages"
                     v-model="to_hide_col"


    ></bbn-multiselect>
  </div>

</div>