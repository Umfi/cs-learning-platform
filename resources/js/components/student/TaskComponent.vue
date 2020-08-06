<template>
    <div class="d-inline-block">
        <button type="button" class="btn btn-primary" title="Start task" @click="openTaskModal(taskid)"><i class="fas fa-play"></i></button>
        <div class="modal" :id="'taskModuleModal-' + taskid">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Task</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>


                    <!-- Modal body -->
                    <div class="modal-body">

                        <!--
                        TODO: Include stepper; step 1 intro, step 2 task description, step 3 task, step 4 extro
                        -->

                        <!-- List all available module configs here with same ref -->
                        <!-- <examplemoduleconfig ref="activeModule" v-if="taskmodule === 'MODULE_EXAMPLE'" :taskid="taskid"></examplemoduleconfig> -->


                    </div>


                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancle</button>
                        <button type="button" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ["taskid", "taskmodule"],
        data() {
            return {
                moduleData: null
            }
        },
        mounted() {
            console.log('Module:' + this.$props.taskmodule);
        },
        methods: {
            openTaskModal(id) {
                $('#taskModuleModal-' + id).modal('show');
            },
        }
    }
</script>
