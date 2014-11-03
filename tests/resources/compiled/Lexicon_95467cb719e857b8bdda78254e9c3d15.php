<?php namespace Anomaly\Lexicon\View\Compiled; class Lexicon_95467cb719e857b8bdda78254e9c3d15 extends \Anomaly\Lexicon\View\CompiledView { public function render($__data) {
?><?php $this->view()->startSection('content'); ?>
<div class="content">Injecting this content into the yield section.</div>
<?php $this->view()->stopSection(); ?>
<?php echo $this->view()->make('test::layout',$__data)->render(); ?><?php }} ?>