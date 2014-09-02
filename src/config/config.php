<?php

/**
 * Lexicon config
 */
return array(

    /**
     * Plugins used for interpreting and outputting custom data. You can add you custom plugins here. Each one must be
     *
     */
    'plugins' => [

    ],

    /**
     * Node types used for parsing and compiling
     */
    'nodeTypes' => [
        'Anomaly\Lexicon\Node\Comment',
        'Anomaly\Lexicon\Node\Parents',
        'Anomaly\Lexicon\Node\Block',
        'Anomaly\Lexicon\Node\Recursive',
        'Anomaly\Lexicon\Node\Section',
        'Anomaly\Lexicon\Node\SectionExtends',
        'Anomaly\Lexicon\Node\SectionShow',
        'Anomaly\Lexicon\Node\SectionStop',
        'Anomaly\Lexicon\Node\SectionYield',
        'Anomaly\Lexicon\Node\Includes',
        'Anomaly\Lexicon\Node\Conditional',
        'Anomaly\Lexicon\Node\ConditionalElse',
        'Anomaly\Lexicon\Node\ConditionalEndif',
        'Anomaly\Lexicon\Node\VariableEscaped',
        'Anomaly\Lexicon\Node\Variable',
    ],

    /**
     * The extension that tells the view environment when to use the Lexicon view engine to process views.
     */
    'extension' => 'html',

    /**
     * The default scope glue that is used for parsing and getting variables with dot notation or a different character
     * if set here. We highly recommend that you leave the default `.`.
     */
    'scopeGlue' => '.',

    /**
     * When debug is turned on it enables exceptions on certain parts where things fail silently. Generic exceptions
     * will always be logged. Also php segments on the view will be compiled each on a new line for readability but
     * will compile compressed when debug is off.
     */
    'debug' => true,

    /**
     * PHP is escaped from views by default but you can enable it if you need it for any reason.
     */
    'allowPhp' => false,

);