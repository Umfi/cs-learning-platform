<template>
    <div class="d-inline-block">
        <button type="button" class="btn btn-secondary" :title="$t('Edit module specific task config')" @click="openTaskModuleSettingsModal(taskid)"><i class="fas fa-cog"></i></button>
        <div class="modal" :id="'taskModuleModal-' + taskid">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $t('Task Module Configuration') }} (<span v-text="subTitle"></span>)</h4>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $t('Cancle') }}</button>
                        <button type="button" class="btn btn-primary" @click="storeData(taskid)">{{ $t('Save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import Swal from 'sweetalert2/src/sweetalert2.js'

    export default {
        props: ["taskid", "taskmodule"],
        data() {
            return {
                subTitle: this.$props.taskmodule,
                moduleData: null
            }
        },
        mounted() {
            //console.log('Module:' + this.$props.taskmodule);
        },
        methods: {
            openTaskModuleSettingsModal(id) {
                $('#taskModuleModal-' + id).modal('show');
            },
            storeData(id) {
                // Call pre Store function of module
                this.$refs.activeModule._preStore();

                var self = this;
                var exportData = this.$refs.activeModule.$data;
                var tips = this.$refs.activeModule.$refs.tips.$data.tips;
                exportData.tips = tips;

                /**
                 * Remove all internal values starting with _
                 */
                this._removeInternalData(exportData);

                this.moduleData = exportData;

                axios.post('/teacher/setTaskModuleConfig/' + id, {
                    id: id,
                    module: this.$props.taskmodule,
                    data: JSON.stringify(this.moduleData)
                }).then(response => {

                    if (response.data.result) {
                        Swal.fire({
                            icon: 'success',
                            title: self.$t('Task module config has been successfully saved.'),
                        });

                        $('#taskModuleModal-' + id).modal('hide');
                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: self.$t('Oops...'),
                            text: response.data.message,
                        });

                    }
                }).catch(function (error) {
                    console.error(error);
                });

            },
            _removeInternalData(obj) {
                for(var prop in obj) {
                    if (prop.startsWith("_"))
                        delete obj[prop];
                    else if (typeof obj[prop] === 'object')
                        this._removeInternalData(obj[prop]);
                }
            }
        }
    }
</script>
