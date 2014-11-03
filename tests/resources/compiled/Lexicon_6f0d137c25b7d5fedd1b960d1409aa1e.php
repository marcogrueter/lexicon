<?php namespace Anomaly\Lexicon\View\Compiled; class Lexicon_6f0d137c25b7d5fedd1b960d1409aa1e extends \Anomaly\Lexicon\View\CompiledView { public function render($__data) {
?><ul>
<?php foreach($this->variable($__data,'books',[],'',[],'traversable') as $i=>$booksItem): ?>
    <li><?php echo e($this->variable($booksItem,'title',[],'',null,'string')); ?></li>
<?php endforeach; ?>
</ul><?php }} ?>