<?php namespace Anomaly\Lexicon\View\Compiled; class Lexicon_e77d3b51bf1a7b7c2e3d9e2fec6b7700 { public function render($__data) {
?>
    These tags will remain unparsed.
    {{ tag1 }}{{ tag2 }}{{ tag3 }}
<?php foreach($__data['__env']->variable($__data,'ignore',[],'',[],'traversable') as $i=>$ignoreItem): ?>
    These tags will remain unparsed.
    <?php echo e($__data['__env']->variable($ignoreItem,'tag1',[],'',null,'string')); ?><?php echo e($__data['__env']->variable($ignoreItem,'tag2',[],'',null,'string')); ?><?php echo e($__data['__env']->variable($ignoreItem,'tag3',[],'',null,'string')); ?>
<?php endforeach; ?>

{{ single }}<?php }} ?>