<template>
    <div class="d-inline-block">
        <button type="button" class="btn btn-secondary" :title="$t('Show my solution')" @click="openTaskSolutionModal(taskid)"><i class="fas fa-user-graduate"></i></button>
        <div class="modal" :id="'taskSolutionModuleModal-' + taskid">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $t('Task') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>


                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="container">
                            <div class="align-items-center row">
                                <div class="col-12">
                                    <div role="alert" class="alert alert-info">
                                        <h4 class="alert-heading">{{ $t('Task Description') }}</h4>
                                        <p v-text="task.description"></p>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- List all available modules here with same ref -->
                            <!-- <examplemodule v-if="taskmodule === 'MODULE_EXAMPLE' && loaded" :taskid="taskid" :taskdata="task"></examplemodule> -->
                            <spreadsheet-module v-if="taskmodule === 'MODULE_SPREADSHEET' && loaded" :taskid="taskid" :taskdata="task" :type="'solution'"></spreadsheet-module>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import SpreadsheetModule from "./modules/SpreadsheetModule";


    export default {
        components: {SpreadsheetModule},
        props: ["taskid", "taskmodule"],
        data() {
            return {
                task: {
                    description: "",
                    tips: [],
                    intro_filetype: "",
                    intro: "",
                    extro_filetype: "",
                    extro: "",
                    solution: null
                },
                loaded: false,
            }
        },
        async mounted() {
            var self = this;

            // Get task data
            axios.get("/student/getTaskWithUserSolution/" + self.$props.taskid)
                .then(response => {
                    if (response.data.task) {
                        self.task = response.data.task;
                        self.loaded = true;
                    }
                }).catch(function (error) {
                console.error(error);
            });

        },
        methods: {
            openTaskSolutionModal(id) {
                $('#taskSolutionModuleModal-' + id).modal('show');
            }
        }
    }
</script>
