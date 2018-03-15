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
          <span>Select a project</span>
          <bbn-dropdown :source="dd_projects"
                        v-model="id_project"
                        @change="load_widgets"
          ></bbn-dropdown>


          <span>Languages configured for translation of this project:</span>
          <div v-if="langs.length">
            <div v-for="c in langs"
                 v-text="get_field(source.primary, 'id', c, 'text')"
                 style="display:inline;padding-right:6px"></div>
          </div>

          <span>Configure languages for this project</span>
          <div>
            <bbn-button icon="fa fa-flag"
                        :noText="true"
                        @click="cfg_project_languages"
            ></bbn-button>
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