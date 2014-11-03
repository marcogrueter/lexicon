<?php namespace Anomaly\Lexicon\View\Compiled; class Lexicon_9eca16b6c92de01da39204f9b2c26cae extends \Anomaly\Lexicon\View\CompiledView { public function render($__data) {
?><?php $this->view()->startSection('sidebar'); ?>
<div class="sidebar">This is some sidebar content.</div>
<?php echo $this->view()->yieldSection(); ?>
<?php echo $this->view()->yieldContent('content'); ?><?php }} ?>