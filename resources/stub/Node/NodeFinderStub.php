<?php namespace Anomaly\Lexicon\Stub\Node;

use Anomaly\Lexicon\Node\NodeType\Block;
use Anomaly\Lexicon\Node\NodeType\Conditional;
use Anomaly\Lexicon\Node\NodeType\ConditionalEndif;
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

        $if = new Conditional($lexicon);
        $if->setId('if');
        $if->setName('if');
        $nodeFactory->addNode($if);

        $if2 = new Conditional($lexicon);
        $if2->setId('if2');
        $if2->setName('if');
        $nodeFactory->addNode($if2);

        $elseif = new Conditional($lexicon);
        $elseif->setId('elseif');
        $elseif->setName('elseif');
        $nodeFactory->addNode($elseif);

        $endif = new ConditionalEndif($lexicon);
        $endif->setId('endif');
        $nodeFactory->addNode($endif);

        $schoolName->setId(4);
        $schoolName->setName('schoolName');
        $nodeFactory->addNode($schoolName);

        $books = new Block($lexicon);
        $books->setId(3);
        $books->setName('books');
        $books->setItemAlias('book');
        $nodeFactory->addNode($books);
        $books->addChild($schoolName);
        $books->addChild($if);
        $books->addChild($if2);
        $books->addChild($elseif);
        $books->addChild($endif);

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