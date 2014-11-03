<?php namespace Anomaly\Lexicon\View\Compiled; class Lexicon_525872800941fe2df701dc02dbcd03df extends \Anomaly\Lexicon\View\CompiledView { public function render($__data) {
?><?php if($this->variable($__data, 'first_name')): ?>
    <h2><?php echo e($this->variable($__data,'first_name',[],'',null,'string')); ?></h2>
<?php elseif($this->variable($__data, 'last_name')): ?>
    <h2><?php echo e($this->variable($__data,'last_name',[],'',null,'string')); ?></h2>
<?php else: ?>
    Nobody.
<?php endif; ?>

<hr/>
<?php if(!($this->variable($__data, 'first_name'))): ?>
<h2><?php echo e($this->variable($__data,'first_name',[],'',null,'string')); ?></h2>
<?php elseif(!($this->variable($__data, 'last_name'))): ?>
<h2><?php echo e($this->variable($__data,'last_name',[],'',null,'string')); ?></h2>
<?php else: ?>
Something.
<?php endif; ?>

<hr/>
<?php if($this->variable($__data, 'title') or $this->booleanTest($this->variable($__data, 'var'),$this->variable($__data, '5'),'>')): ?>
    <?php echo e($this->variable($__data,'title',[],'',null,'string')); ?>
<?php endif; ?>

<?php }} ?>