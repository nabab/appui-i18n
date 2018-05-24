<div class="bbn-full-screen appui-dashboard-splitter-container">
  <bbn-splitter orientation="vertical"
                :resizable="true"
  >

    <bbn-pane :collapsible="true"
              :resizable="true"
              size="15%"

    >
      <div class="bbn-full-screen bbn-middle">
        <div class="bbn-grid-fields bbn-padded">

          <span class="bbn-r">Select a project</span>
          <bbn-dropdown :source="dd_projects"
                        v-model="id_project"
                        @change="load_widgets"
          ></bbn-dropdown>

          <span class="bbn-r" v-if="id_project !== 'options'"><?=_("The source language for this project is:")?></span>
          <span v-if="id_project !== 'options'"
                v-text="get_field(source.primary, 'code', get_field(source.projects, 'id', id_project, 'lang'), 'text')"></span>


          <span class="bbn-r"><?=_("Configure languages for this project")?></span>
          <div style="max-height: 25px;">
            <bbn-button icon="fa fa-flag"
                        :noText="true"
                        @click="cfg_project_languages"
            ></bbn-button>
          </div>

          <span class="bbn-r"><?=_("Languages configured for translation of this project:")?></span>
          <div v-if="source.configured_langs">
            <div v-for="c in source.configured_langs"
                 class="bbn-b bbn-i"
                 v-text="get_field(source.primary, 'id', c, 'text')"
                 style="display:inline;padding-right:6px"></div>
          </div>
          <div v-if="id_project !== 'options'"
               class="bbn-grid-full"
               style="position:absolute; right: 7px; top: 7px;">
            <bbn-button icon="fa fa-table"
                        :noText="true"
                        @click="link_projects_table"
                        title="Open table view of projects"
            ></bbn-button>
          </div>
          <div v-else
               class="bbn-grid-full bbn-large bbn-c"
          >
            <a href="options/tree"
               title="<?=_("Go to options' tree, choice the option you want to translate")?>"
            ><?=_("Follow the link to configure other options for translation")?></a>
          </div>
        </div>

      </div>

    </bbn-pane>


    <bbn-pane v-if="widgets.length">

      <bbn-dashboard :source="widgets"
      >
      </bbn-dashboard>
    </bbn-pane>
    <bbn-pane v-else>
      <bbn-loader></bbn-loader>
    </bbn-pane>
  </bbn-splitter>
</div>