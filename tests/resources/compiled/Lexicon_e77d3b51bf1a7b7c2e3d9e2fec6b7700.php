<?php namespace Anomaly\Lexicon\View\Compiled; class Lexicon_e77d3b51bf1a7b7c2e3d9e2fec6b7700 extends \Anomaly\Lexicon\View\CompiledView { public function render($__data) {
?>
    These tags will remain unparsed.
    {{ tag1 }}{{ tag2 }}{{ tag3 }}
<?php foreach($this->variable($__data,'ignore',[],'',[],'traversable') as $i=>$ignoreItem): ?>
    These tags will remain unparsed.
    <?php echo e($this->variable($ignoreItem,'tag1',[],'',null,'string')); ?><?php echo e($this->variable($ignoreItem,'tag2',[],'',null,'string')); ?><?php echo e($this->variable($ignoreItem,'tag3',[],'',null,'string')); ?>
<?php endforeach; ?>

{{ single }}<?php }} ?>