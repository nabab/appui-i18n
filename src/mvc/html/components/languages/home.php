<div class="appui-languages-home bbn-full-screen">
  <bbn-splitter orientation="vertical" class="bbn-full-height">
    <div style="height:15%" class="bbn-padded bbn-c  home_buttons">
      <div class="bbn-grid-full" style="width:100%">
        <bbn-button text="Home"
                    icon="fa fa-home"
                    :notext="true"
                    class="bbn-b bbn-xxl bbn-c"
                    @click="home_switch = 1"
        ></bbn-button>

        <bbn-button text="Languages"
                    :notext="true"
                    title="<?=_('Open glossaries tab')?>"
                    icon="fa fa-flag"
                    class="bbn-b bbn-xxl bbn-c"
                    @click="home_switch = 2"
         ></bbn-button>

        <bbn-button text="History"
                    :notext="true"
                    icon="fa fa-history"
                    class="bbn-b bbn-xxl bbn-c"
                    title="<?=_('Watch last translations')?>"
                    @click="home_switch = 3"
        ></bbn-button>

        <bbn-button text="Advanced Search"
                    :notext="true"
                    icon="fa fa-search"
                    class="bbn-b bbn-xxl bbn-c"
                    title="<?=_('Advanced search of strings')?>"
                    @click="search = !search"
        ></bbn-button>
      </div>

      <div class="bbn-grid-full bbn-padded bbn-c"
           style="width:100%"
           v-if="search"
      >
        <bbn-input class="bbn-w-100 bbn-xl bbn-c"
                   placeholder="Insert here the string you want to search"
        ></bbn-input>
        <bbn-button text="Advanced Search"
                    style="width:80%">
        </bbn-button>
      </div>
    </div>

    <div style="height:70%"
         class=" bbn-full-screen"
    >
      <div class="bbn-flex-width">
        <bbn-table v-if="home_switch === 1"
                   id="table_paths"
                   class="paths_table bbn-flex-fill"
                   :source="source.projects"
                   :data="{primary:source.primary}"
                   :pageable="true"
                   :info="true"
                   :limit="25"
                   :sortable="true"
                   :filterable="true"
                   :multifilter="true"
                   :groupable="true"
                   :server-grouping="false"
                   ref="table1"
                   :editable="true"
                   :editor="$options.components['appui-languages-form']"
                   :expander="$options.components['appui-roots-table']"
        >
          <bbn-column field="name"
                      width="30%"
                      class="bbn-xl bbn-b"
                      title="<?=_('Projects')?>"
                      ftitle="<?=_('Projects')?>"
                      :render="render_projects"
          ></bbn-column>



          <bbn-column title="Language(s)"
                      field="langs"
                      :render="render_langs"
                      class="bbn-xl"
					></bbn-column>


          <bbn-column field="langs"
											title="<?=_('Actions')?>"
                      width="15%"
                      class="bbn-c"
                      :buttons="[{
                        text: 'Cfg Languages',
                        width: '100%',
                        command: cfg_root ,
                        icon: 'fa fa-cog',
                        title:'<?=_('Config languages for the project')?>'
                      },{
                        text: 'Update',
                        width: '100%',
                        command: run_script,
                        icon: 'fa fa-superpowers',
                        title: '<?=_('Check  in this project for new strings')?>'
                      }]"
          ></bbn-column>
        </bbn-table>
      </div>

      <!--temporary I take all the language in the list but I'll take only languages configured for some root-->
      <div class="bbn-flex-width">
        <bbn-table :source="example_data"
                   id="table_languages"
                   v-if="home_switch === 2"
                   class="home_table bbn-flex-fill"
                   :pageable="true"
                   :info="true"
                   :limit="25"
                   :sortable="true"
                   :filterable="true"
                   :multifilter="true"
        >
          <bbn-column field="lang"
                      title="Languages"
                      :render="render_language_name"
                      width="33%"
          >
          </bbn-column>

          <!--when I try to put a title with the tag for translation the table disappear without errors-->
          <bbn-column field="percentage"
                      title="Percentage of translated strings"
                      component="bbn-progressbar"
                      width="34%"
          >
          </bbn-column>

          <bbn-column field="strings"
                      title="Number of strings without translation"
                      :render="render_untr_strings"
                      width="33%"
          >
          </bbn-column>


        </bbn-table>
      </div>

      <div class="bbn-flex-width">
        <bbn-table v-if="home_switch === 3"
                   id="table_history"
                   class="root_table"
                   :source="source.primary"
        >
          <bbn-column field="code"
                      width="100%"
          ></bbn-column>
        </bbn-table>
      </div>



    </div>
    <div style="height:15%"
         class="bbn-c bbn-l bbn-middle"
    >here i'll show results of the script</div>
  </bbn-splitter>

</div>