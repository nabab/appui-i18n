<div class="bbn-overlay dashboard-splitter-container">
  <bbn-splitter orientation="vertical"
                :resizable="true"
  >

    <bbn-pane :collapsible="true"
              :resizable="true"
              size="17%"

    >
      <div class="bbn-overlay config-container">

          <div class="first">
            <span class="bbn-b bbn-medium"><?=_('Select a project')?></span>
            <bbn-dropdown :url="source.root + 'page/dashboard'"
                          :source="dd_projects"
                          v-model="id_project"
                          @change="load_widgets"
            ></bbn-dropdown>
          </div>

          <div class="second bbn-b bbn-medium bbn-w-100" v-if="id_project !== 'options'">
            <div class="bbn-w-50 bbn-right">
              <span><?=_("The source language for this project is")?>:</span>
            </div>
            <div class="bbn-w-50 bbn-grid-fields" style="height:30px">
              <div>
                <span class="bbn-green bbn-medium bbn-hpadded" v-text="languageText"></span>
                <i :class="['bbn-large', 'bbn-p' ,{
                    'nf nf-fa-edit' : !changingProjectLang,
                    'nf nf-fa-times': changingProjectLang
                  }]" 
                    @click="changingProjectLang = !changingProjectLang"
                    :title="_('Change the project source lang')"
                ></i>
              </div>
              <div v-show="changingProjectLang" >
                <bbn-dropdown :source="dd_primary"
                              v-model="language"
                              @change="set_project_language"
                              placeholder="<?=_('Select a language')?>"
                ></bbn-dropdown>
              </div>
            </div>
          </div>
          <div v-else
               class="bbn-large second"
          >
            <a :href="optionsRoot + 'tree'"
               title="<?=_("Choose the option you want to translate from the options' tree")?>"
            ><?=_("Follow the link to configure other options for translation")?></a>
          </div>

          <div class="third" v-show="source.configured_langs">
            <span v-if="(id_project !== 'options') && source.configured_langs.length"  class="bbn-b bbn-medium">
              <?=_("Languages configured for translation of this project")?>:
            </span>
            <span  class="bbn-b bbn-medium" v-else-if="(id_project !== 'options') && !source.configured_langs.length">
              <?=_("There are no languages configured for the translation of this project")?>
            </span>
            <span v-else class="bbn-b bbn-medium"><?=_("Languages configured for options translation")?>:</span>
            <div class="langs">
              <div v-for="c in source.configured_langs"
                   class="bbn-i bbn-medium"
                   v-text="getField(primary, 'text', {id: c})"
              ></div>
            </div>

          </div>

          <div class="fourth">
            <div style="max-height: 25px;" class="bbn-grid-full bbn-c" >
              <bbn-button icon="nf nf-fa-cogs"
                          text="<?=_("Config project languges")?>"
                          @click="cfg_project_languages"
                          title="<?=_("Configure languages for this project")?>"
                          v-if="id_project !== 'options'"
              ></bbn-button>

              <bbn-button icon="nf nf-fa-user"
                          text="<?=_("User activity")?>"
                          @click="open_user_activity"
              ></bbn-button>

              <bbn-button icon="nf nf-fa-users"
                          text="<?=_("Users activity")?>"
                          @click="open_users_activity"
              ></bbn-button>

              <bbn-button icon="nf nf-fa-flag"
                          text="<?=_("Glossary table")?>"
                          @click="open_glossary_table"
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