/**
 * Created by spatra on 14-12-2.
 */


define(['projectJS/module',

    'projectJS/controllers/project',
    'projectJS/controllers/ngUI',
    'projectJS/controllers/task',
    'projectJS/controllers/member',
    'projectJS/controllers/discussion',
    'projectJS/controllers/sharing',
    'projectJS/controllers/mindmap',

    'projectJS/services/discussion',
    'projectJS/services/member',
    'projectJS/services/project',
    'projectJS/services/sharing',
    'projectJS/services/task',

    'projectJS/directives'],
  function(projectModule){
    return projectModule.name;
});
