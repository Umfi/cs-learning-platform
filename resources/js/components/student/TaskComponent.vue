<template>
    <div class="d-inline-block">
        <button type="button" class="btn btn-primary" :title="$t('Start task')" @click="openTaskModal(taskid)"><i class="fas fa-play"></i></button>
        <div class="modal" :id="'taskModuleModal-' + taskid">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $t('Task') }}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>


                    <!-- Modal body -->
                    <div class="modal-body">
                        <div :id="'bs-stepper-' + taskid" class="bs-stepper">
                            <div class="bs-stepper-header" role="tablist">
                                <!-- your steps here -->
                                <div class="step" :data-target="'#intro-part-' + taskid">
                                    <button type="button" class="step-trigger" role="tab" :aria-controls="'intro-part-' + taskid" :id="'intro-part-trigger-' + taskid">
                                        <span class="bs-stepper-circle"><i class="fas fa-film"></i></span>
                                        <span class="bs-stepper-label">{{ $t('Intro') }}</span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" :data-target="'#task-part-' + taskid">
                                    <button type="button" class="step-trigger" role="tab" :aria-controls="'task-part-' + taskid" :id="'task-part-trigger-' + taskid">
                                        <span class="bs-stepper-circle"><i class="fas fa-tasks"></i></span>
                                        <span class="bs-stepper-label">{{ $t('Task') }}</span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" :data-target="'#extro-part-' + taskid">
                                    <button type="button" class="step-trigger" role="tab" :aria-controls="'extro-part-' + taskid" :id="'extro-part-trigger-' + taskid">
                                        <span class="bs-stepper-circle"><i class="fas fa-film"></i></span>
                                        <span class="bs-stepper-label">{{ $t('Extro') }}</span>
                                    </button>
                                </div>
                            </div>
                            <div class="bs-stepper-content">
                                <!-- your steps content here -->
                                <div :id="'intro-part-' + taskid" class="content" role="tabpanel" :aria-labelledby="'intro-part-trigger-' + taskid">
                                    <div v-if="task.introFileType === 'IMAGE'" class="embed-responsive">
                                        <img :src="task.intro" class="img-fluid"/>
                                    </div>
                                    <div v-if="task.introFileType === 'VIDEO'" class="embed-responsive embed-responsive-16by9">
                                        <video controls disablePictureInPicture controlsList="nofullscreen nodownload noremoteplayback">
                                            <source :src="task.intro">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                    <div v-if="task.introFileType === 'TEXT'">
                                        <div class="alert alert-info" role="alert">
                                            <p v-text="task.intro"></p>
                                        </div>
                                    </div>
                                </div>
                                <div :id="'task-part-' + taskid" class="content" role="tabpanel" :aria-labelledby="'task-part-trigger-' + taskid">
                                    <div class="container">
                                        <div class="align-items-center row">
                                            <div class="col-10">
                                                <div role="alert" class="alert alert-info">
                                                    <h4 class="alert-heading">{{ $t('Task Description') }}</h4>
                                                    <p v-text="task.description"></p>
                                                </div>
                                            </div>
                                            <div class="col text-center">
                                                <button type="button" class="btn btn-lg btn-warning mt-md-n3"
                                                        :disabled="task.tips.length == 0"
                                                        :title="$t('Show tip')"
                                                        v-on:click="showHint">
                                                    <i class="fa-smile-wink fa-2x far"></i>
                                                    <br>
                                                    {{ $t('Tips') }}
                                                    <small v-text="(usedTips+1) + '/' + task.tips.length"></small>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="align-items-center row" v-if="usedTips >= 0 && tipsVisible">
                                            <div class="col">
                                                <div role="alert" class="alert alert-warning alert-dismissible">
                                                    <h4 class="alert-heading">{{ $t('Tips') }}</h4>
                                                    <button type="button" class="close" aria-label="Close" v-on:click="tipsVisible = false">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    <ul class="list-unstyled">
                                                        <li v-for="(tip, index) in task.tips">
                                                            <p v-text="tip" v-if="index <= usedTips"></p>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- List all available modules here with same ref -->
                                    <!-- <examplemodule ref="activeModule" v-if="taskmodule === 'MODULE_EXAMPLE'" :taskid="taskid" :taskdata="task"></examplemodule> -->
                                    <spreadsheet-module ref="activeModule" v-if="taskmodule === 'MODULE_SPREADSHEET'" :taskid="taskid" :taskdata="task"></spreadsheet-module>

                                </div>
                                <div :id="'extro-part-' + taskid" class="content" role="tabpanel" :aria-labelledby="'extro-part-trigger-' + taskid">
                                    <div v-if="task.extroFileType === 'IMAGE'" class="embed-responsive">
                                        <img :src="task.extro" class="img-fluid"/>
                                    </div>
                                    <div v-if="task.extroFileType === 'VIDEO'" class="embed-responsive embed-responsive-16by9">
                                        <video controls disablePictureInPicture controlsList="nofullscreen nodownload noremoteplayback">
                                            <source :src="task.extro">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                    <div v-if="task.extroFileType === 'TEXT'">
                                        <div class="alert alert-info" role="alert">
                                            <p v-text="task.extro"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" @click="nextStep()" v-text="stepperBtnText"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import Stepper from 'bs-stepper'
    import Swal from 'sweetalert2/src/sweetalert2.js'
    import VueConfetti from 'vue-confetti'
    import SpreadsheetModule from "./modules/SpreadsheetModule";

    Vue.use(VueConfetti)

    export default {
        components: {SpreadsheetModule},
        props: ["taskid", "taskmodule"],
        data() {
            return {
                stepper: null,
                currentStep: 0,
                stepperBtnText: this.$t('Next'),
                usedTips: -1,
                tipsVisible: false,
                task: {
                    description: "",
                    tips: [],
                    introFileType: "",
                    intro: "",
                    extroFileType: "",
                    extro: "",
                },
                moduleData: null,
                timer: 0
            }
        },
        async mounted() {

            var self = this;

            document.addEventListener('DOMContentLoaded', function () {
                // Initialize stepper
                self.stepper = new Stepper(document.querySelector('#bs-stepper-' + self.$props.taskid), {
                    linear: true,
                    animation: true
                });

                // Get task data
                axios.get("/student/getTask/" + self.$props.taskid)
                    .then(response => {
                        if (response.data.task) {
                            self.task = response.data.task;
                        }
                    }).catch(function (error) {
                    console.error(error);
                });
            });

            var stepperEl = document.querySelector('#bs-stepper-' + self.$props.taskid);
            stepperEl.addEventListener('show.bs-stepper', function (event) {
                self.currentStep = event.detail.indexStep;
            });
        },
        methods: {
            openTaskModal(id) {
                $('#taskModuleModal-' + id).modal('show');
            },
            showHint() {
                this.tipsVisible = true;
                if (this.usedTips < this.task.tips.length - 1)
                    this.usedTips++;
            },
            triggerTimer() {
                setTimeout(() => {
                        this.timer += 1;
                        this.triggerTimer();
                    }, 1000);
            },
            nextStep() {

                var self = this;

                if (this.currentStep === 0) {
                    this.stepperBtnText = this.$t('Solve');
                    this.stepper.next();

                    //Start timer, to measure the time how long the user needs
                    this.timer = 0;
                    this.triggerTimer();
                } else if (this.currentStep === 1) {

                    // Call pre Store function of module
                    this.$refs.activeModule._preStore();

                    var exportData = this.$refs.activeModule.$data;

                    /**
                     * Remove all internal values starting with _
                     */
                    this._removeInternalData(exportData);

                    this.moduleData = exportData;

                    // send solution to server to verify it
                    axios.post('/student/solveTask/' + self.$props.taskid, {
                        id: self.$props.taskid,
                        module: self.$props.taskmodule,
                        data: JSON.stringify(this.moduleData),
                        required_time: this.timer,
                        used_tips: this.usedTips + 1,
                    }).then(response => {

                        if (response.data.result) {

                            this.$confetti.start();

                            Swal.fire({
                                icon: 'success',
                                title: self.$t('Task solved'),
                                showConfirmButton: false,
                                timer: 1500
                            })

                            setTimeout(() => {
                                this.$confetti.stop();
                                self.stepperBtnText = self.$t('Close');
                                self.stepper.next();
                            }, 2000);

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

                } else if (this.currentStep === 2) {
                    location.reload();
                }
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
