<?php

/**
 * Lexicon config
 */
return array(

    /**
     * The extension that tells the view environment when to use the Lexicon view engine to process views.
     */
    'extension'        => 'html',
    /**
     * The default scope glue that is used for parsing and getting variables with dot notation or a different character
     * if set here. We highly recommend that you leave the default `.`.
     */
    'scopeGlue'        => '.',
    /**
     * Plugins used for interpreting and outputting custom data. You can add you custom plugins here. Each one must have
     * a key that will represent the tag. i.e {{ counter.count }}
     */
    'plugins'          => [
        'counter' => 'Anomaly\Lexicon\Plugin\CounterPlugin',
    ],
    /**
     * Conditional boolean test types
     */
    'booleanTestTypes' => [
        'itemTest'   => 'Anomaly\Lexicon\Conditional\Test\ItemTest',
        'stringTest' => 'Anomaly\Lexicon\Conditional\Test\StringTest',
    ],
    /**
     * Node types used for compiling. The order is very important as it will affect
     * how the template is processed. You can define `groups` which allow to create new
     * instances of Lexicon that only compiles templates using a subset of the node types.
     * Node types are listed here in `node groups`, the `all` node set
     * is the default, you can use a different node groups by calling using() method
     * from a view. Here is an example.
     * view('foo', $data)->using('simple')->render()
     */
    'nodeGroups'       => [
        'all'       => [
            'Anomaly\Lexicon\Node\NodeType\Comment',
            'Anomaly\Lexicon\Node\NodeType\IgnoreBlock',
            'Anomaly\Lexicon\Node\NodeType\IgnoreVariable',
            'Anomaly\Lexicon\Node\NodeType\Block',
            'Anomaly\Lexicon\Node\NodeType\Conditional',
            'Anomaly\Lexicon\Node\NodeType\ConditionalElse',
            'Anomaly\Lexicon\Node\NodeType\ConditionalEndif',
            'Anomaly\Lexicon\Node\NodeType\Recursive',
            'Anomaly\Lexicon\Node\NodeType\Section',
            'Anomaly\Lexicon\Node\NodeType\SectionAppend',
            'Anomaly\Lexicon\Node\NodeType\SectionExtends',
            'Anomaly\Lexicon\Node\NodeType\SectionOverwrite',
            'Anomaly\Lexicon\Node\NodeType\SectionShow',
            'Anomaly\Lexicon\Node\NodeType\SectionStop',
            'Anomaly\Lexicon\Node\NodeType\SectionYield',
            'Anomaly\Lexicon\Node\NodeType\Includes',
            'Anomaly\Lexicon\Node\NodeType\VariableUnescaped',
            'Anomaly\Lexicon\Node\NodeType\Variable',
        ],
        /**
         * Compile without layout features
         */
        'simple'    => [
            'Anomaly\Lexicon\Node\NodeType\Comment',
            'Anomaly\Lexicon\Node\NodeType\Conditional',
            'Anomaly\Lexicon\Node\NodeType\ConditionalElse',
            'Anomaly\Lexicon\Node\NodeType\ConditionalEndif',
            'Anomaly\Lexicon\Node\NodeType\IgnoreBlock',
            'Anomaly\Lexicon\Node\NodeType\IgnoreVariable',
            'Anomaly\Lexicon\Node\NodeType\Block',
            'Anomaly\Lexicon\Node\NodeType\Recursive',
            'Anomaly\Lexicon\Node\NodeType\VariableUnescaped',
            'Anomaly\Lexicon\Node\NodeType\Variable',
        ],
        /**
         * Compile without Blocks
         */
        'variables' => [
            'Anomaly\Lexicon\Node\NodeType\Comment',
            'Anomaly\Lexicon\Node\NodeType\IgnoreVariable',
            'Anomaly\Lexicon\Node\NodeType\Conditional',
            'Anomaly\Lexicon\Node\NodeType\ConditionalElse',
            'Anomaly\Lexicon\Node\NodeType\ConditionalEndif',
            'Anomaly\Lexicon\Node\NodeType\VariableUnescaped',
            'Anomaly\Lexicon\Node\NodeType\Variable',
        ]
    ],
    /**
     * Compiler sequence (experimental feature)
     * The compiler sequence allows templates to be processed by other
     * compilers other than the Lexicon source compiler. They will process the template
     * the order specified here. For example, you want to process the template
     * with markdown, before it gets processed by Lexicon.
     *
     * Example
     * 'compilerSequence' => [
     * 'App\Compiler\MarkdownCompiler',
     * 'Anomaly\Lexicon\View\LexiconCompiler',
     * ]
     */
);