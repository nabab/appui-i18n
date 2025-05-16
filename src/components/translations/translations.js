(() => {
  return {
    props: ['source'],
    data(){
      return {
        columnLength: true,
        hiddenCols: [],
        showAlert: false,
        root: appui.plugins['appui-i18n'] + '/',
        mainPage: appui.getRegistered('appui-i18n')
      };
    },
    computed: {
      currentProject(){
        return bbn.fn.getRow(this.mainPage.source.projects, 'code', this.source.id_project);
      },
      currentLangs(){
        let langs = [];
        if (this.currentProject) {
          bbn.fn.each(this.currentProject.langs, l => {
            if (this.mainPage.source.primary) {
              let a = bbn.fn.getField(this.mainPage.source.primary, 'code', 'id', l);
              if (a) {
                langs.push(a);
              }
            }
          });
        }
        return langs;
      },
      isOptions(){
        return this.source?.id_project === 'options';
      },
      /** the source language of this id_option */
      source_lang(){
        return bbn.fn.getField(this.source.primary, 'text', 'code' , this.source.res.path_source_lang)
      },
      /**array of columns for the table*/
      columns(){
        let r = [];
        bbn.fn.each(this.currentLangs, l => {
          let text = bbn.fn.getField(this.source.primary, 'text', 'code', l);
          let obj = {
            field: l + '_db',
            label:  (l === this.source.res.path_source_lang) ?
              (`${text} <i class="nf nf-fa-asterisk" title="` + bbn._('This is the original language of the expression') + `"/>`) :
              text,
            editable: true,
            editor: 'appui-i18n-translations-editor'
          };
          obj.render = row => {
            let translation_db = row[l + '_db'];
            let translation_po = row[l + '_po'];
            if (translation_db === false) {
              return '';
            }
            if (!bbn.fn.isNull(translation_db)
              && translation_db?.length
              && (translation_db === translation_po)
            ) {
              return `<div class="bbn-vmiddle" style="justify-content: space-between">
                <span>${translation_db}</span>
                <i class="nf nf-fa-check bbn-s bbn-green" title="` + bbn._('Expression correctly inserted in db and po file') + `" style="float:right"/>
              </div>`;
            }
            else if (bbn.fn.isNull(translation_db)
              || (translation_db?.length
                && (translation_db !== translation_po))
            ) {
              return  `<div class="bbn-vmiddle" style="justify-content: space-between">
                <span title="` + (translation_po?.length ? bbn._('The translation in the po file is different from the one in database') : '') + `"
                      class="${translation_po?.length ? 'bbn-orange' : 'bbn-red'}">
                  ${translation_db}
                </span>
                <i style="float:right"
                   class="${translation_po?.length ? 'nf nf-fa-exclamation' : 'nf nf-fa-exclamation_triangle'} bbn-s ${translation_po?.length ? 'bbn-orange' : 'bbn-red'}"
                   title="` + (translation_po ? (bbn._('The translation in the po file is') + ': ' + translation_po) : bbn._('Translation missing in po file')) + `"/>
              </div>`;
            }
          }
          r.push(obj);
        });
        r.push({
          flabel: bbn._('Number of occurrences of the strings in the path files'),
          label: '#',
          field: 'occurrence',
          editable: false,
          render(row){
            return row.occurrence ? row.occurrence : 0;
          },
          width: 40,
          cls: 'bbn-c'
        }, {
          flabel: bbn._('Remove original expression'),
          buttons: this.buttons,
          width: this.isOptions ? 40 : 70,
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
              this.source.res.strings = d.strings;
              if (!!d.strings && d.strings.length) {
                appui.success(bbn._('Files of translation successfully updated for %s', d.languages.join(' ' + bbn._('and') + ' ')));
              }
              if (!!d.widget) {
                this.updateWidget(this.source.res.id_option, d.widget);
              }
              this.$nextTick(() => {
                this.showAlert = false;
              });
            }
            else {
              appui.error();
              this.showAlert = false;
            }
          });
        }
        else {
          this.alert(bbn._('You have to configure at least a language using the button %s of the widget in the dashboard', '<i class="nf nf-fa-flag"></i>'));
          this.showAlert = false;
        }
      },
      /** checks if there are new strings in the files of the path */
      findStrings(){
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
            icon: 'nf nf-md-sign_direction',
            title: bbn._('Show files containing the string'),
            notext: true
          });
        }
        return res;
      },
      //opens the popup containing the link(s) to the file(s) containing the string
      showPath(row){
        this.getPopup({
          label: bbn._('File(s) containing the string'),
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
        if (row) {
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
                this.updateWidget(this.source.res.id_option, d.widget);
              }
            }
          });
        }
      },
      /** remakes the model of table in cache */
      remakeCache(){
        this.columnLength = false;
        this.showAlert = true;
        this.post(this.root + 'actions/reload_table_cache', {
          id_option: this.source.res.id_option,
          id_project: this.source.id_project
        }, d => {
          if (d.success) {
            if (!!d.data) {
              let diff = d.data.total - this.source.res.total;
              this.source.res.languages = d.data.languages;
              this.source.res.strings = d.data.strings;
              this.source.res.total = d.data.total;
              if (diff > 0) {
                appui.warning(bbn._('%d new string(s) found in %s', diff, this.source.res.path));
              }
              else if (diff < 0) {
                appui.warning(bbn._('%d string(s) deleted from %s files', Math.abs(diff), this.source.res.path));
              }
              else if (diff = 0) {
                appui.warning(bbn._('There are no changes in data'));
              }
              this.$nextTick(() => {
                this.showAlert = false;
                this.columnLength = true;
              });
            }
            else {
              appui.error();
            }
            if (!!d.widget) {
              this.updateWidget(this.source.res.id_option, d.widget);
            }
          }
          else {
            appui.error();
          }
        });
      },
      updateWidget(idWidget, data){
        let dashboardPage = appui.getRegistered('appui-i18n-dashboard', true);
        if (!!this.currentProject
          && dashboardPage
          && !!dashboardPage.idProject
          && (dashboardPage.idProject === this.currentProject.id)
        ) {
          let dashboard = dashboardPage.getRef('dashboard');
          if (dashboard) {
            let widget = dashboard.getWidget(idWidget);
            if (widget) {
              this.$set(widget.source, 'data_widget', data);
            }
          }
        }
      }
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
      hiddenCols(val){
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
            idx = this.hiddenCols.indexOf(v);
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
          <div class="bbn-vpadding">
            <span class="bbn-spadding bbn-w-100"
                  bbn-for="p in source.paths">
              <a bbn-text="p"
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
          <div class="bbn-header bbn-flex-width bbn-left-padding bbn-right-padding bbn-bottom-padding bbn-top-sspadding">
            <div class="bbn-vmiddle bbn-flex-wrap">
              <bbn-input placeholder="` + bbn._('Search the string') + `"
                        bbn-model="valueToFind"
                        :button-right="valueToFind.length ? 'nf nf-fa-close bbn-red' : 'nf nf-fa-search'"
                        @clickrightbutton="valueToFind = valueToFind?.length ? '' : valueToFind"
                        class="bbn-right-sspace bbn-top-sspace"/>
              <bbn-button title="` + bbn._('Force translation files update') + `"
                          class="bbn-bg-orange bbn-white bbn-right-sspace bbn-top-sspace"
                          @click="generate"
                          icon="nf nf-md-file_replace_outline"
                          label="` + bbn._('Create translation files') + `"/>
              <bbn-button title="` + bbn._('Rebuild table data') + `"
                          @click="remakeCache"
                          icon="nf nf-fa-retweet"
                          label="` + bbn._('Rebuild table data') + `"
                          class="bbn-right-sspace bbn-top-sspace"/>
              <bbn-button bbn-if="!isOptions"
                          title="` + bbn._('Check files for new strings') + `"
                          @click="findStrings"
                          icon="nf nf-fa-search"
                          label="` + bbn._('Parse files for new strings') + `"
                          class="bbn-top-sspace"/>
            </div>
            <div class="bbn-xs bbn-vmiddle bbn-flex-fill bbn-left-space bbn-top-sspace"
                bbn-if="!isOptions"
                style="justify-content: flex-end">
              <span>
                <span>` + bbn._('If the column with') + `</span>
                <i class="nf nf-fa-asterisk"/>
                <span>` + bbn._('is empty be sure to force translation files update and then update the table') + `</span>
              </span>
            </div>
          </div>
        `,
        props: ['source'],
        data(){
          return {
            main: this.closest('appui-i18n-translations'),
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
          },
          generate(){
            this.main.generate();
          },
          remakeCache(){
            this.main.remakeCache();
          },
          findStrings(){
            this.main.findStrings();
          }
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
