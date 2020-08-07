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

                        <div :id="'bs-stepper-' + taskid" class="bs-stepper">
                            <div class="bs-stepper-header" role="tablist">
                                <!-- your steps here -->
                                <div class="step" :data-target="'#intro-part-' + taskid">
                                    <button type="button" class="step-trigger" role="tab" :aria-controls="'intro-part-' + taskid" :id="'intro-part-trigger-' + taskid">
                                        <span class="bs-stepper-circle"><i class="fas fa-film"></i></span>
                                        <span class="bs-stepper-label">Intro</span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" :data-target="'#task-part-' + taskid">
                                    <button type="button" class="step-trigger" role="tab" :aria-controls="'task-part-' + taskid" :id="'task-part-trigger-' + taskid">
                                        <span class="bs-stepper-circle"><i class="fas fa-tasks"></i></span>
                                        <span class="bs-stepper-label">Task</span>
                                    </button>
                                </div>
                                <div class="line"></div>
                                <div class="step" :data-target="'#extro-part-' + taskid">
                                    <button type="button" class="step-trigger" role="tab" :aria-controls="'extro-part-' + taskid" :id="'extro-part-trigger-' + taskid">
                                        <span class="bs-stepper-circle"><i class="fas fa-film"></i></span>
                                        <span class="bs-stepper-label">Extro</span>
                                    </button>
                                </div>
                            </div>
                            <div class="bs-stepper-content">
                                <!-- your steps content here -->
                                <div :id="'intro-part-' + taskid" class="content" role="tabpanel" :aria-labelledby="'intro-part-trigger-' + taskid">
                                    <div v-if="task.introType === 'IMAGE'" class="embed-responsive">
                                        <img :src="task.intro" class="img-fluid"/>
                                    </div>
                                    <div v-if="task.introType === 'VIDEO'" class="embed-responsive embed-responsive-16by9">
                                        <video controls disablePictureInPicture controlsList="nofullscreen nodownload noremoteplayback">
                                            <source :src="task.intro">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                </div>
                                <div :id="'task-part-' + taskid" class="content" role="tabpanel" :aria-labelledby="'task-part-trigger-' + taskid">
                                    task


                                    <!-- List all available module configs here with same ref -->
                                    <!-- <examplemoduleconfig ref="activeModule" v-if="taskmodule === 'MODULE_EXAMPLE'" :taskid="taskid"></examplemoduleconfig> -->


                                </div>
                                <div :id="'extro-part-' + taskid" class="content" role="tabpanel" :aria-labelledby="'extro-part-trigger-' + taskid">
                                    <div v-if="task.extroType === 'IMAGE'" class="embed-responsive">
                                        <img :src="task.extro" class="img-fluid"/>
                                    </div>
                                    <div v-if="task.extroType === 'VIDEO'" class="embed-responsive embed-responsive-16by9">
                                        <video controls disablePictureInPicture controlsList="nofullscreen nodownload noremoteplayback">
                                            <source :src="task.extro">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancle</button>
                        <button type="button" class="btn btn-primary" @click="stepper.next()">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import Stepper from 'bs-stepper'

    export default {
        props: ["taskid", "taskmodule"],
        data() {
            return {
                stepper :null,
                task: {
                    introType: "",
                    intro: "",
                    extroType: "",
                    extro: "",
                }
            }
        },
        async mounted() {
            console.log('Module:' + this.$props.taskmodule);

            var self = this;

            document.addEventListener('DOMContentLoaded', function () {
                self.stepper = new Stepper(document.querySelector('#bs-stepper-' + self.$props.taskid), {
                    linear: true,
                    animation: true
                });

                axios.get("/student/getTask/" + self.$props.taskid)
                    .then(response => {
                        if (response.data.task) {
                            self.task = response.data.task;
                        }
                    }).catch(function (error) {
                    console.error(error);
                });

            });
        },
        methods: {
            openTaskModal(id) {
                $('#taskModuleModal-' + id).modal('show');
            },
        }
    }
</script>
