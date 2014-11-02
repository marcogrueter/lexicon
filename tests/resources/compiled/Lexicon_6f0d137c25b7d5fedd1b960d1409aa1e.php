<?php namespace Anomaly\Lexicon\View\Compiled; class Lexicon_6f0d137c25b7d5fedd1b960d1409aa1e { public function render($__data) {
?><ul>
<?php foreach($__data['__env']->variable($__data,'books',[],'',[],'traversable') as $i=>$booksItem): ?>
    <li><?php echo e($__data['__env']->variable($booksItem,'title',[],'',null,'string')); ?></li>
<?php endforeach; ?>
</ul><?php }} ?>