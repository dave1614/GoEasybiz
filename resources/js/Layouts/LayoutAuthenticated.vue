<script setup>
import { mdiForwardburger, mdiBackburger, mdiMenu } from "@mdi/js";
// import { useRouter } from "vue-router";
import { Inertia } from '@inertiajs/inertia'
import menuAside from "@/menuAside.js"; 
import menuAsideAdmin from "@/menuAsideAdmin.js";
import menuNavBar from "@/menuNavBar.js";
import { useMainStore } from "@/stores/main.js";
import { useLayoutStore } from "@/stores/layout.js";
import { useStyleStore } from "@/stores/style.js";
import { useForm, usePage, Head, Link } from '@inertiajs/inertia-vue3'
import BaseIcon from "@/components/BaseIcon.vue";
import FormControl from "@/components/FormControl.vue";
import NavBar from "@/components/NavBar.vue";
import NavBarItemPlain from "@/components/NavBarItemPlain.vue";
import AsideMenu from "@/components/AsideMenu.vue";
import FooterBar from "@/components/FooterBar.vue";
import { ref } from 'vue';

useMainStore().setUser({
  name: "John Doe",
  email: "john@example.com",
  avatar:
    "https://avatars.dicebear.com/api/avataaars/example.svg?options[top][]=shortHair&options[accessoriesChance]=93",
});

const layoutAsidePadding = "xl:pl-60";

const styleStore = useStyleStore();

const layoutStore = useLayoutStore();
const mainStore = useMainStore();

const page = usePage();
const props = page.props;
const auth_user = props.value?.auth.user;


const is_admin = ref(auth_user.is_admin == 1 ? true : false);

Inertia.on('navigate', () => {
  layoutStore.isAsideMobileExpanded = false
  layoutStore.isAsideLgActive = false
})

// const router = useRouter();

// router.beforeEach(() => {
//   layoutStore.isAsideMobileExpanded = false;
//   layoutStore.isAsideLgActive = false;
// });

const menuClick = (event, item) => {
  if (item.isToggleLightDark) {
    styleStore.setDarkMode();
  }

  if (item.isLogout) {
    Inertia.post(route('logout'))
  }
};
</script>

<template>
  <div
    :class="{
      dark: styleStore.darkMode,
      'overflow-hidden lg:overflow-visible': layoutStore.isAsideMobileExpanded,
    }"
  >
    <div
      :class="[
        layoutAsidePadding,
        { 'ml-60 lg:ml-0': layoutStore.isAsideMobileExpanded },
      ]"
      class="pt-14 min-h-screen w-screen transition-position lg:w-auto bg-gray-50 dark:bg-slate-800 dark:text-slate-100"
    >
      <NavBar
        :menu="menuNavBar"
        :class="[
          layoutAsidePadding,
          { 'ml-60 lg:ml-0': layoutStore.isAsideMobileExpanded },
        ]"
        @menu-click="menuClick"
      >
        <NavBarItemPlain
          display="flex lg:hidden"
          @click.prevent="layoutStore.asideMobileToggle()"
        >
          <BaseIcon
            :path="
              layoutStore.isAsideMobileExpanded
                ? mdiBackburger
                : mdiForwardburger
            "
            size="24"
          />
        </NavBarItemPlain>
        <NavBarItemPlain
          display="hidden lg:flex xl:hidden"
          @click.prevent="layoutStore.isAsideLgActive = true"
        >
          <BaseIcon :path="mdiMenu" size="24" />
        </NavBarItemPlain>
        
      </NavBar>
      <AsideMenu :menu="is_admin ? menuAsideAdmin : menuAside" @menu-click="menuClick" />
      <slot />
      <FooterBar>
        
      </FooterBar>
    </div>
  </div>
</template>
