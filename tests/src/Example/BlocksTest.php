<?php namespace Anomaly\Lexicon\Test\Example;

use Anomaly\Lexicon\Node\Block;
use Anomaly\Lexicon\Test\LexiconTestCase;

/**
 * Class BlocksTest
 *
 * @package Anomaly\Lexicon\Test\Example
 */
class BlocksTest extends LexiconTestCase
{
    /**
     * Set up node
     */
    public function setUpTest()
    {
        $this->node = new Block($this->lexicon);
    }

    /**
     * The bug is the last item name leaks into previous loops

     */
    public function testMultipleBlocksInATemplate()
    {
        $data = [
            'page' => [
                [
                    'messages' => [
                        'error'   => [
                            'Error #1',
                            'Error #2',
                            'Error #3',
                        ],
                        'success' => [
                            'Success #1',
                            'Success #2',
                            'Success #3',
                        ]
                    ]
                ]
            ]
        ];

        $expected = '<ul>
        <li>Success #1</li>
        <li>Success #2</li>
        <li>Success #3</li>
    </ul>
<ul>
        <li>Error #1</li>
        <li>Error #2</li>
        <li>Error #3</li>
    </ul>
';

        $this->assertEquals(
            $expected,
            $this->view->make('test::example/different-blocks-with-same-variable', $data)->render()
        );
    }

}
 