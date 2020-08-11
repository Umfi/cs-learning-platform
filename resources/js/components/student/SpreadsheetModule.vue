<template>
    <div>
        <div>
            <hot-table ref="hotTableSpecificationComponent" :data="specificationData" :settings="$data._settings"></hot-table>
        </div>

        <div v-if="programming">
            <prism-editor v-model="specificationCode" language="js" :lineNumbers="true"></prism-editor>
        </div>

    </div>
</template>

<script>

    import { HotTable } from '@handsontable/vue';
    import Handsontable from 'handsontable';
    import "prismjs";
    import "prismjs/themes/prism.css";
    import PrismEditor from 'vue-prism-editor'
    import "vue-prism-editor/dist/VuePrismEditor.css";
    import Stepper from "bs-stepper";

    export default {
        props: ["taskid", "taskdata"],
        data() {
            return {
                programming: false,
                dataVisualization: false,
                specificationData: Handsontable.helper.createEmptySpreadsheetData(5, 4),
                specificationCode: '',
                /*
                * INTERNAL IS GOING TO BE EXCLUDED FROM EXPORT
                * starts with _
                * */
                _hotInstanceSpecification: null,
                _settings: {
                    rowHeaders: true,
                    colHeaders: true,
                    formulas: true,
                    width: '100%',
                    height: 200,
                    licenseKey: process.env.MIX_HANDSONTABLE_KEY
                },
            };
        },
        components: {
            HotTable,
            PrismEditor
        },
        mounted() {
            console.log('TaskID:' + this.$props.taskid);

            this._hotInstanceSpecification = this.$refs.hotTableSpecificationComponent.hotInstance;

            var instSpecification = this._hotInstanceSpecification;

            var self = this;

            $('#taskModuleModal-' + this.$props.taskid).on('shown.bs.modal', function () {
                var data = self.$props.taskdata;
                self.programming = data.specification.programming;
                self.dataVisualization = data.specification.dataVisualization;
                self.specificationData = data.specification.data;
                self.specificationCode = data.specification.code;
            });

            var stepperEl = document.querySelector('#bs-stepper-' + self.$props.taskid);
            stepperEl.addEventListener('shown.bs-stepper', function (event) {
                setTimeout(function(){
                    instSpecification.render();
                }, 500);
            });


        },
        methods: {

        }
    }
</script>

