<template>
    <div class="container">
        <div id="drawflow">
            <div class="bar-zoom btn-group" role="group">
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#learningPathInfoModal"><i class="fas fa-info"></i></button>
                <button type="button" class="btn btn-secondary" @click="editor.zoom_out()"><i class="fas fa-search-minus"></i></button>
                <button type="button" class="btn btn-secondary" @click="editor.zoom_reset()"><i class="fas fa-search"></i></button>
                <button type="button" class="btn btn-secondary" @click="editor.zoom_in()"><i class="fas fa-search-plus"></i></button>
            </div>
        </div>
        <hr>
        <button type="button" class="btn btn-primary float-right" @click="storeLearningPath()"><i class="fas fa-save"></i> {{ $t('Save') }}</button>
        <button type="button" class="btn btn-danger float-right mr-2" @click="init()"><i class="fas fa-trash"></i> {{ $t('Reset') }}</button>

        <div id="learningPathInfoModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $t('Info') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{ $t('Each node represents a task. You can connect the nodes by clicking on an output connector (green/yellow/red circles) and draw a line to the input connector (blue circle) of an other node.') }}</p>
                        <p>{{ $t('The green connector should be used if the student solves the task well. The yellow one if the task was solved averagely. The red one otherwise.') }}</p>
                        <p>{{ $t('The start node should only have outgoing connections (no connections to blue circle). Only one start node is allowed. It is possible to have multiple end nodes.') }}</p>
                        <hr>
                        <p>{{ $t('To remove a line, use right click on it and click the x.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

    import Drawflow from 'drawflow'
    import styleDrawflow from 'drawflow/dist/drawflow.min.css'
    import Vue from 'vue'
    import Swal from 'sweetalert2/src/sweetalert2.js'

    export default {
        props: ["topic"],
        data() {
            return {
                editor: null,
                topicdata: null
            };
        },
        mounted() {
            var id = document.getElementById("drawflow");
            this.editor = new Drawflow(id, Vue);
            this.editor.start();

            var self = this;

            // Every output point can only has one connection to another node
            this.editor.on('connectionCreated', function(connection) {
                var currentNode = self.editor.getNodeFromId(connection.output_id);
                var outputNode = currentNode.outputs[connection.output_class].connections;
                if (outputNode.length > 1) {
                    self.editor.removeSingleConnection(connection.output_id, connection.input_id, connection.output_class, connection.input_class);
                }
            });


            axios.get("/teacher/getTopic/" + self.$props.topic)
                .then(response => {
                    if (response.data.topic) {
                        self.topicdata = response.data.topic;

                        if (self.topicdata != null) {

                            // No learning path exists yet, create a new one, otherwise load it
                            if  (typeof self.topicdata.learningpath === "undefined"
                                || self.topicdata.learningpath === "") {
                                self.init();
                            } else {
                                // import data
                                self.editor.import(self.topicdata.learningpath);

                                //update existing nodes
                                self.updateExistingTasks();

                                // check if there are tasks which are not yet included in the learning path
                                self.importMissingTasks();

                                // store updated version
                                self.storeLearningPath(false);
                            }

                            // Update titles of connection points
                            self.updateNodeTitles();
                       }
                    }
                }).catch(function (error) {
                console.error(error);
            });

        },
        methods: {
            init() {
                this.editor.clear();

                var offset = 0;
                for (let task of this.topicdata.tasks) {
                    if (task.active) {
                        this.editor.addNode('Home', 1, 3, 50, 50 + offset, '', {"task": task._id}, task.name);
                        offset += 80;
                    }
                }
            },
            storeLearningPath(showAlert = true) {
                //The node that has no input connection, it is the start node.
                //the node that has no output connections is a final node.
                if (!this.validatePath(showAlert)) {
                    return;
                }

                var self = this;
                var id = this.$props.topic;

                axios.post('/teacher/topic/' + id + '/storeLearningPath', {
                    id: id,
                    data: JSON.stringify(this.editor.export())
                }).then(response => {

                    if (showAlert) {
                        if (response.data.result) {
                            Swal.fire({
                                icon: 'success',
                                title: self.$t('Learning path has been successfully saved.'),
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: self.$t('Oops...'),
                                text: response.data.message,
                            });
                        }
                    }
                }).catch(function (error) {
                    console.error(error);
                });
            },
            validatePath(showAlert = true) {

                var currentState = this.editor.export();
                var lpNode = currentState.drawflow.Home.data;


                var startNodes = 0;
                var endNodes = 0;

                for (let node in lpNode) {
                    if (lpNode[node].inputs.input_1.connections.length == 0) {
                        startNodes++;
                    }
                    if (lpNode[node].outputs.output_1.connections.length == 0
                        && lpNode[node].outputs.output_2.connections.length == 0
                        && lpNode[node].outputs.output_3.connections.length == 0) {
                        endNodes++;
                    }
                }

                // Check if there is only one start node
                if (startNodes > 1) {
                    if (showAlert) {
                        Swal.fire({
                            icon: 'error',
                            title: this.$t('Invalid Learning Path'),
                            text: this.$t('Only one start node allowed. There exists multiple nodes with no ingoing connections.'),
                        });
                    }
                    return false;
                }

                // Check if there exists at least one end node
                if (endNodes === 0) {
                    if (showAlert) {
                        Swal.fire({
                            icon: 'error',
                            title: this.$t('Invalid Learning Path'),
                            text: this.$t('No end node found. Possible graph cycle detected.'),
                        });
                    }
                    return false;
                }

                return true;
            },
            updateNodeTitles() {
                $('.output.output_1').prop('title', this.$t('What should happen if the student solves the task well?'));
                $('.output.output_2').prop('title', this.$t('What should happen if the student solves the task averagely well?'));
                $('.output.output_3').prop('title', this.$t('What should happen if the student does not solve the task so well?'));
            },
            importMissingTasks() {

                var tasks = this.topicdata.tasks;
                var lpNode = this.topicdata.learningpath.drawflow.Home.data;

                var offset = 500;
                for (let task of tasks) {
                    if (task.active) {

                        var found = false;
                        for (let node in lpNode) {
                            if (lpNode[node].data.task === task._id) {
                                found = true;
                                break;
                            }
                        }

                        if (!found) {
                            this.editor.addNode('Home', 1, 3, 50, 50 + offset, '', {"task": task._id}, task.name);
                            offset += 80;
                        }
                    }
                }
            },
            updateExistingTasks() {
                var tasks = this.topicdata.tasks;
                var lpNode = this.topicdata.learningpath.drawflow.Home.data;

                var nodesToRemove = [];

                for (let node in lpNode) {
                    var found = false;
                    for (let task of tasks) {
                        if (lpNode[node].data.task === task._id) {
                            // If not active, remove too
                            if (task.active) {
                                found = true;
                            }
                            // Update Label
                            lpNode[node].html = task.name;
                            break;
                        }
                    }

                    if (!found) {
                        nodesToRemove.push(lpNode[node].id);
                    }
                }

                this.editor.clear();
                this.editor.import(this.topicdata.learningpath);

                // Delete Nodes, that are not active anymore, or are deleted
                for (let node of nodesToRemove) {
                    this.editor.removeNodeId("node-" + node);
                }

            }
        }
    }
</script>
