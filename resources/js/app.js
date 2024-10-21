import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { vueDebounce } from 'vue-debounce';

/* Shadcn UI */
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import { Button } from '@/Components/ui/button'
import { Checkbox } from '@/Components/ui/checkbox'
import { Switch } from '@/Components/ui/switch'
import { RadioGroup, RadioGroupItem  } from '@/Components/ui/radio-group'
import ComboBox from '@/Components/ComboBox.vue'
import Datepicker from '@/Components/Datepicker.vue'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${appName}`,
    resolve: name => name.startsWith('Components/') ? require(`./${name}.vue`) : require(`./Pages/${name}.vue`),
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .directive('debounce', vueDebounce({ lock: true }))
            .component('Label', Label)
            .component('Input', Input)
            .component('Textarea', Textarea)
            .component('Button', Button)
            .component('Checkbox', Checkbox)
            .component('Switch', Switch)
            .component('RadioGroup', RadioGroup)
            .component('RadioGroupItem', RadioGroupItem)
            .component('ComboBox', ComboBox)
            .component('Datepicker', Datepicker)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
