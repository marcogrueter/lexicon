<?php namespace Aiws\Lexicon\Provider\Laravel\Node;

use Aiws\Lexicon\Node\Single;

class Set extends Single
{

    /**
     * Node name
     *
     * @var string
     */
    protected $name = 'set';

    /**
     * Compile source
     *
     * @return string
     */
    public function compile()
    {
        $source = "<?php\n";

        foreach($this->getAttributes() as $key => $value) {
            $source .= "\${$key} = '{$value}';\n";
        }

        return $source."?>";
    }

}