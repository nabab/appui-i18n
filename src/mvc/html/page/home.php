<div class="strings-table"
     style="min-height: 500px">
  <bbn-table id="table_paths"
             class="paths_table bbn-flex-fill"
             :source="source.projects"
             :data="{primary: source.primary}"
             :limit="25"
             ref="table1"
             :editable="true"
             :editor="$options.components['languages-form']"
             :expander="$options.components['paths-table']"
             :info="false"

  >
    <bbns-column field="name"
                width="35%"
                class="bbn-xl bbn-b"
                label="<?= _('Projects') ?>"
                :render="render_projects"
    ></bbns-column>

    <bbns-column label="<?= _('Language(s)') ?>"
                field="langs"
                :render="render_langs"
                class="bbn-xl"
    ></bbns-column>

    <bbns-column label="<?= _('Source language') ?>"
                field="lang"
                :render="render_lang_name"
                class="bbn-xl"
    ></bbns-column>

    <bbns-column :buttons="[{
                    action: cfg_languages,
                    icon: 'nf nf-fa-flag',
                    label: 'Configure languages of translation for this project'
                  }]"
                width="80"
                cls="bbn-c"
    ></bbns-column>

  </bbn-table>
</div>