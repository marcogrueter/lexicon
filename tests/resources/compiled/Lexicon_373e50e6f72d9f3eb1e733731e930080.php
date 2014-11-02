<?php namespace Anomaly\Lexicon\View\Compiled; class Lexicon_373e50e6f72d9f3eb1e733731e930080 { public function render($__data) {
?><ul>
<?php foreach($__data['__env']->variable($__data,'categories',[],'',[],'traversable') as $i=>$categoriesItem): ?>
    <?php foreach($__data['__env']->variable($categoriesItem,'posts',[],'',[],'traversable') as $i=>$postsItem): ?>
    <li><?php echo e($__data['__env']->variable($categoriesItem,'name',[],'',null,'string')); ?> - <?php echo e($__data['__env']->variable($postsItem,'name',[],'',null,'string')); ?></li>
    <?php endforeach; ?>
<?php endforeach; ?>
</ul><?php }} ?>