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
                    <!-- TODO: Insert Content here -->
                </div>
            </div>
            <div class="tab-pane fade" v-bind:id="'solution-' + taskid" role="tabpanel" aria-labelledby="solution-tab">
                <div class="mt-1">
                    <!-- TODO: Insert Content here -->
                </div>
            </div>
            <div class="tab-pane fade" v-bind:id="'tips-' + taskid" role="tabpanel" aria-labelledby="tips-tab">
                <div class="mt-1">
                    <tip-list ref="tips" :taskid="taskid"></tip-list>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: ["taskid"],
        data() {
            return {
                example: null,
                /*
                * INTERNAL IS GOING TO BE EXCLUDED FROM EXPORT
                * starts with _
                * */
                _example: null
            };
        },
        mounted() {
            console.log('TaskID:' + this.$props.taskid);

            var self = this;

            $('#taskModuleModal-' + this.$props.taskid).on('shown.bs.modal', function () {

                axios.get("/teacher/getTask/" + self.$props.taskid)
                    .then(response => {

                        if (response.data) {
                            // Fill with stored data
                            self.example = response.data.example;
                        }
                    }).catch(function (error) {
                        console.error(error);
                    });

            });
        }
    }
</script>

