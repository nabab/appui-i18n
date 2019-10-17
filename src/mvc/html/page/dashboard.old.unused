<div class="bbn-overlay dashboard-splitter-container bbn-flex-height">
  <div class="bbn-w-100 bbn-header bbn-middle" style="height:60px; justify-content:space-around">
    <bbn-dropdown :url="source.root + 'page/dashboard'"
                  :source="dd_projects"
                  :title="_('Select a project')"
                  v-model="id_project"
                  @change="load_widgets"
                  class="bbn-s"
    ></bbn-dropdown>
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
      <!--<span v-text="get_field(source.primary, 'code', get_field(source.projects, 'id', id_project, 'lang'), 'text')"></span>-->
    </div>
    <div v-else
         class="second"
    >
      <a href="options/tree"
         title="<?=_("Go to options' tree, choice the option you want to translate")?>"
      ><?=_("Follow the link to configure other options for translation")?></a>
    </div>

      <!-- <div class="first">
        <span>Select a project</span>
        <bbn-dropdown :url="source.root + 'page/dashboard'"
                      :source="dd_projects"
                      v-model="id_project"
                      @change="load_widgets"
        ></bbn-dropdown>
      </div>
      <div class="second" v-if="id_project !== 'options'">
        <span><?=_("The source language for this project is")?>:</span>
        <span v-text="get_field(source.primary, 'code', get_field(source.projects, 'id', id_project, 'lang'), 'text')"></span>
      </div>
      <div v-else
            class="bbn-large second"
      >
        <a href="options/tree"
            title="<?=_("Go to options' tree, choice the option you want to translate")?>"
        ><?=_("Follow the link to configure other options for translation")?></a>
      </div>

      <div class="third" v-show="source.configured_langs">
        <span v-if="id_project !== 'options'"><?=_("Languages configured for translation of this project")?>:</span>
        <span v-else><?=_("Languages configured for options translation")?>:</span>
        <div class="langs ">
          <div v-for="c in source.configured_langs"
                class="bbn-b bbn-i"
                v-text="get_field(source.primary, 'id', c, 'text')"
          ></div>
        </div>

      </div>-->

      





  </div>
  <!--div class="bbn-flex-fill">
    
    <bbn-tabnav >
      <bbn-container :url="id_project"
                     component="appui-i18n-dashboard" 
                     :source="{widgets: widgets}"
                     
      ></bbn-container>
    </bbn-tabnav>
  </div-->
  
 

</div>