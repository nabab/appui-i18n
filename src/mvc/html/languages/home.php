<div class="appui-languages-home bbn-full-screen">
  <bbn-splitter orientation="vertical"
                :collapsible="true"
              	:resizable="true"
              	:scrollable="true"
                >
    <bbn-pane size="5%"
              :resizable="true"
              class="bbn-padded"
    >
      <div class="bbn-full-screen bbn-padded">
        <div v-text="page_title" class="bbn-c bbn-middle bbn-xxl bbn-grey"></div>
      </div>
    </bbn-pane>
    <bbn-pane size="20%" class="bbn-padded bbn-c home_buttons"
              :collapsible="true"
              :resizable="true"
              :scrollable="true"
              :checker="true"
   	>
      <div class="bbn-full-screen bbn-middle">
        <bbn-button text="Home"
                    icon="fa fa-home"
                    :notext="true"
                    class="bbn-b bbn-xxl bbn-c"
                    @click="home_switch = 1"
                    tabindex="0"
        ></bbn-button>

        <bbn-button text="Languages"
                    :notext="true"
                    title="<?=_('Open glossaries tab')?>"
                    icon="fa fa-flag"
                    class="bbn-b bbn-xxl bbn-c"
                    @click="home_switch = 2"
                    tabindex="0"
        ></bbn-button>

        <bbn-button text="Move to the table of your translations"
                    :notext="true"
                    icon="fa fa-user"
                    class="bbn-b bbn-xxl bbn-c"
                    @click="home_switch = 3"
                    tabindex="0"
        ></bbn-button>

        <bbn-button text="Move to the complete table of translations of all users"
                    v-if="source.is_admin"
                    :notext="true"
                    icon="fa fa-history"
                    class="bbn-b bbn-xxl bbn-c"
                    title="<?=_('Watch last translations')?>"
                    @click="home_switch = 4"
                    tabindex="0"
        ></bbn-button>


        <bbn-button text="Advanced Search"
                    v-if="( home_switch === 3 ) || ( home_switch === 4 )"
                    :notext="true"
                    icon="fa fa-search"
                    class="bbn-b bbn-xxl bbn-c"
                    title="<?=_('Advanced search of strings')?>"
                    @click="search = !search"
                    tabindex="0"
        ></bbn-button>

        <div v-if="search">
          <bbn-autocomplete class="bbn-w-90"
                            placeholder="Search values in this table"
                            v-model="search_value"
                            :source="autocomplete_source"
                            @change="alert"
                            tabindex="0"
          ></bbn-autocomplete>
        </div>
      </div>
    </bbn-pane>

    <bbn-pane size="75%"
              :collapsible="true"
              :resizable="true"
              :scrollable="true"
              
		>
      <div class="bbn-flex-width">
        <bbn-table v-if="home_switch === 1"
                   id="table_paths"
                   class="paths_table bbn-flex-fill"
                   :source="source.projects"
                   :data="{primary:source.primary}"
                   :limit="25"
                   ref="table1"
                   :editable="true"
                   :editor="$options.components['appui-languages-form']"
                   :expander="$options.components['appui-roots-table']"
                   :info="false"
        >
          <bbn-column field="name"
                      width="35%"
                      class="bbn-xl bbn-b"
                      title="<?=_('Projects')?>"
                      ftitle="<?=_('Projects')?>"
                      :render="render_projects"
          ></bbn-column>

          <bbn-column title="Language(s)"
                      field="langs"
                      :render="render_langs"
                      class="bbn-xl"
                      width="60%"
          ></bbn-column>

          <bbn-column :buttons="[{
                  command: cfg_root,
                  icon: 'fa fa-flag',
                  title: 'Configure languages for this project'

                }]"

          ></bbn-column>

        </bbn-table>
      </div>

      <!--temporary I take all the language in the list but I'll take only languages configured for some root-->
      <div class="bbn-flex-width">
        <div class="bbn-full-screen" v-if="home_switch === 2">
          <div class="bbn-h-100 bbn-middle bbn-xxxl bbn-c">
            <bbn-loader loadingText="ComingSoon"></bbn-loader></div>
        </div>
      </div>

      <!--this is the case in which the property home_switch = 3, history is a property which depends from
      home_switch-->
      <div class="bbn-flex-width">

        <bbn-table v-if="history"
                   :source="user_history"
                   :sortable="true"
                   :pageable="true"
                   :filterable="true"
                   :multifilter="true"
                   :limit="25"
                   editable="inline"
        >

          <bbn-column field="expression"
                      title="Expression"
          ></bbn-column>


          <bbn-column field="lang"
                      title="Language"
          ></bbn-column>


          <bbn-column field="last_modified"
                      title="Date"
                      type="date"
          ></bbn-column>

        </bbn-table>
      </div>

      <div class="bbn-flex-width">

        <bbn-table v-if="complete_history_access && source.is_admin"
                   :source="complete_history"
                   :sortable="true"
                   :pageable="true"
                   :filterable="true"
                   :multifilter="true"
                   :limit="25"
                   editable="inline"
        >
          <bbn-column field="name"
                      title="User"
          ></bbn-column>


          <bbn-column field="expression"
                      title="Expression"
          ></bbn-column>


          <bbn-column field="lang"
                      title="Language"
          ></bbn-column>


          <bbn-column field="last_modified"
                      title="Date"
                      type="date"
          ></bbn-column>

        </bbn-table>
      </div>

    </bbn-pane>

  </bbn-splitter>

</div>