<div class="bbn-header bbn-w-100"
     ref="toolbar-strings-table"
     style="min-height: 60px;">
  <div style="padding: 6px;"
       v-if="id_project !=='options'"
  >
    <bbn-button title="<?= _('Force translation files update') ?>"
                class="bbn-button bbn-events-component"
                @click="generate"
                icon="nf nf-fa-files_o"
                text="<?= _('Create translation files') ?>"
                style="background-color: orange;"
    >
    </bbn-button>

    <bbn-button title="<?= _('Rebuild table data') ?>"
                class="bbn-button bbn-events-component"
                @click="remake_cache"
                icon="nf nf-fa-retweet"
                text="<?= _('Rebuild table data') ?>"
    >
    </bbn-button>


    <bbn-button title="<?= _('Check files for new strings') ?>"
                class="bbn-button bbn-events-component"
                @click="find_strings"
                icon="nf nf-fa-search"
                text="<?= _('Parse files for new strings') ?>"
    >
    </bbn-button>
    <div style="display:inline">
      <bbn-input placeholder="<?= _("Search the string") ?>"
                 @change="search"
                 v-model="valueToFind"
      ></bbn-input></div>
    <!--div style="display:inline">

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
    </div-->

  </div>
  <div style="font-size:9px; text-align: right; padding-right: 6px;padding-bottom:3px"
       v-if="id_project !=='options'"
  ><?= _("If the column with ")?><i class="nf nf-fa-asterisk"></i> <?=_("is empty be sure to force translation files update and then update the table") ?></div>

  <div v-if="id_project ==='options'"
       class="bbn-padding bbn-grid-fields"

  >
    <div class="bbn-r"><?= _("Select languages you want to hide from the table") ?></div>
    <div class="bbn-r">
      <div v-for="l in  languages"
           style="display: inline;"
      >
        <label v-text="l"></label>
        <bbn-checkbox :key="l"
                      style="padding-right: 3px"
                      @change="hide_col"
                      :value="l"
        ></bbn-checkbox>
      </div>

    </div>
  </div>

</div>
