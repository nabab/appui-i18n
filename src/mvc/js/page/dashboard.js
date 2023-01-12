(() => {
  return {
    name: 'appui-i18n-dashboard',
    props: {
      source: {
        type: Object
      }
    },
    data(){
      return {
        idProject: !!this.source.projects.length ? bbn.fn.getField(this.source.projects, 'id', 'name', bbn.env.appName) : '',
        primary: this.source.primary,
        language: !!this.source.projects.length ? this.source.projects[0].lang : '',
        optionsRoot: appui.plugins['appui-option'] + '/',
        root: appui.plugins['appui-i18n'] + '/'
      }
    },
    computed: {
      isOptionsProject(){
        return this.idProject === 'options';
      },
      currentProject(){
        return !!this.idProject ? bbn.fn.getRow(this.source.projects, 'id', this.idProject) : {};
      },
      languageText(){
        if (this.primary) {
          return bbn.fn.getField(this.primary, 'text', 'code', this.language);
        }
        return null;
      },
      widgets(){
        let res = [];
        if (this.source.data){
          let buttons = [{
            text: bbn._('Update widget data'),
            icon: 'nf nf-fa-retweet',
            action: 'remake_cache'
          }];
          if (!this.isOptionsProject) {
            buttons.push({
              text: bbn._('Setup languages'),
              icon: 'nf nf-fa-flag',
              action: 'generate'
            }, {
              text: bbn._('Open the table of strings'),
              icon: 'nf nf-fa-book',
              action: 'open_strings_table',
            }, {
              text: bbn._('Delete locale folder'),
              icon: 'nf nf-fa-trash',
              action: 'delete_locale_folder',
            });
          }
          else {
            buttons.push({
              text: bbn._('Open the table of strings'),
              icon: 'nf nf-fa-book',
              action: 'open_strings_table',
            });
          }
          if (bbn.fn.isArray(this.source.data)
            && this.source.data.length
          ) {
            bbn.fn.each(this.source.data, v => {
              if (v.id || v.code) {
                res.push({
                  title: v.title + (this.isOptionsProject ? ` (${v.code})` : ''),
                  key: v.id || v.code,
                  component : 'appui-i18n-widget',
                  id_project: this.idProject,
                  buttonsRight: buttons,
                  source: v
                });
              }
            });
          }
          return res;
        }

      },
      //source to choose the translation language using the popup
      dd_translation_lang(){
        let res = [];
        bbn.fn.each(this.primary, (v, i) => {
          res.push({text: v.text, value: v.code })
        })
        return res;
      }
    },
    methods: {
      isMobile: bbn.fn.isMobile,
      setProjectLanguage(){
        this.post(this.root + 'actions/project_lang', {
          id_project: this.idProject,
          language: this.language
        }, d => {
          if (d.success) {
            let idx = bbn.fn.search(this.source.projects, 'id', this.idProject);
            if ( idx > -1 ){
              this.source.projects[idx].lang = this.language;
            }
            appui.success(bbn._('Project source language successfully updated'))
          }
        })
      },
      openUsersActivity(){
        bbn.fn.link(this.root + 'page/history/');
      },
      openUserActivity(){
        bbn.fn.link(this.root + 'page/user_history');
      },
      openGlossary(){
        //open a component popup to select source language and translation language for the table glossary
          var tab = this.closest('bbn-container').getComponent();
          this.getPopup().open({
            scrollable: false,
            width: 400,
            height: 250,
            source: {
              source_lang: false,
              translation_lang: false,
              primary: tab.primary,
              dd_source_lang: tab.dd_source_lang,
              dd_translation_lang: tab.dd_translation_lang,
            },
            component: tab.$options.components.cfg_translations_form,
            title: bbn._('Config your translation tab')
          })

        },

      openProjectLanguagesCfg(){
        this.getPopup().open({
          width: 600,
          height: 300,
          title: bbn._("Config translation languages for the project"),
          component: this.$options.components.languagesForm,
          componentOptions: {
            source: {
              langs: this.source.configured_langs,
              idProject: this.idProject
            },
            currentLanguage: this.language,
            primariesLanguages: this.primary
          }
        })
      },
      getField: bbn.fn.getField,
      loadWidgets(){
        this.source.data.splice(0);
        this.source.configured_langs.splice(0);
        this.post(this.root + 'data/dashboard', {idProject: this.idProject}, d => {
          if (d.success) {
            this.source.data.push(...d.paths);
            this.source.configured_langs.push(...d.langs);
          }
        });
      }
    },
    created(){
      appui.register('appui-i18n-dashboard', this);
    },
    watch : {
      idProject(val){
        if (val){
          this.language = bbn.fn.getField(this.source.projects, 'lang', 'id', val)
        }
      }
    },
    components: {
      'cfg_translations_form': {
        template: `<bbn-form :source="source.row"
                  @submit="link"
                  :prefilled="true"
                  @cancel="cancel"
                  :scrollable="false"
        >
          <div class="bbn-flex-height">
            <div class="bbn-grid-fields bbn-flex-fill bbn-padded bbn-c">
              <span>
                <?=_('Select source language')?>:
              </span>
              <div>
                <bbn-dropdown placeholder="Choose" :source="source.dd_translation_lang" v-model="source.source_lang"></bbn-dropdown>
              </div>

              <span>
                <?=_('Select a language for the translation')?>:
              </span>
              <div>
                <bbn-dropdown placeholder="Choose" :source="source.dd_translation_lang" v-model="source.translation_lang"></bbn-dropdown>
              </div>
            </div>
          </div>
        </bbn-form>`,
        props:['source'],
        methods: {
          link(){
            bbn.fn.link(this.root + 'page/glossary/' + this.source.source_lang + '/' + this.source.translation_lang);
          },
          cancel(){
            this.getPopup().close();
          },
        }
      },
      languagesForm: {
        props: {
          source: {
            type: Object
          },
          currentLanguage: {
            type: String
          },
          primariesLanguages: {
            type: Array
          }
        },
        template: `
          <bbn-form :scrollable="true"
                    :source="source"
                    ref="form"
                    :action="root + 'actions/languages_form'"
                    confirm-leave="<?php echo _('Are you sure you want to exit without saving changes?'); ?>"
                    :prefilled="true"
                    @success="success">
            <div class="bbn-padded bbn-grid"
                 style="grid-template-columns: repeat(3, 1fr)">
              <div v-for="l in primariesLanguages"
                  class="bbn-spadded bbn-radius bbn-alt-background">
                <bbn-checkbox :id="l.id"
                              :checked="source.langs.includes(l.id)"
                              @change="toggleLang"
                              :label="l.text"
                              component="appui-i18n-lang"
                              :componentOptions="{code: l.code}"
                              :disabled="l.code === currentLanguage"/>
              </div>
            </div>
          </bbn-form>
        `,
        data(){
          return {
            root: appui.plugins['appui-i18n'] + '/'
          }
        },
        methods:{
          success(d){
            if (d.success) {
              appui.success(bbn._('Languages successfully updated'))
            }
            else {
              appui.error();
            }
          },
          toggleLang(val, obj){
            let idx = this.source.langs.indexOf(obj.id);
            if (idx > -1) {
              this.source.langs.splice(idx, 1);
            }
            else {
              this.source.langs.push(obj.id)
            }
          }
        },

      }
    }
  }

})();
