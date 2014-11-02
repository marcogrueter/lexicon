<?php namespace Anomaly\Lexicon\View\Compiled; class Lexicon_525872800941fe2df701dc02dbcd03df { public function render($__data) {
?><?php if($__data['__env']->variable($__data, 'first_name')): ?>
    <h2><?php echo e($__data['__env']->variable($__data,'first_name',[],'',null,'string')); ?></h2>
<?php elseif($__data['__env']->variable($__data, 'last_name')): ?>
    <h2><?php echo e($__data['__env']->variable($__data,'last_name',[],'',null,'string')); ?></h2>
<?php else: ?>
    Nobody.
<?php endif; ?>

<hr/>
<?php if(!($__data['__env']->variable($__data, 'first_name'))): ?>
<h2><?php echo e($__data['__env']->variable($__data,'first_name',[],'',null,'string')); ?></h2>
<?php elseif(!($__data['__env']->variable($__data, 'last_name'))): ?>
<h2><?php echo e($__data['__env']->variable($__data,'last_name',[],'',null,'string')); ?></h2>
<?php else: ?>
Something.
<?php endif; ?>

<hr/>
<?php if($__data['__env']->variable($__data, 'title') or $__data['__env']->booleanTest($__data['__env']->variable($__data, 'var'),$__data['__env']->variable($__data, '5'),'>')): ?>
    <?php echo e($__data['__env']->variable($__data,'title',[],'',null,'string')); ?>
<?php endif; ?>

<?php }} ?>