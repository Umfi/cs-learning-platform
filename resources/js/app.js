/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// Initialize i18n support for vue
import languageBundle from '@kirschbaum-development/laravel-translations-loader/json!@kirschbaum-development/laravel-translations-loader';
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

const i18n = new VueI18n({
    locale: window.locale,
    fallbackLocale: 'en',
    messages: languageBundle,
})


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
Vue.component('rating', require('./components/teacher/frontend/RatingComponent.vue').default);
Vue.component('rating-chart', require('./components/teacher/frontend/RatingChart.vue').default);
Vue.component('taskmoduleconfig', require('./components/teacher/backend/TaskModuleConfigComponent.vue').default);
Vue.component('tip-list', require('./components/teacher/backend/TipComponent.vue').default);
Vue.component('learning-path', require('./components/teacher/backend/LearningPathComponent.vue').default);
Vue.component('spreadsheetmoduleconfig', require('./components/teacher/backend/modules/SpreadsheetModuleConfig.vue').default);
Vue.component('task', require('./components/student/TaskComponent.vue').default);
Vue.component('task-solution', require('./components/student/TaskSolutionViewerComponent.vue').default);
Vue.component('spreadsheet-module', require('./components/student/modules/SpreadsheetModule.vue').default);
Vue.component('spreadsheet-code-info', require('./components/shared/SpreadsheetCodeInfo.vue').default);
Vue.component('spreadsheet-formula-info', require('./components/shared/SpreadsheetFormulaInfo.vue').default);
Vue.component('spreadsheet-datavisualization', require('./components/shared/SpreadsheetDataVisualization.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    i18n: i18n
});
