<?php
use Anomaly\Lexicon\Contract\LexiconInterface;
use Anomaly\Lexicon\View\Compiler\Compiler;
use Anomaly\Lexicon\View\Compiler\CompilerEngine;
use Illuminate\Filesystem\Filesystem;

/**
 * Created by PhpStorm.
 * User: ob
 * Date: 9/7/14
 * Time: 10:55 AM
 */
class CompilerEngineTest extends LexiconTest
{
/*
    public function testEvaluatePath()
    {
        $view   = 'Template';
        $method = new ReflectionMethod($this->engine, 'evaluatePath');
        $method->setAccessible(true);
        $method->invoke($this->newEngine(), $this->getCompiledPath() . '/' . $view, []);
        $this->assertTrue(class_exists('Anomaly\Lexicon\View\View_' . $view));
    }

    **
     * @expectedException \ErrorException
     *
    public function testEvaluatePathAndHandleException()
    {
        $view   = 'TemplateCauseException';
        $method = new ReflectionMethod($this->newEngine(), 'evaluatePath');
        $method->setAccessible(true);
        $method->invoke($this->engine, $this->getCompiledPath() . '/' . $view, []);
        $this->assertTrue(class_exists('Anomaly\Lexicon\View\View_' . $view));
    }

    public function testGetView()
    {
        $view    = 'Template';
        $lexicon = $this->newLexicon()->registerNodeTypes(
            [
                'Anomaly\Lexicon\Node\Block',
                'Anomaly\Lexicon\Node\Variable'
            ]
        );
        $engine  = $this->newEngine($lexicon);
        $path    = $engine->getCompiler()->setLexicon($lexicon)->getCompiledPath($view);
        $engine->get($path, []);
    }

    public function testGetParseView()
    {
        $view    = '{{ hello }}';
        $lexicon = $this->newLexicon()->registerNodeType('Anomaly\Lexicon\Node\Block');
        $lexicon->addParsePath($view);
        $this->newEngine($lexicon)->get($view, []);
    }

    public function newEngine(LexiconInterface $lexicon = null)
    {
        if (!$lexicon) {
            $lexicon = $this->newLexicon();
        }

        $compiler = new Compiler(new Filesystem(), $this->getCompiledPath());
        $compiler->setLexicon($lexicon);
        return new CompilerEngine($compiler);
    }*/
}
 