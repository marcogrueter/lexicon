<?php namespace Anomaly\Lexicon\View\Compiled; class Lexicon_be9fd3a6e7452168904180634a75ecd2 extends \Anomaly\Lexicon\View\CompiledView { public function render($__data) {
?><ul>
    <?php foreach($this->variable($__data,'messages.success',[],'',[],'traversable') as $i=>$messagesSuccessItem): ?>
    <li><?php echo e($this->variable($messagesSuccessItem,'message',[],'',null,'string')); ?></li>
    <?php endforeach; ?>
</ul>
<ul>
    <?php foreach($this->variable($__data,'messages.error',[],'',[],'traversable') as $i=>$messagesErrorItem): ?>
    <li><?php echo e($this->variable($messagesErrorItem,'message',[],'',null,'string')); ?></li>
    <?php endforeach; ?>
</ul><?php }} ?>