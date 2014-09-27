<?php namespace Anomaly\Lexicon\Stub\Node;

use Anomaly\Lexicon\Node\NodeType\Block;
use Anomaly\Lexicon\Stub\Node\Node as NodeStub;
use Anomaly\Lexicon\Stub\LexiconStub;

/**
 * Class NodeFinderStub
 *
 * @package Anomaly\Lexicon\Stub\Node
 */
class NodeFinderStub 
{

    public static function get()
    {
        $lexicon = LexiconStub::get();
        $nodeFactory = $lexicon->getFoundation()->getNodeFactory();

        $schoolName = new NodeStub($lexicon);

        $schoolName->setId(4);
        $nodeFactory->addNode($schoolName);

        $books = new Block($lexicon);
        $books->setId(3);
        $books->addChild($schoolName);
        $books->setName('books');
        $books->setItemAlias('book');
        $nodeFactory->addNode($books);

        $libraries = new Block($lexicon);
        $libraries->setId(2);
        $libraries->addChild($books);
        $libraries->setName('libraries');
        $libraries->setItemAlias('library');
        $nodeFactory->addNode($libraries);

        $schools = new Block($lexicon);
        $schools->setId(1);
        $schools->addChild($libraries);
        $schools->setName('schools');
        $schools->setItemAlias('school');
        $nodeFactory->addNode($schools);

        $root = new Block($lexicon);
        $root->setId(0);
        $root->addChild($schools);
        $nodeFactory->addNode($root);

        return $lexicon->getFoundation()->getNodeFinder($schoolName);
    }

} 