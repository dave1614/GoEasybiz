import '../css/main.css'

import { createPinia } from 'pinia'
import { useStyleStore } from '@/stores/style.js'

import { darkModeKey, styleKey } from '@/config.js'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m'
import swal from 'sweetalert2';
import 'vue-universal-modal/dist/index.css';

import VueUniversalModal from 'vue-universal-modal';



window.Swal = swal;

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Go-Easybiz'

const pinia = createPinia()

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(pinia)
            .use(VueUniversalModal, {
              teleportTarget: '#modals',
            })
            .use(ZiggyVue, Ziggy)
            .mount(el)
    }
})

InertiaProgress.init({ color: '#4B5563' })

const styleStore = useStyleStore(pinia)

/* App style */
styleStore.setStyle(localStorage[styleKey] ?? 'basic')

/* Dark mode */
if ((!localStorage[darkModeKey] && window.matchMedia('(prefers-color-scheme: dark)').matches) || localStorage[darkModeKey] === '1') {
  styleStore.setDarkMode(true)
}
