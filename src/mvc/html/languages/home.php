<div class="appui-strings-table"
     style="min-height: 500px">
  <bbn-table id="table_paths"
             class="paths_table bbn-flex-fill"
             :source="source.projects"
             :data="{primary:source.primary}"
             :limit="25"
             ref="table1"
             :editable="true"
             :editor="$options.components['appui-languages-form']"
             :expander="$options.components['appui-paths-table']"
             :info="false"
             :toolbar="$options.components['toolbar']"
  >
    <bbn-column field="name"
                width="35%"
                class="bbn-xl bbn-b"
                title="<?=_('Projects')?>"
                :render="render_projects"
    ></bbn-column>

    <bbn-column title="<?=_('Language(s)')?>"
                field="langs"
                :render="render_langs"
                class="bbn-xl"
    ></bbn-column>

    <bbn-column title="<?=_('Source language')?>"
                field="lang"
                :render="render_lang_name"
                class="bbn-xl"
    ></bbn-column>

    <bbn-column :buttons="[{
                    command: cfg_languages,
                    icon: 'fa fa-flag',
                    title: 'Configure languages for this project'
                  }]"
                width="80"
    ></bbn-column>

  </bbn-table>
</div>