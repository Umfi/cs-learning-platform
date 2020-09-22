# CS Learning Platform - Developer Notes

Hi! In this file, i am going to write down some useful hints that may be interesting if one wants to extend the existing platform.


## Create a new module

* Add a new constant for the new module in App/Task.php in the *MODULES* array.

`const MODULES = array( 
    "MODULE_SPREADSHEET" => "Spreadsheet", 
    "MODULE_NEW" => "New Module",
    );`

* Create a new Vue file for the configuration of the new module in resources/js/components/teacher/backend/modules (for teachers)
	* You can use the ExampleModuleConfig.vue as a starting point, it includes everything that is required to get started

* Add the newly created config in the TaskModuleConfigComponent.vue in resources/js/components/teacher/backend

`<newmoduleconfig ref="activeModule" v-if="taskmodule === 'MODULE_NEW'" :taskid="taskid"></newmoduleconfig>`

* Adopt the function *storeModuleConfig()* in App/Task.php
	* Here it is important to save a specification and a solution to the Task Model

* Create a new Vue file for  the new module in resources/js/components/student/modules (for students)
	* You can use the ExampleModule.vue as a starting point, it includes everything that is required to get started

* Add the newly created config in the TaskComponent.vue in resources/js/components/student

`<new-module ref="activeModule" v-if="taskmodule === 'MODULE_NEW' && loaded" :taskid="taskid" :taskdata="task" :type="'assignment'"></new-module>`

* Add the newly created config in the TaskSolutionViewerComponent.vue in resources/js/components/student

`<new-module ref="activeModule" v-if="taskmodule === 'MODULE_NEW' && loaded" :taskid="taskid" :taskdata="task" :type="'solution'"></new-module>`

* Adopt the function *checkSolution()* in App/Task.php
	* In this function you should implement a logic that checks if the submitted user input matches with the solution


## Lifecycle of a module

A task loads the data stored in the database and passes the data to the specific task module. When a task is started the *_onOpen()* function of the module is called. Within this function you can setup the vue component and setup everything that is required by it. Before a task is submitted the *_preStore()* function of the task module is triggered. Within this function you can assure that every variable of the vue component has the correct value before it is submitted to the server. Note that every variable starting with a _ is excluded from the export and won't be send to the server.

