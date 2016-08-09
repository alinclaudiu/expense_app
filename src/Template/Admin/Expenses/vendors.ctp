<?php
$this->assign('title', __('Vendors'));
$this->Html->addCrumb(__('Vendors'));
$this->loadHelper('Search');

$this->Html->css('//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/css/bootstrap-datepicker3.min.css', ['block' => true]);
$this->Html->script('//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/js/bootstrap-datepicker.min.js', ['block' => true]);
$this->Html->scriptBlock('
    $("#created").datepicker({autoclose: true,todayHighlight: true});
    $("#modified").datepicker({autoclose: true,todayHighlight: true});
', ['block' => true]);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="title">
                        <?=__('Vendors List')?>
                    </div>
                </div>
                <div class="pull-right card-action">
                    <div class="btn-group" role="group" aria-label="...">
                        <?= $this->Html->link('<i class="fa fa-plus"></i>', ['action' => 'add_vendor'], ['class'
                        => 'btn btn-success', 'escape' => false])?>&nbsp;
                        <?= $this->Html->link('<i class="fa fa-refresh"></i>', ['action' => 'index'], ['class' => 'btn btn-default', 'escape' => false])?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="actions"><?=__('Actions')?></th>
                            <th><?=$this->Paginator->sort('id')?></th>
                            <th><?=$this->Paginator->sort('name')?></th>
                            <th><?=$this->Paginator->sort('created')?></th>
                            <th><?=$this->Paginator->sort('updated')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if($vendors->count() == 0):
                        ?>
                            <tr>
                                <td colspan="8" class="text-center"><?= __('No data found')?></td>
                            </tr>
                        <?php endif;?>
                        <?php foreach ($vendors as $expenses_type): ?>
                        <tr>
                            <td>
                                <?=$this->Html->link('<i class="fa fa-search"></i>', ['action' => 'view', $expenses_type->id], ['escape' => false])?>&nbsp;
                                <?=$this->Html->link('<i class="fa fa-edit"></i>', ['action' => 'update_expense_type',
                                $expenses_type->id], ['escape' => false])?>&nbsp;
                                <?=$this->Form->postLink('<i class="fa fa-trash"></i>', ['action' =>
                                'delete_expense_type', $expenses_type->id], ['confirm' => __('Are you sure you want to
                                delete # {0}?', h($expenses_type->name)), 'escape' => false])?>
                            </td>
                            <td><?=$this->Number->format($expenses_type->id)?></td>
                            <td><?=h($expenses_type->name)?></td>
                            <td><?=$expenses_type->created?></td>
                            <td><?=$expenses_type->updated?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <ul class="pagination pagination-sm pull-right">
                    <?=$this->Paginator->prev()?>
                    <?=$this->Paginator->numbers()?>
                    <?=$this->Paginator->next()?>
                </ul>
                <p><?=$this->Paginator->counter()?></p>
            </div>
        </div>
    </div>
</div>
