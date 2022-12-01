<div class="appui-i18n-dashboard bbn-overlay bbn-flex-height">
  <div class="bbn-alt-background bbn-padded">
    <div class="appui-i18n-dashboard-toolbar bbn-bottom-space">
      <div :class="['bbn-spadded', 'bbn-background', 'bbn-radius', 'appui-task-box-shadow', 'bbn-vmiddle', 'bbn-nowrap', {
            'bbn-flex-width': !isMobile(),
            'bbn-flex-height': !!isMobile()
          }]">
        <div :class="['bbn-alt-background', 'bbn-vmiddle', 'bbn-radius', 'bbn-flex-fill', {
               'bbn-hspadded': !isMobile(),
               'bbn-spadded': isMobile()
             }]"
             style="min-height: 2rem; flex-wrap: wrap">
          <div :class="['bbn-vxsmargin', {
                'bbn-vmiddle bbn-right-lspace': !isMobile(),
                'bbn-right-space': isMobile()
              }]">
            <div :class="['bbn-upper', 'bbn-right-space', 'bbn-b', 'bbn-secondary-text-alt', {'bbn-bottom-xsspace': isMobile()}]"
                 v-text="_('Select a project')"/>
            <bbn-dropdown :url="source.root + 'page/dashboard'"
                          :source="dd_projects"
                          v-model="id_project"
                          @change="load_widgets"/>
          </div>
        </div>
        <div :class="['bbn-upper', 'bbn-b', 'bbn-lg', 'bbn-tertiary-text-alt', {
              'bbn-left-lspace bbn-right-space': !isMobile(),
              'bbn-top-space bbn-bottom-space': !!isMobile(),
            }]"
            v-text="_('i18n')"/>
      </div>
    </div>
    <div class="bbn-background bbn-radius bbn-spadded">
      <div v-if="id_project !== 'options'"
            class="second bbn-b bbn-medium bbn-w-100">
        <div class="bbn-w-50 bbn-right">
          <span><?=_("The source language for this project is")?>:</span>
        </div>
        <div class="bbn-w-50 bbn-grid-fields"
              style="height:30px">
          <div>
            <span class="bbn-green bbn-medium bbn-hpadded"
                  v-text="languageText"/>
            <i :class="['bbn-large', 'bbn-p', {
                  'nf nf-fa-edit' : !changingProjectLang,
                  'nf nf-fa-times': changingProjectLang
                }]"
                @click="changingProjectLang = !changingProjectLang"
                :title="_('Change the project source lang')"/>
          </div>
          <div v-show="changingProjectLang">
            <bbn-dropdown :source="dd_primary"
                          v-model="language"
                          @change="set_project_language"
                          placeholder="<?=_('Select a language')?>"/>
          </div>
        </div>
      </div>
      <div v-else
            class="bbn-large second">
        <a :href="optionsRoot + 'tree'"
            title="<?=_("Choose the option you want to translate from the options' tree")?>"
            v-text="_('Follow the link to configure other options for translation')"/>
      </div>
      <div v-show="source.configured_langs"
            class="third">
        <span v-if="(id_project !== 'options') && source.configured_langs.length"
              class="bbn-b bbn-medium">
          <?=_("Languages configured for translation of this project")?>:
        </span>
        <span v-else-if="(id_project !== 'options') && !source.configured_langs.length"
              class="bbn-b bbn-medium">
          <?=_("There are no languages configured for the translation of this project")?>
        </span>
        <span v-else
              class="bbn-b bbn-medium">
          <?=_("Languages configured for options translation")?>:
        </span>
        <div class="langs">
          <div v-for="c in source.configured_langs"
                class="bbn-i bbn-medium"
                v-text="getField(primary, 'text', {id: c})"/>
        </div>
      </div>
      <div class="fourth">
        <div style="max-height: 25px;"
            class="bbn-grid-full bbn-c">
          <bbn-button v-if="id_project !== 'options'"
                      icon="nf nf-fa-cogs"
                      text="<?=_("Config project languges")?>"
                      @click="cfg_project_languages"
                      title="<?=_("Configure languages for this project")?>"/>
          <bbn-button icon="nf nf-fa-user"
                      text="<?=_("User activity")?>"
                      @click="open_user_activity"/>
          <bbn-button icon="nf nf-fa-users"
                      text="<?=_("Users activity")?>"
                      @click="open_users_activity"/>
          <bbn-button icon="nf nf-fa-flag"
                      text="<?=_("Glossary table")?>"
                      @click="open_glossary_table"/>
        </div>
      </div>
    </div>
  </div>
  <div class="bbn-flex-fill">
    <bbn-dashboard v-if="widgets.length"
                   :source="widgets"/>
    <bbn-loader v-else/>
  </div>
</div>
