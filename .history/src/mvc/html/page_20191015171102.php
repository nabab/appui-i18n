<div class="languages bbn-overlay bbn-flex-height">
  <div class="bbn-w-100 bbn-header bbn-middle" style="height:60px; justify-content:space-around">
    <!--bbn-dropdown :url="source.root + 'page/dashboard'"
                  :source="dd_projects"
                  :title="_('Select a project')"
                  v-model="id_project"
                  @change="load_widgets"
                  class="bbn-s"
    ></bbn-dropdown-->
    <bbn-button icon="nf nf-fa-cogs"
                text="<?=_("Config project languges")?>"
                @click="cfg_project_languages"
                title="<?=_("Configure languages for this project")?>"
                v-if="id_project !== 'options'"
                class="bbn-s"
    ></bbn-button>

    <bbn-button icon="nf nf-fa-user"
                text="<?=_("User Activity")?>"
                @click="open_user_activity"
                title="<?=_("User activity")?>"
                class="bbn-s"
    ></bbn-button>

    <bbn-button icon="nf nf-fa-users"
                text="<?=_("Users Activity")?>"
                @click="open_users_activity"
                title="<?=_("Users activity")?>"
                class="bbn-s"
    ></bbn-button>

    <bbn-button icon="nf nf-fa-flag"
                text="<?=_("Glossary table")?>"
                @click="open_glossary_table"
                title="<?=_("Glossary tables")?>"
                class="bbn-s"
    ></bbn-button>
    <div v-if="id_project !== 'options'" class="bbn-h-100">
      <div class="bbn-vmiddle bbn-w-100 bbn-h-50"><img :title="_('Original language of this project')" :src="src" style="width:20px" class="bbn-hspadded"></div>
      
      <div class="bbn-vmiddle bbn-w-100 bbn-h-50" :title="_('Languages configured for translation of this project. If you want to delete or add a language check/uncheck it in the form \'Config project languages\'')">
        <img v-for="c in source.configured_langs"
             style="width:20px" 
             class="bbn-hspadded"
             :src="makeSrc(get_field(source.primary, 'id', c, 'code'))"
        >
      </div>
    </div>
    <div v-else
         class="second"
    >
      <a href="options/tree"
         title="<?=_("Go to options' tree, choice the option you want to translate")?>"
      ><?=_("Follow the link to configure other options for translation")?></a>
    </div>
  </div>
  <div class="bbn-flex-fill">
    <bbn-tabnav :autoload="true">
      <bbns-container  v-for="(p, i) in source.projects"
                      icon="nf nf-fa-edit"
                      :url="'dashboard/' + p.id"
                      component="appui-i18n-dashboard"
                      :title="p.name"
                      :load="true"
                      :source="source.widgets"
                      :static="false"
                      :pinned="true"
                      :key="i"
      ></bbns-container>
    </bbn-tabnav>
  </div>

</div>
<script v-for="temp in source.templates" :id="temp.id" type="text/x-template" v-html="temp.html"></script>