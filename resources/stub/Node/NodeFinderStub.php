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

        $if = $nodeFactory->make(new Conditional($lexicon),[],null,0,0,'if');
        $if->setName('if');

        $if2 = $nodeFactory->make(new Conditional($lexicon),[],null,0,0,'if');
        $if2->setName('if');

        $elseif = $nodeFactory->make(new Conditional($lexicon),[],null,0,0,'elseif');
        $elseif->setName('elseif');

        $endif = $nodeFactory->make(new ConditionalEndif($lexicon),[],null,0,0,'endif');
        $endif->setName('endif');

        $schoolName = $nodeFactory->make(new NodeStub($lexicon),[],null,0,0,'name');
        $schoolName->setName('name');

        $books = $nodeFactory->make(new Block($lexicon),[],null,0,0,'books');
        $books->setName('books');
        $books->setItemAlias('book');

        $books->setCurrentContent(
            '
        Anomaly\Lexicon\Node\NodeType\Conditional__if__if__
        Anomaly\Lexicon\Node\NodeType\Conditional__if2__if2__
        Anomaly\Lexicon\Node\NodeType\Conditional__elseif__elseif__
        Anomaly\Lexicon\Node\NodeType\ConditionalEndif__endif__enif__
        Anomaly\Lexicon\Stub\Node\Node__name__name__
        '
        );

        $books->addChild($schoolName);
        $books->addChild($if);
        $books->addChild($if2);
        $books->addChild($elseif);
        $books->addChild($endif);

        $libraries = $nodeFactory->make(new Block($lexicon),[],null,0,0,'libraries');
        $libraries->addChild($books);
        $libraries->setName('libraries');
        $libraries->setItemAlias('library');

        $schools = $nodeFactory->make(new Block($lexicon),[],null,0,0,'schools');
        $schools->addChild($libraries);
        $schools->setName('schools');
        $schools->setItemAlias('school');

        $root = $nodeFactory->make(new Block($lexicon));
        $root->addChild($schools);

        return $lexicon->getFoundation()->getNodeFinder($schoolName);
    }

} 