<?php namespace Aiws\Lexicon;

class Regex
{
    protected $scopeGlue = '.';

    public function __construct($scopeGlue = '.')
    {
        $this->scopeGlue = $scopeGlue;
    }

    public function setScopeGlue($scopeGlue = '.')
    {
        $this->scopeGlue = $scopeGlue;
    }

    public function compress($string)
    {
        return preg_replace(['/\s\s+/', '/\n+/'], ' ', trim($string));
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

    /**
     * Parses a parameter string into an array
     *
     * @param   string  The string of parameters
     * @return array
     */
    public function parseAttributes($string)
    {
        $attributes = [];

        $string = $this->compress($string);

        // Extract all literal string in the conditional to make it easier
        if (strpos($string, '"') !== false) {

            if (preg_match_all(
                '/(.*?)\s*=\s*(\'|"|&#?\w+;)(.*?)(?<!\\\\)\2/s',
                $string,
                $matches,
                PREG_SET_ORDER
            )
            ) {
                foreach ($matches as $match) {
                    $attributes[trim($match[1])] = trim($match[3]);
                }
            }

        } elseif (!empty($string)) {

            $attributes = explode(' ', $string);

            foreach ($attributes as &$attribute) {
                $attribute = trim($attribute);
            }

        }

        return $attributes;
    }

    public function extractNoParse($string)
    {
        preg_match_all('/\{\{\s*noparse\s*\}\}(.*?)\{\{\s*\/noparse\s*\}\}/ms', $string, $matches, PREG_SET_ORDER);

        $extractions = array();

        foreach ($matches as $match) {

            $extraction = array(
                'block'   => $match[0],
                'id'    => '__NO_PARSE__' . md5($match[0]),
                'content' => $match[1],
            );

            $string        = str_replace($extraction['block'], $extraction['id'], $string);
            $extractions[] = $extraction;
        }

        return ['content' => $string, 'extractions' => $extractions];
    }

    public function getVariableRegexMatcher()
    {
        $glue = preg_quote($this->scopeGlue, '/');

        return $glue === '\\.' ? '[a-zA-Z0-9_' . $glue . ']+' : '[a-zA-Z0-9_\.' . $glue . ']+';
    }

    public function getClosingTagRegexMatcher($name)
    {
        return '/\{\{\s*(\/' . $name . ')\s*\}\}/m';
    }

    public function getEmbeddedAttributeRegexMatcher()
    {
        return "/\{\s*?({$this->getVariableRegexMatcher()})(\s+.*?)?\s*?(\/)?\}/ms";
    }

    public function getEmbeddedMatches($string)
    {
        return $this->getMatches($string, $this->getEmbeddedAttributeRegexMatcher());
    }

    public function getMatch($text, $regex)
    {
        $match = [];
        preg_match($regex, $text, $match);
        return $match;
    }

    public function getMatches($text, $regex)
    {
        $matches = [];
        preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
        return $matches;
    }

}