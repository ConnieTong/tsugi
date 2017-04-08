<?php

/**
 * The Tsugi variant of a Silex Application
 *
 * This needs the session started before it is called
 * 
 */

namespace Tsugi\Silex;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class Application extends \Silex\Application {

    function __construct($launch, array $values = array()) {
        global $CFG;
        parent::__construct($values);
        if ( ! isset($CFG->loader) ) {
            echo("<pre>\n".'Please fix your config.php to set $CFG->loader as follows:'."\n");
            echo('$loader = require_once($dirroot."/vendor/autoload.php");'."\n");
            echo("...\n".'$CFG = ...'."\n...\n");
            echo('$CFG->loader = $loader;'."\n");
            echo("Please see config-dist.php for sample code.\n</pre>\n");
            die('Need to set $CFG->loader');
        }
        $this['tsugi'] = $launch;
        $launch->output->buffer = true;  // Buffer output

        $session = new Session(new PhpBridgeSessionStorage());
        $session->start();
        $this['session'] = $session;

        $loader = new \Twig_Loader_Filesystem('templates');
        $yourNewPath = $CFG->dirroot . '/vendor/tsugi/lib/src/Templates';
        $loader->addPath($yourNewPath, 'Tsugi');
        $yourNewPath = $CFG->dirroot . '/vendor/koseu/lib/src/Templates';
        if ( file_exists($yourNewPath) ) {
            $loader->addPath($yourNewPath, 'Koseu');
        }
        $CFG->loader->addPsr4('AppBundle\\', 'src/AppBundle');

        //$loader = new \Tsugi\Twig\Twig_Loader_Class();
        $this->register(new \Silex\Provider\TwigServiceProvider(), array(
            'twig.loader' => $loader
        ));
    }
}