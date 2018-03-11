<div class="bbn-full-screen appui-dashboard-splitter-container">
  <bbn-splitter orientation="vertical">

    <bbn-pane size="15%">

      <div class="bbn-grid-fields bbn-padded">
        <span>Select a project</span>
        <bbn-dropdown :source="dd_projects"
                      v-model="id_project"
                      @change="load_widgets"
        ></bbn-dropdown>

        <span>Languages configured for translation of this project:</span>

        <div>
          <div v-for="(c, i) in source.configured_langs" v-text="get_field(source.primary, 'code', i, 'text')" style="display:inline;padding-right:6px"></div>
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