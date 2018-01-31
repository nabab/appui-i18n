<div class="k-header bbn-w-100"
     style="min-height: 30px;">
  <div>
    <bbn-button title="<?=_('Go to statistic\'s list')?>"
                class="k-button bbn-button bbn-events-component"
                @click="open_statistic_list"
                icon="fa fa-bar-chart-o"
                text="<?=_('Statistic\'s list')?>">
    </bbn-button>
    <bbn-button title="<?=_('Go to the table of your translations')?>"
                class="k-button bbn-button bbn-events-component"
                @click="open_user_history"
                icon="fa fa-user"
                text="<?=_('User\'s translation')?>"
    >
    </bbn-button>

    <bbn-button title="<?=_('Go to the complete table of translations of all users')?>"
                class="k-button bbn-button bbn-events-component"
                @click="open_complete_history"
                icon="fa fa-users"
                text="<?=_('Complete translations history')?>"
    >
    </bbn-button>

    <bbn-dropdown :source="dd_source"
                  title="<?=_('Select a language to open it\'s glossary')?>"
                  placeholder="Glossary table"
                  v-model="lang"
    ></bbn-dropdown>

  </div>
</div>