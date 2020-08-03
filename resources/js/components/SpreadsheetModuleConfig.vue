<template>
    <div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="specification-tab" data-toggle="tab" href="#specification" role="tab" aria-controls="specification" aria-selected="true">Specification</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="solution-tab" data-toggle="tab" href="#solution" role="tab" aria-controls="solution" aria-selected="false">Solution</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tips-tab" data-toggle="tab" href="#tips" role="tab" aria-controls="tips" aria-selected="false">Tips</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="specification" role="tabpanel" aria-labelledby="specification-tab">
                <div class="mt-1">

                    <div>
                        <div class="form-group">
                            <label for="col">Columns</label>
                            <input v-model="col" type="number" min="1" max="20" class="form-control" id="col">
                        </div>
                        <div class="form-group">
                            <label for="row">Rows</label>
                            <input v-model="row" type="number" min="1" max="50" class="form-control" id="row">
                        </div>
                        <div class="form-check">
                            <input v-model="programming" type="checkbox" class="form-check-input" id="programming">
                            <label class="form-check-label" for="programming">Enable Programming View</label>
                        </div>
                        <div class="form-check">
                            <input v-model="dataVisualization" type="checkbox" class="form-check-input" id="datavis">
                            <label class="form-check-label" for="datavis">Enable Data Visualization View</label>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <hot-table ref="hotTableComponent" :settings="settings"></hot-table>
                    </div>

                    <div v-if="programming">
                        <prism-editor v-model="code" language="js" :lineNumbers="true"></prism-editor>
                    </div>

                </div>
            </div>
            <div class="tab-pane fade" id="solution" role="tabpanel" aria-labelledby="solution-tab">
                <div class="mt-1">

                </div>
            </div>
            <div class="tab-pane fade" id="tips" role="tabpanel" aria-labelledby="tips-tab">
                <div class="mt-1">
                    <tip-list :tips="tips"></tip-list>
                </div>
            </div>
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

    export default {
        props: ["taskid"],
        data() {
            return {
                tips: [],
                row: 5,
                col: 4,
                programming: false,
                dataVisualization: false,
                hotInstance: null,
                settings: {
                    data: Handsontable.helper.createEmptySpreadsheetData(5, 4),
                    rowHeaders: true,
                    colHeaders: true,
                    formulas: true,
                    width: '100%',
                    height: 200,
                    licenseKey: process.env.MIX_HANDSONTABLE_KEY
                },
                code: ''
            };
        },
        components: {
            HotTable,
            PrismEditor
        },
        mounted() {
            console.log('TaskID:' + this.$props.taskid);

            this.hotInstance = this.$refs.hotTableComponent.hotInstance;
            var inst = this.hotInstance;

            $('#taskModuleModal-' + this.$props.taskid).on('shown.bs.modal', function () {
                setTimeout(function(){
                    inst.render();
                }, 200);
            })
        },
        watch: {
            row: function(newVal, oldVal) {
                this.settings.data = Handsontable.helper.createEmptySpreadsheetData(newVal, this.col);
            },
            col: function(newVal, oldVal) {
                this.settings.data = Handsontable.helper.createEmptySpreadsheetData(this.row, newVal);
            }
        },
    }
</script>

