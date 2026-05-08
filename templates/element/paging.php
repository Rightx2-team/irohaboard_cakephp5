<?php
$canPaginate = false;
try {
    $this->Paginator->params();
    $canPaginate = true;
} catch (\Throwable $e) {
    $canPaginate = false;
}

if ($canPaginate): ?>
<div class="text-center">
<?= $this->Paginator->counter(__('合計') . ' : {{count}}' . __('件') . '　{{page}} / {{pages}}' . __('ページ')); ?>
</div>
<div class="text-center">
<ul class="pagination" style="margin:4px 0;">
<?php
$this->Paginator->setTemplates([
    'paginationWrapper' => '{{pages}}',
    'number'   => '<li><a href="{{url}}">{{text}}</a></li>',
    'current'  => '<li class="active"><a href="{{url}}">{{text}}</a></li>',
    'ellipsis' => '<li class="disabled"><span>…</span></li>',
    'prevActive'   => '<li><a href="{{url}}">&laquo;</a></li>',
    'prevDisabled' => '<li class="disabled"><span>&laquo;</span></li>',
    'nextActive'   => '<li><a href="{{url}}">&raquo;</a></li>',
    'nextDisabled' => '<li class="disabled"><span>&raquo;</span></li>',
]);
echo $this->Paginator->prev();
echo $this->Paginator->numbers();
echo $this->Paginator->next();
?>
</ul>
</div>
<?php endif; ?>
