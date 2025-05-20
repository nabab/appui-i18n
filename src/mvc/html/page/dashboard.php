<div class="appui-i18n-dashboard bbn-overlay bbn-flex-height">
  <div class="bbn-spadding bbn-bottom-xsspace">
    <div :class="['appui-i18n-dashboard-toolbar', 'bbn-alt-background', 'bbn-radius', 'bbn-spadding', 'bbn-vmiddle', 'bbn-nowrap', {
            'bbn-flex-width': !isMobile(),
            'bbn-flex-height': !!isMobile()
          }]">
      <div :class="['bbn-background', 'bbn-vmiddle', 'bbn-radius', 'bbn-flex-fill', {
              'bbn-hspadding': !isMobile(),
              'bbn-spadding': isMobile()
            }]"
            style="min-height: 2rem; flex-wrap: wrap">
        <div :class="['bbn-vxsmargin', {
              'bbn-vmiddle bbn-right-lspace': !isMobile(),
              'bbn-right-space': isMobile()
            }]">
          <div :class="['bbn-upper', 'bbn-right-space', 'bbn-b', 'bbn-secondary-text-alt', {'bbn-bottom-xsspace': isMobile()}]"
                bbn-text="_('Select a project')"/>
          <bbn-dropdown :url="source.root + 'page/dashboard'"
                        :source="source.projects"
                        bbn-model="idProject"
                        @change="loadProject"
                        source-value="id"
                        source-text="name"/>
        </div>
        <bbn-button bbn-if="!isOptionsProject"
                    icon="nf nf-fa-cogs"
                    label="<?= _("Config project languges") ?>"
                    @click="openProjectLanguagesCfg"
                    title="<?= _("Configure languages for this project") ?>"
                    class="bbn-right-space bbn-vxsmargin bbn-no-border"/>
        <bbn-button icon="nf nf-fa-user"
                    label="<?= _("User activity") ?>"
                    @click="openUserActivity"
                    class="bbn-right-space bbn-vxsmargin bbn-no-border"/>
        <bbn-button icon="nf nf-fa-users"
                    label="<?= _("Users activity") ?>"
                    @click="openUsersActivity"
                    class="bbn-right-space bbn-vxsmargin bbn-no-border"/>
        <bbn-button icon="nf nf-fa-flag"
                    label="<?= _("Glossary table") ?>"
                    @click="openGlossary"
                    class="bbn-right-space bbn-vxsmargin bbn-no-border"/>
      </div>
      <div :class="['bbn-upper', 'bbn-b', 'bbn-lg', 'bbn-tertiary-text-alt', {
              'bbn-left-lspace bbn-right-space': !isMobile(),
              'bbn-top-space bbn-bottom-space': !!isMobile(),
            }]"
            bbn-text="_('i18n')"/>
    </div>
  </div>
  <div class="bbn-flex-fill bbn-flex-height bbn-hspadding">
    <div class="appui-i18n-dashboard-head bbn-alt-background bbn-radius-top bbn-spadding bbn-middle">
      <div bbn-if="!isOptionsProject"
           class="bbn-medium bbn-vmiddle"
           style="flex-wrap: wrap !important">
        <span class="bbn-right-sspace bbn-tertiary-text-alt bbn-upper bbn-b"><?= _("The source language for this project is") ?></span>
        <bbn-dropdown :source="source.primary"
                      bbn-model="currentProjectLanguage"
                      placeholder="<?= _('Select a language') ?>"
                      class="appui-i18n-dashboard-head-lang bbn-b bbn-primary-text-alt bbn-vxsmargin bbn-no-border"
                      source-value="code"
                      component="appui-i18n-lang"/>
      </div>
      <div bbn-else
           class="bbn-large">
        <a :href="optionsRoot + 'tree'"
            title="<?= _("Choose the option you want to translate from the options' tree") ?>"
            bbn-text="_('Click here to configure the languages on the options\' tree')"/>
      </div>
      <div bbn-if="source.configured_langs"
           class="bbn-vmiddle bbn-top-sspace"
           style="flex-wrap: wrap !important">
        <span bbn-if="!isOptionsProject && source.configured_langs.length"
              class="bbn-medium bbn-right-sspace bbn-tertiary-text-alt bbn-upper bbn-b">
          <?= _("Languages configured for translation of this project") ?>
        </span>
        <span bbn-else-if="!isOptionsProject && !source.configured_langs.length"
              class="bbn-medium bbn-medium bbn-right-sspace bbn-tertiary-text-alt bbn-upper bbn-b">
          <?= _("There are no languages configured for the translation of this project") ?>
        </span>
        <span bbn-else
              class="bbn-medium bbn-medium bbn-right-sspace bbn-tertiary-text-alt bbn-upper bbn-b">
          <?= _("Languages found in the main project's options") ?>:
        </span>
        <span bbn-for="c in source.configured_langs"
             class="bbn-radius bbn-xspadding bbn-background bbn-nowrap bbn-right-sspace bbn-vxsmargin">
          <appui-i18n-lang :code="getField(primary, 'code', {id: c})"/>
        </span>
      </div>
    </div>
    <div class="bbn-flex-fill bbn-border bbn-radius-bottom bbn-bottom-sspace"
         style="border-top: 0; border-color: var(--header-background); border-width: 0.25rem !important;">
      <bbn-dashboard bbn-if="widgets.length"
                    :source="widgets"
                    ref="dashboard"
                    :scrollable="true"/>
      <bbn-loader bbn-else font-size="l"/>
    </div>
  </div>
  <div bbn-if="isLoading"
       class="bbn-overlay bbn-modal">
    <bbn-loader font-size="l"/>
  </div>
</div>
