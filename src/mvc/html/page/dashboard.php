<div class="appui-i18n-dashboard bbn-overlay bbn-flex-height bbn-alt-background">
  <div class="bbn-padded">
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
                          :source="source.projects"
                          v-model="idProject"
                          @change="loadWidgets"
                          source-value="id"
                          source-text="name"/>
          </div>
          <bbn-button v-if="!isOptionsProject"
                      icon="nf nf-fa-cogs"
                      text="<?=_("Config project languges")?>"
                      @click="openProjectLanguagesCfg"
                      title="<?=_("Configure languages for this project")?>"
                      class="bbn-right-space bbn-vxsmargin bbn-no-border"/>
          <bbn-button icon="nf nf-fa-user"
                      text="<?=_("User activity")?>"
                      @click="openUserActivity"
                      class="bbn-right-space bbn-vxsmargin bbn-no-border"/>
          <bbn-button icon="nf nf-fa-users"
                      text="<?=_("Users activity")?>"
                      @click="openUsersActivity"
                      class="bbn-right-space bbn-vxsmargin bbn-no-border"/>
          <bbn-button icon="nf nf-fa-flag"
                      text="<?=_("Glossary table")?>"
                      @click="openGlossary"
                      class="bbn-right-space bbn-vxsmargin bbn-no-border"/>
        </div>
        <div :class="['bbn-upper', 'bbn-b', 'bbn-lg', 'bbn-tertiary-text-alt', {
              'bbn-left-lspace bbn-right-space': !isMobile(),
              'bbn-top-space bbn-bottom-space': !!isMobile(),
            }]"
            v-text="_('i18n')"/>
      </div>
    </div>
    <div class="appui-i18n-dashboard-head bbn-background bbn-radius bbn-padded bbn-middle">
      <div v-if="!isOptionsProject"
           class="bbn-medium bbn-vmiddle"
           style="flex-wrap: wrap !important">
        <span class="bbn-right-sspace"><?=_("The source language for this project is")?>:</span>
        <bbn-dropdown :source="source.primary"
                      v-model="language"
                      @change="setProjectLanguage"
                      placeholder="<?=_('Select a language')?>"
                      class="appui-i18n-dashboard-head-lang bbn-b bbn-primary-text-alt bbn-vxsmargin"
                      source-value="code"
                      component="appui-i18n-lang"/>
      </div>
      <div v-else
           class="bbn-large">
        <a :href="optionsRoot + 'tree'"
            title="<?=_("Choose the option you want to translate from the options' tree")?>"
            v-text="_('Follow the link to configure other options for translation')"/>
      </div>
      <div v-if="source.configured_langs"
           class="bbn-vmiddle bbn-top-sspace"
           style="flex-wrap: wrap !important">
        <span v-if="!isOptionsProject && source.configured_langs.length"
              class="bbn-medium bbn-right-sspace">
          <?=_("Languages configured for translation of this project")?>:
        </span>
        <span v-else-if="!isOptionsProject && !source.configured_langs.length"
              class="bbn-medium bbn-medium bbn-right-sspace">
          <?=_("There are no languages configured for the translation of this project")?>
        </span>
        <span v-else
              class="bbn-medium bbn-medium bbn-right-sspace">
          <?=_("Languages configured for options translation")?>:
        </span>
        <span v-for="c in source.configured_langs"
             class="bbn-radius bbn-spadded bbn-alt-background bbn-nowrap bbn-right-sspace bbn-vxsmargin">
          <appui-i18n-lang :code="getField(primary, 'code', {id: c})"/>
        </span>
      </div>
    </div>
  </div>
  <div class="bbn-flex-fill">
    <bbn-dashboard v-if="widgets.length"
                   :source="widgets"
                   ref="dashboard"/>
    <bbn-loader v-else/>
  </div>
</div>
