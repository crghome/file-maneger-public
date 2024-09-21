// import router from "./router.js";
import { createApp, ref } from 'vue';
import Loader from './Components/Loader.vue';
import Modal from './Components/Modal.vue';
import AlertBlock from './Components/AlertBlock.vue';
import HeadContent from './Components/HeadContent.vue';
import FileLoader from './Components/FileLoader.vue';
import FilePreview from './Components/FilePreview.vue';
import AuthPage from './Components/Auth.vue';

const app = createApp({});

const clickOutsideDirective = {
    // добавляем эвент для тыка вне компонента
    beforeMount(el, binding) {
        el.clickOutsideEvent = function (event) {
            if (!(el == event.target || el.contains(event.target))) {
                binding.value(event, el);
            }
        };
        document.body.addEventListener('click', el.clickOutsideEvent)
    },
    unmounted(el) {
        // нужно удалять эвенты, берегите ОЗУ клиентов
        document.body.removeEventListener('click', el.clickOutsideEvent);
    }
};
app.directive('click-outside', clickOutsideDirective);
// use v-click-outside="yourMethod"

app.component('vue-loader', Loader);
app.component('vue-modal', Modal);
app.component('vue-alert-block', AlertBlock);
app.component('vue-head-content', HeadContent);
app.component('vue-file-loader', FileLoader);
app.component('vue-file-preview', FilePreview);
app.component('vue-auth', AuthPage);


// app.use(router).mount('#app-vue-main');
app.mount('#app-vue-main');

