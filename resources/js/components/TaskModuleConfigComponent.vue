<template>
    <div class="d-inline-block">
        <button type="button" class="btn btn-secondary" title="Edit module specific task config" @click="openTaskModuleSettingsModal(taskid)"><i class="fas fa-cog"></i></button>
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

                        <!-- List all available module configs here with same ref -->
                        <!-- <examplemoduleconfig ref="activeModule" v-if="taskmodule === 'MODULE_EXAMPLE'" :taskid="taskid"></examplemoduleconfig> -->
                        <spreadsheetmoduleconfig ref="activeModule" v-if="taskmodule === 'MODULE_SPREADSHEET'" :taskid="taskid"></spreadsheetmoduleconfig>


                    </div>


                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancle</button>
                        <button type="button" class="btn btn-primary" @click="storeData(taskid)">Save</button>
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
                subTitle: this.$props.taskmodule,
                moduleData: null
            }
        },
        mounted() {
            console.log('Module:' + this.$props.taskmodule);
        },
        methods: {
            openTaskModuleSettingsModal(id) {
                $('#taskModuleModal-' + id).modal('show');
            },
            storeData(id) {
                this.moduleData = this.$refs.activeModule.$data;


                    axios.post('/teacher/topic/' + id + '/taskmoduleconfig', {
                        id: id,
                        data: this.moduleData //todo only send important data
                    }).then(response => {
                        //response.data
                        $('#taskModuleModal-' + id).modal('hide');
                    }).catch(function (error) {
                        console.error(error);
                    });

            },
        }
    }
</script>
