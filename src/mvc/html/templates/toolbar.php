<div class="k-header bbn-w-100"
     ref="toolbar"
     style="min-height: 30px;">
  <div class="bbn-spadded bbn-c">
    <span class="bbn-xl">History of translations</span>
    <div style="float:right">

      <bbn-button title="<?=_('Open translation\'s table')?>"
                  placeholder="Glossary table"
                  @click="config_translations"
      ><?=_('Go to translation\'s tab')?></bbn-button>
      
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

    </div>

  </div>
</div>