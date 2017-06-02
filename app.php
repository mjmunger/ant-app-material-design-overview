<?php

namespace MyNewService\Overview;

/**
 * App Name: Overview Microservice
 * App Description: A demonstration of URI specific Microservices
 * App Version: 1.0
 * App Action: cli-load-grammar -> loadOverviewService @ 90
 * App Action: cli-init         -> declareMySelf  @ 50
 * App Action: cli-command      -> processCommand @ 50
 * App Action: show-overview    -> showOverview   @ 50
 *
 * App URI: '/overview\/[0-9]/'   -> show-overview  @ 50
 */

 /**
 * This app adds the Overview Microservice and commands into the CLI by adding in
 * the grammar for commands into an array, and returning it up the chain.
 *
 * @package      MyNewService
 * @subpackage   Overview
 * @category     Overview Microservice Demonstration
 * @author       Michael Munger <michael@highpoweredhelp.com>
 */ 


class OverviewService extends \PHPAnt\Core\AntApp implements \PHPAnt\Core\AppInterface  {

    /**
     * Instantiates an instance of the OverviewService class.
     * Example:
     *
     * <code>
     * $appOverviewService = new OverviewService();
     * </code>
     *
     * @return void
     * @author Michael Munger <michael@highpoweredhelp.com>
     **/

    function __construct() {
        $this->appName = 'Overview Microservice';
        $this->canReload = true;
        $this->path = __DIR__;
    }

    /**
     * Callback for the cli-load-grammar action, which adds commands specific to this plugin to the CLI grammar.
     * Example:
     *
     * @return array An array of CLI grammar that will be merged with the rest of the grammar. 
     * @author Michael Munger <michael@highpoweredhelp.com>
     **/

    function loadOverviewService() {
        $grammar = [];

        $this->loaded = true;
        
        $results['grammar'] = $grammar;
        $results['success'] = true;
        return $results;
    }

    //Uncomment this function and the following function to enable the autoloader for this plugin.
    function OverviewServiceAutoLoader() {
        //REGISTER THE AUTOLOADER! This has to be done first thing! 
        spl_autoload_register(array($this,'loadOverviewServiceClasses'));
        return ['success' => true];

    }

    public function loadOverviewServiceClasses($class) {
        $baseDir = $this->path;

        $candidate_files = array();

        //Try to grab it from the classes directory.
        $candidate_path = sprintf($baseDir.'/classes/%s.class.php',$class);
        array_push($candidate_files, $candidate_path);

        //Loop through all candidate files, and attempt to load them all in the correct order (FIFO)
        foreach($candidate_files as $dependency) {
            if($this->verbosity > 14) printf("Looking to load %s",$dependency) . PHP_EOL;

            if(file_exists($dependency)) {
                if(is_readable($dependency)) {

                    //Print debug info if verbosity is greater than 9
                    if($this->verbosity > 9) print "Including: " . $dependency . PHP_EOL;

                    //Include the file!
                    include($dependency);
                }
            }
        }
        return ['success' => true];
    }
    
    /**
     * Callback function that prints to the CLI during cli-init to show this plugin has loaded.
     * Example:
     *
     * @return array An associative array declaring the status / success of the operation.
     * @author Michael Munger <michael@highpoweredhelp.com>
     **/

    function declareMySelf() {
        if($this->verbosity > 4 && $this->loaded ) print("Overview Microservice app loaded.\n");

        return ['success' => true];
    }

    function processCommand($args) {
        $cmd = $args['command'];

        return ['success' => true];
    }

    function showNextStep($args) {
        $matches = NULL;
        preg_match('/overview\/([0-9])/', $args['AE']->Configs->Server->Request->uri,$matches);
        $step = $matches[1];

        $nextStep = ($step < 9 ? $step + 1 : $step);
        $lastStep = ($step > 1 ? $step - 1 : $step);

        echo "<p>This step: $step</p>";
        printf('<a href="/overview/%s/">Move on to step %s</a>',$nextStep,$nextStep);
        printf('<a href="/overview/%s/">Move back to previous step %s</a>',$lastStep,$lastStep);
    }

    function showOverview($args) {
        $args['AE']->runActions('include-navigation');

        //Run steps wizard
        $this->showNextStep($args);

        $args['AE']->runActions('include-footer');
        
        return ['success' => true, 'exit' => true];
    }

}