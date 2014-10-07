<?php namespace Anomaly\Lexicon\Stub\Node;

use Anomaly\Lexicon\Node\NodeType\Block;
use Anomaly\Lexicon\Node\NodeType\Conditional;
use Anomaly\Lexicon\Node\NodeType\ConditionalEndif;
use Anomaly\Lexicon\Stub\LexiconStub;
use Anomaly\Lexicon\Stub\Node\Node as NodeStub;

/**
 * Class NodeFinderStub
 *
 * @package Anomaly\Lexicon\Stub\Node
 */
class NodeFinderStub
{

    public static function get()
    {
        $lexicon     = LexiconStub::get();
        $nodeFactory = $lexicon->getFoundation()->getNodeFactory();

        $if = $nodeFactory->make(new Conditional($lexicon));
        $if->setName('if');

        $if2 = $nodeFactory->make(new Conditional($lexicon));
        $if2->setName('if');

        $elseif = $nodeFactory->make(new Conditional($lexicon));
        $elseif->setName('elseif');

        $endif = $nodeFactory->make(new ConditionalEndif($lexicon));
        $endif->setName('endif');

        $match = [
            ['content', 25]
        ];

        $schoolName = $nodeFactory->make(new NodeStub($lexicon),$match);
        $schoolName->setName('name');

        $books = $nodeFactory->make(new Block($lexicon));
        $books->setName('books');
        $books->setItemAlias('book');

        $books->addChild($schoolName);
        $books->addChild($if);
        $books->addChild($if2);
        $books->addChild($elseif);
        $books->addChild($endif);

        dd($books->getChildren());

        $libraries = $nodeFactory->make(new Block($lexicon));
        $libraries->addChild($books);
        $libraries->setName('libraries');
        $libraries->setItemAlias('library');

        $schools = $nodeFactory->make(new Block($lexicon));
        $schools->addChild($libraries);
        $schools->setName('schools');
        $schools->setItemAlias('school');

        $root = $nodeFactory->make(new Block($lexicon));
        $root->addChild($schools);

        return $lexicon->getFoundation()->getNodeFinder($schoolName);
    }

} 