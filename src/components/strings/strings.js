(() => {
  return {
    props: ['source'],
    data(){
      return {
        column_length: true,
        hidden_cols: [],
        showAlert: false,
        root: appui.plugins['appui-i18n'] + '/'
      };
    },
    computed: {
      isOptions(){
        return !!this.source
          && !!this.source.res
          && !!this.source.id_project
          && !!this.source.res.id_option
          && (this.source.id_project === 'options')
          && (this.source.res.id_option.length === 2)
      },
      /** the source language of this id_option */
      source_lang(){
        return bbn.fn.getField(this.source.primary, 'text', 'code' , this.source.res.path_source_lang)
      },
      /**array of columns for the table*/
      columns(){
        let r = [];
        bbn.fn.each(this.source.res.languages, l => {
          let text = bbn.fn.getField(this.source.primary, 'text', 'code', l);
          let obj = {
            field: l + '_db',
            title:  (l === this.source.res.path_source_lang) ?
              (`${text} <i class="nf nf-fa-asterisk" title="` + bbn._('This is the original language of the expression') + `"/>`) :
              text,
            editable: true
          };
          if (!this.isOptions) {
            obj.render = row => {
              let translation_db = row[l + '_db'];
              let translation_po = row[l + '_po'];
              if ((translation_db !== false)
                && !!translation_db.length
                && (translation_db === translation_po)
              ) {
                return `${translation_db} <i class="nf nf-fa-check bbn-large bbn-green" title="` + bbn._('Expression correctly inserted in db and po file') + `" style="float:right"/>`;
              }
              else if (translation_db.length
                && (translation_db !== translation_po)
                && (!this.isOptions)
              ) {
                return  `<span title="` + (translation_po.length ? bbn._('The translation in the po file is different from the one in database') : '') + `"
                               class="${translation_po.length ? 'bbn-orange' : 'bbn-red'}">
                          ${translation_db}
                        </span>
                        <i style="float:right"
                           class="${translation_po.length ? 'nf nf-fa-exclamation' : 'nf nf-fa-exclamation_triangle'} bbn-large ${translation_po.length ? 'bbn-orange' : 'bbn-red'}"
                           title="` + (translation_po ? (bbn._('The translation in the po file is') + ': ' + translation_po) : bbn._('Translation missing in po file')) + `"/>
                        `;
              }
              else if (translation_db === false) {
                return '';
              }
            }
          }
          r.push(obj);
        });
        /** column occurrence --- doesn't exist for project option */
        if (!this.isOptions){
          r.push({
            ftitle: bbn._('Number of occurrences of the strings in the path files'),
            title: '#',
            field: 'occurrence',
            editable: false,
            render(row){
              return row.occurrence ? row.occurrence : 0;
            },
            width: 40,
            cls: 'bbn-c'
          });
        }
        r.push({
          ftitle: bbn._('Remove original expression'),
          buttons: this.buttons,
          width: this.isOptions ? 50 : 90,
          cls: 'bbn-c'
        });
        return r;
      },
    },
    methods: {
      /** generate po files for all columns of the table */
      generate(){
        this.showAlert = true;
        if ( this.source.res.languages.length ){
          this.post(this.root + 'actions/generate', {
            id_option: this.source.res.id_option,
            languages: this.source.res.languages,
            id_project: this.source.id_project,
            language: this.source.res.path_source_lang
          }, d => {
            if (d.success) {
              d.languages = d.languages.map(v => {
                return bbn.fn.getField(this.source.primary, 'text', 'code', v);
              });
              let i18n = this.closest('appui-i18n');
              if (i18n) {
                let dashboardPage = i18n.find('appui-i18n-dashboard');
                if (dashboardPage
                  && !!dashboardPage.idProject
                  && (dashboardPage.idProject === this.source.id_project)
                ) {
                  let dashboard = dashboardPage.getRef('dashboard');
                  if (dashboard) {
                    let widget = dashboard.getWidget(this.source.res.id_option);
                    if (widget) {
                      this.$set(widget.source, 'data_widget', d.widget);
                    }
                  }
                }
              }
              this.source.res.strings = d.strings;
              appui.success(bbn._('Files of translation successfully updated for %s', d.languages.join(' ' + bbn._('and') + ' ')));
              this.$nextTick(() => {
                this.showAlert = false;
              });
            }
          });
        }
        else {
          this.alert(bbn._('You have to configure at least a language using the button %s of the widget in the dashboard', '<i class="nf nf-fa-flag"></i>'));
          this.showAlert = false;
        }
      },
      /** checks if there are new strings in the files of the path */
      find_strings(){
        this.post(this.root + 'actions/find_strings', {
          id_option: this.source.res.id_option,
          language: this.source.res.path_source_lang,
          languages: this.source.res.languages,
          id_project: this.source.id_project
        }, d => {
          if (d.success) {
            if (!!d.done) {
              appui.success(bbn._('%d new strings found', d.done));
              this.generate();

            }
            else {
              appui.warning(bbn._('No new strings found'));
            }
          }
        } );
      },
      /** button delete row of table */
      buttons(){
        let res = [];
        res.push({
          action: this.delete_expression,
          icon: 'nf nf-fa-times',
          title: bbn._('Delete original expression'),
          notext: true
        });
        if ( this.source.id_project !== 'options' ){
          res.push({
            action: this.showPath,
            icon: 'nf nf-mdi-sign_direction',
            title: bbn._('Show files containing the string'),
            notext: true
          });
        }
        return res;
      },
      //opens the popup containing the link(s) to the file(s) containing the string
      showPath(row){
        this.getPopup({
          title: bbn._('File(s) containing the string'),
          source: row,
          component:this.$options.components.showPath,
          height: 500,
          width: 400
        });
      },
      /** deletes the original expression from db, if the expression is not deleted before from the file (using the link of the expander to the code) it will be again in the table when the table is reloaded or updated */
      delete_expression(row, ob, idx){
        bbn.fn.log('arguments',arguments)
        let id_exp = row.id_exp,

          data = this.find('bbn-table').currentData;
          //idx = bbn.fn.search(data, { id_exp: id_exp });
        this.confirm(bbn._('Did you remove the expression from code before to delete the row?'), () => {
          this.post(this.root + 'actions/delete_expression', {id_exp: row.id_exp, exp: row.exp},  d => {
            bbn.fn.log('succesws',d)
            if (d.success) {
              //this.$refs.strings_table.updateData();
              appui.success(bbn._('Expression deleted '));
              this.$nextTick(()=>{
                this.$refs.strings_table.currentData.splice(idx, 1);
              })
            }
            else{
              appui.error(bbn._('An error occurred while deleting the expression'));
            }
          } );
        })
      },
      /** called at @change of the table (when the idx of the row focused changes), insert translation in db and remake the po file */
      insertTranslation(row){
        if (!!row) {
          this.post(this.root + 'actions/insert_translations', {
            row: row,
            langs: this.source.res.languages,
            id_option: this.source.res.id_option,
            id_project: this.source.id_project
          }, d => {
            if (!d.success) {
              appui.error(bbn._('An error occurred while saving translation'));
            }
            else {
              appui.success(bbn._('Translation saved'));
              if (d.deleted
                && d.deleted.length
                && !this.isOptions
              ) {
                appui.warning(bbn._('Please be sure to remake po files'));
              }
              if (!!d.widget) {
                let i18n = this.closest('appui-i18n');
                if (i18n) {
                  let dashboardPage = i18n.find('appui-i18n-dashboard');
                  if (dashboardPage
                    && !!dashboardPage.idProject
                    && (dashboardPage.idProject === this.source.id_project)
                  ) {
                    let dashboard = dashboardPage.getRef('dashboard');
                    if (dashboard) {
                      let widget = dashboard.getWidget(this.source.res.id_option);
                      if (widget) {
                        this.$set(widget.source, 'data_widget', d.widget);
                      }
                    }
                  }
                }
              }
            }
          });
        }
      },
      /** remakes the model of table in cache */
      remake_cache(){
        this.column_length = false;
        //this.generate();
        this.showAlert = true;
        this.post(this.root + 'actions/reload_table_cache', {
          id_option: this.source.res.id_option,
          id_project: this.source.id_project,
          routes: this.root
        }, (d) => {
          if ( d.success ){
            let diff = ( d.res.total - this.source.res.total );
            this.source.res.languages = d.res.languages;
            this.source.res.strings = d.res.strings;
            //this.find(this, 'bbn-table').updateData();
            if ( diff > 0 ){
              appui.warning(bbn._('%d new string(s) found in %s', diff, this.source.res.path));
              this.source.res.strings = d.res.strings;
              this.source.res.total = d.res.total;
              //this.find(this, 'bbn-table').updateData();
            }
            else if ( diff < 0 ){
              appui.warning(bbn._('%d string(s) deleted from %s files', Math.abs(diff), this.source.res.path));
              this.source.res.strings = d.res.strings;
              this.source.res.total = d.res.total;
            }
            else if ( diff = 0 ){
              appui.warning(bbn._('There are no changes in data'));
            }
            this.$nextTick(() => {
              this.showAlert = false;
              this.column_length = true;
            });
            let router = this.closest('bbn-router');
            /*if( this.find(router, 'bbn-dashboard') !== undefined ){
              let dashboard = this.find(router, 'bbn-dashboard'),
                tab = this.closest(dashboard, 'bbns-container'),
                cp = tab.getComponent(),
                data = cp.source.data,
                widget_idx = bbn.fn.search(data, 'id', this.source.res.id_option);
            }*/
          }
        })
      },
    },
    watch: {
      showAlert(val){
        if ( val){
          //this.alert(bbn._('Wait for the ending of the process before to make other actions in this tab') );
        }
        else{
          //this.getPopup().close()
        }
      },
      hidden_cols(val){
        /** function to make the difference between two arrays */
        Array.prototype.diff = function (a) {
          return this.filter(function (i) {
            return a.indexOf(i) === -1;
          });
        };
        /** creates the array not hidden making the diff between checked and unchecked langs */
        var not_hidden = this.source.res.languages.diff(val);
        /** val is the array of checked langs */
        val.forEach( (v, i) => {
          /** the index of the column to hide */
          let col_idx = bbn.fn.search(this.columns, 'field', v + '_db'),
            /** the column to hide */
            col = this.columns[col_idx],
            //idx = $.inArray(v, this.hidden_cols);
            idx = this.hidden_cols.indexOf(v);
          if ( idx > -1 ){
            col.hidden = true;
          }
        });
        /** loop on the array of not to hide langs */
        if ( not_hidden.length ){
          not_hidden.forEach( (v, i) => {
            let col_idx = bbn.fn.search(this.columns, 'field', v + '_db'),
              col = this.columns[col_idx];
            col.hidden = false;
          })
        }
        this.find('bbn-table').updateData();
      },
    },
    components:{
      showPaths: {
        template: `
          <div class="bbn-vpadded">
            <span class="bbn-spadded bbn-w-100"
                  v-for="p in source.paths">
              <a v-text="p"
                 @click="linkToIde(p)"
                 class="bbn-p"/>
            </span>
          </div>`,
        props:['source'],
        methods: {
          linkToIde(path){
            let idx = path.lastIndexOf('/'),
              st = 'ide/editor/file/',
              ext = path.slice(idx, path.length);
            st += path.slice(0, idx);
            st += '/_end_';
            if (ext === '/public'){
              st += '/php'
            }
            else{
              st += ext;
            }

            bbn.fn.link(st)
          }
        }

      },
      toolbar: {
        template: `
          <div class="bbn-header bbn-flex-width bbn-left-padded bbn-right-padded bbn-bottom-padded bbn-top-sspadded">
            <div class="bbn-vmiddle"
                style="flex-wrap: wrap">
              <bbn-input :placeholder="_('Search the string')"
                        v-model="valueToFind"
                        :button-right="valueToFind.length ? 'nf nf-fa-close bbn-red' : 'nf nf-fa-search'"
                        @clickRightButton="valueToFind = valueToFind.length ? '' : valueToFind"
                        class="bbn-right-sspace bbn-top-sspace"/>
              <bbn-button :title="_('Force translation files update')"
                          class="bbn-bg-orange bbn-white bbn-right-sspace bbn-top-sspace"
                          @click="main.generate"
                          icon="nf nf-fa-files_o"
                          :text="_('Create translation files')"/>
              <bbn-button :title="_('Rebuild table data')"
                          @click="main.remake_cache"
                          icon="nf nf-fa-retweet"
                          :text="_('Rebuild table data')"
                          class="bbn-right-sspace bbn-top-sspace"/>
              <bbn-button :title="_('Check files for new strings')"
                          @click="main.find_strings"
                          icon="nf nf-fa-search"
                          :text="_('Parse files for new strings')"
                          v-if="!isOptions"
                          class="bbn-top-sspace"/>
            </div>
            <div class="bbn-xs bbn-vmiddle bbn-flex-fill bbn-left-space bbn-top-sspace"
                v-if="!isOptions"
                style="justify-content: flex-end">
              <span>
                <span v-text="_('If the column with')"/>
                <i class="nf nf-fa-asterisk"/>
                <span v-text="_('is empty be sure to force translation files update and then update the table')"/>
              </span>
            </div>
          </div>
        `,
        props: ['source'],
        data(){
          return {
            main: null,
            valueToFind: '',
            root: appui.plugins['appui-i18n'] + '/'
          };
        },
        computed: {
          languages(){
            return this.main ? this.main.source.res.languages : []
          },
          idProject(){
            return this.main ? this.main.source.id_project : null
          },
          isOptions(){
            return !!this.idProject && (this.idProject === 'options');
          }
        },
        methods: {
          search(){
            let table = this.closest('bbn-table');
            if (this.valueToFind.length) {
              table.currentFilters.conditions.splice(0);
              table.currentFilters.logic = 'OR'
              table.currentFilters.conditions.push({
                field: 'exp',
                operator: 'contains',
                value: this.valueToFind
              });
              bbn.fn.each(this.languages, (v, i) => {
                table.currentFilters.conditions.push({
                  field: v + '_db',
                  operator: 'contains',
                  value: this.valueToFind
                })
              })
            }
            else if (!this.valueToFind.length && table.currentFilters.conditions.length) {
              table.currentFilters.conditions.splice(0);
            }
          }
        },
        beforeMount(){
          this.$set(this, 'main', this.closest('appui-i18n-strings'));
        },
        watch: {
          valueToFind(val){
            this.search();
          }
        }
      }
    }
  }
})();
