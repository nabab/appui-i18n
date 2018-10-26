<div class="bbn-full-screen dashboard-splitter-container">
  <bbn-splitter orientation="vertical"
                :resizable="true"
  >

    <bbn-pane :collapsible="true"
              :resizable="true"
              size="17%"

    >
      <div class="bbn-full-screen config-container">

          <div class="first">
            <span>Select a project</span>
            <bbn-dropdown :url="source.root + 'page/dashboard'"
                          :source="dd_projects"
                          v-model="id_project"
                          @change="load_widgets"
            ></bbn-dropdown>
          </div>
          <div class="second" v-if="id_project !== 'options'">
            <span><?=_("The source language for this project is")?>:</span>
            <span v-text="get_field(source.primary, 'code', get_field(source.projects, 'id', id_project, 'lang'), 'text')"></span>
          </div>
          <div v-else
               class="bbn-large second"
          >
            <a href="options/tree"
               title="<?=_("Go to options' tree, choice the option you want to translate")?>"
            ><?=_("Follow the link to configure other options for translation")?></a>
          </div>

          <div class="third" v-show="source.configured_langs">
            <span v-if="id_project !== 'options'"><?=_("Languages configured for translation of this project")?>:</span>
            <span v-else><?=_("Languages configured for options translation")?>:</span>
            <div class="langs ">
              <div v-for="c in source.configured_langs"
                   class="bbn-b bbn-i"
                   v-text="get_field(source.primary, 'id', c, 'text')"
              ></div>
            </div>

          </div>

          <div class="fourth">
            <div style="max-height: 25px;" class="bbn-grid-full bbn-c" >
              <bbn-button icon="fas fa-cogs"
                          :noText="true"
                          @click="cfg_project_languages"
                          title="<?=_("Configure languages for this project")?>"
                          v-if="id_project !== 'options'"
              ></bbn-button>

              <bbn-button icon="fas fa-user"
                          :noText="true"
                          @click="open_user_activity"
                          title="<?=_("User activity")?>"
              ></bbn-button>

              <bbn-button icon="fas fa-users"
                          :noText="true"
                          @click="open_users_activity"
                          title="<?=_("Users activity")?>"
              ></bbn-button>

              <bbn-button icon="fab fa-font-awesome-flag"
                          :noText="true"
                          @click="open_glossary_table"
                          title="<?=_("Glossary tables")?>"
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