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
                    <!-- TODO: Insert Content here -->
                </div>
            </div>
            <div class="tab-pane fade" id="solution" role="tabpanel" aria-labelledby="solution-tab">
                <div class="mt-1">
                    <!-- TODO: Insert Content here -->
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

    export default {
        props: ["taskid"],
        data() {
            return {
                tips: [],
            };
        },
        mounted() {
            console.log('TaskID:' + this.$props.taskid);
        }
    }
</script>

