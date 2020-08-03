<template>
    <div class="d-inline-block">
        <button type="button" class="btn btn-secondary" title="Edit module specific task config" @click="editTaskModuleSettings(taskid)"><i class="fas fa-cog"></i></button>
        <div class="modal" :id="'taskModuleModal-' + taskid">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Task Module Configuration (<span v-text="subTitle"></span>)</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                    </div>


                    <!-- Modal body -->
                    <div class="modal-body">

                        <!-- List all available module configs here-->
                        <spreadsheetmoduleconfig v-if="taskmodule === 'MODULE_SPREADSHEET'" :taskid="taskid"></spreadsheetmoduleconfig>

                    </div>


                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancle</button>
                        <button type="submit" class="btn btn-primary">Save</button>
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
                subTitle: this.$props.taskmodule
            }
        },
        mounted() {
            console.log('Module:' + this.$props.taskmodule);
        },
        methods: {
            editTaskModuleSettings(id) {
                $('#taskModuleModal-' + id).modal('show');
            },
        }
    }
</script>
