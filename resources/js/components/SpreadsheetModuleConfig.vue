<template>
    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" v-bind:id="'specification-tab-' + taskid" data-toggle="tab" v-bind:href="'#specification-' + taskid" role="tab" aria-controls="specification" aria-selected="true">Specification</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" v-bind:id="'solution-tab-' + taskid" data-toggle="tab" v-bind:href="'#solution-' + taskid" role="tab" aria-controls="solution" aria-selected="false">Solution</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" v-bind:id="'tips-tab-' + taskid" data-toggle="tab" v-bind:href="'#tips-' + taskid" role="tab" aria-controls="tips" aria-selected="false">Tips</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" v-bind:id="'specification-' + taskid" role="tabpanel" aria-labelledby="specification-tab">
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
                        <hot-table ref="hotTableSpecificationComponent" :data="specificationData" :settings="$data._settings"></hot-table>
                    </div>

                    <div v-if="programming">
                        <prism-editor v-model="specificationCode" language="js" :lineNumbers="true"></prism-editor>
                    </div>

                </div>
            </div>
            <div class="tab-pane fade" v-bind:id="'solution-' + taskid" role="tabpanel" aria-labelledby="solution-tab">
                <div class="mt-1">

                    <div>
                        <button type="button" class="btn btn-sm btn-primary" @click="syncData()"><i class="fas fa-sync"></i> Copy values from specification</button>
                    </div>
                    <hr>
                    <div>
                        <hot-table ref="hotTableSolutionComponent" :data="solutionData" :settings="$data._settings"></hot-table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" v-bind:id="'tips-' + taskid" role="tabpanel" aria-labelledby="tips-tab">
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
                specificationData: Handsontable.helper.createEmptySpreadsheetData(5, 4),
                specificationCode: '',
                solutionData: Handsontable.helper.createEmptySpreadsheetData(5, 4),
                solutionCode: '',
                /*
                * INTERNAL IS GOING TO BE EXCLUDED FROM EXPORT
                * starts with _
                * */
                _hotInstanceSpecification: null,
                _hotInstanceSolution: null,
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
            this._hotInstanceSolution = this.$refs.hotTableSolutionComponent.hotInstance;

            var instSpecification = this._hotInstanceSpecification;
            var instSolution = this._hotInstanceSolution;

            $('#taskModuleModal-' + this.$props.taskid).on('shown.bs.modal', function () {
                setTimeout(function(){
                    instSpecification.render();
                    instSolution.render();
                }, 200);
            });

            $('#taskModuleModal-' + this.$props.taskid).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
                setTimeout(function(){
                    instSpecification.render();
                    instSolution.render();
                }, 200);
            })
        },
        watch: {
            row: function(newVal, oldVal) {
                this.specificationData = Handsontable.helper.createEmptySpreadsheetData(newVal, this.col);
                this.solutionData = Handsontable.helper.createEmptySpreadsheetData(newVal, this.col);
            },
            col: function(newVal, oldVal) {
                this.specificationData = Handsontable.helper.createEmptySpreadsheetData(this.row, newVal);
                this.solutionData = Handsontable.helper.createEmptySpreadsheetData(this.row, newVal);
            }
        },
        methods: {
            syncData() {
                this.solutionData = JSON.parse(JSON.stringify(this.specificationData));
            }
        }
    }
</script>

