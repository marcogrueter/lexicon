<?php namespace Anomaly\Lexicon;

class Regex
{
    /**
     * Scope glue
     *
     * @var string
     */
    protected $scopeGlue = '.';

    /**
     * @param string $scopeGlue
     */
    public function __construct($scopeGlue = '.')
    {
        $this->scopeGlue = $scopeGlue;
    }

    /**
     * Set scope glue
     *
     * @param string $scopeGlue
     */
    public function setScopeGlue($scopeGlue = '.')
    {
        $this->scopeGlue = $scopeGlue;
    }

    /**
     * Compress
     *
     * @param $string
     * @return mixed
     */
    public function compress($string)
    {
        return preg_replace(['/\s\s+/', '/\n+/'], ' ', trim($string));
    }

    /**
     * @param $string
     * @return array
     */
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

    /**
     * Get variable matcher
     *
     * @return string
     */
    public function getVariableRegexMatcher()
    {
        $glue = preg_quote($this->scopeGlue, '/');

        return $glue === '\\.' ? '[a-zA-Z0-9_' . $glue . ']+' : '[a-zA-Z0-9_\.' . $glue . ']+';
    }

    /**
     * Get closing tag regex
     *
     * @param $name
     * @return string
     */
    public function getClosingTagRegexMatcher($name)
    {
        return '/\{\{\s*(\/' . $name . ')\s*\}\}/m';
    }

    /**
     * Get embedded attribute regex
     *
     * @return string
     */
    public function getEmbeddedAttributeRegexMatcher()
    {
        return "/\{\s*?({$this->getVariableRegexMatcher()})(\s+.*?)?\s*?(\/)?\}/ms";
    }

    /**
     * Get embedded matches
     *
     * @param $string
     * @return array
     */
    public function getEmbeddedMatches($string)
    {
        return $this->getMatches($string, $this->getEmbeddedAttributeRegexMatcher());
    }

    /**
     * Get match
     *
     * @param $text
     * @param $regex
     * @return array
     */
    public function getMatch($text, $regex)
    {
        $match = [];
        preg_match($regex, $text, $match);
        return $match;
    }

    /**
     * Get matches
     *
     * @param $text
     * @param $regex
     * @return array
     */
    public function getMatches($text, $regex)
    {
        $matches = [];
        preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
        return $matches;
    }

}