<?php namespace Aiws\Lexicon\Util;

use Aiws\Lexicon\Contract\EnvironmentInterface;

class Regex
{
    protected $lexicon;

    public function __construct(EnvironmentInterface $lexicon)
    {
        $this->lexicon = $lexicon;
    }

    public function compress($string)
    {
        return preg_replace('/\s\s+/', ' ', trim($string));
    }

    /**
     * Removes all of the comments from the text.
     *
     * @param  string $string Text to remove comments from
     * @return string
     */
    public function parseComments($string)
    {
        return preg_replace('/\{\{#.*?#\}\}/s', '', $string);
    }

    public function extractNoParse($string)
    {
        preg_match_all('/\{\{\s*noparse\s*\}\}(.*?)\{\{\s*\/noparse\s*\}\}/ms', $string, $matches, PREG_SET_ORDER);

        $extractions = array();

        foreach ($matches as $match) {

            $extraction = array(
                'block'   => $match[0],
                'hash'    => '__NO_PARSE__' . md5($match[0]),
                'content' => $match[1],
            );

            $string        = str_replace($extraction['block'], $extraction['hash'], $string);
            $extractions[] = $extraction;
        }

        return ['content' => $string, 'extractions' => $extractions];
    }

}