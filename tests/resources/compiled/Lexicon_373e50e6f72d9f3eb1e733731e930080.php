<?php namespace Anomaly\Lexicon\View\Compiled; class Lexicon_373e50e6f72d9f3eb1e733731e930080 extends \Anomaly\Lexicon\View\CompiledView { public function render($__data) {
?><ul>
<?php foreach($this->variable($__data,'categories',[],'',[],'traversable') as $i=>$categoriesItem): ?>
    <?php foreach($this->variable($categoriesItem,'posts',[],'',[],'traversable') as $i=>$postsItem): ?>
    <li><?php echo e($this->variable($categoriesItem,'name',[],'',null,'string')); ?> - <?php echo e($this->variable($postsItem,'name',[],'',null,'string')); ?></li>
    <?php endforeach; ?>
<?php endforeach; ?>
</ul><?php }} ?>