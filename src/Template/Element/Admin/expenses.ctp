<?php
$this->assign('title', __('Expenses'));
$this->Html->addCrumb(__('Expenses'));
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
                        <?= $this->Html->link('<i class="fa fa-plus"></i>', ['action' => 'add_expense'], ['class'
                        => 'btn btn-success', 'escape' => false])?>&nbsp;
                        <?= $this->Html->link('<i class="fa fa-refresh"></i>', ['action' => 'expenses'], ['class' =>
                        'btn btn-default', 'escape' => false])?>
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
                        <th><?=$this->Paginator->sort('type')?></th>
                        <th><?=$this->Paginator->sort('amount')?></th>
                        <th><?=$this->Paginator->sort('vendor')?></th>
                        <th><?=$this->Paginator->sort('reference no')?></th>
                        <th><?=$this->Paginator->sort('expenses date')?></th>
                        <th><?=$this->Paginator->sort('created')?></th>
                        <th><?=$this->Paginator->sort('status')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?= $this->Search->generate([
                    ['search', ['type' => 'submit', 'class' => 'btn btn-primary', 'style' => 'margin: 0px']],
                    ['id', ['placeholder' => __('id =')]],
                    ['name', ['placeholder' => __('Expenses Title')]],
                    ['expenses_type_id', ['empty' => __('All'), 'options' => $expenses_type]],
                    ['amount', ['placeholder' => __('Expenses Amount')]],
                    ['vendor_id', ['empty' => __('All'), 'options' => $vendors]],
                    ['reference_no', ['placeholder' => __('Reference No')]],
                    ['expense_date', ['placeholder' => __('Expenses Date')]],
                    ['created', ['placeholder' => __('created >=')]],
                    ['status', ['empty' => __('All'), 'options' => ['1' => __('Approved'), '0' => __('Pending'), '2'
                    => __('Rejected')]]], ])?>
                    <?php
                        if($expenses->count() == 0):
                    ?>
                    <tr>
                        <td colspan="8" class="text-center"><?= __('No data found')?></td>
                    </tr>
                    <?php endif;?>
                    <?php foreach ($expenses as $expenses_type): ?>
                    <tr>
                        <td>
                            <?=$this->Html->link('<i class="fa fa-search"></i>', ['action' => 'view_expense',
                            $expenses_type->id], ['escape' => false])?>&nbsp;
                            <?=$this->Html->link('<i class="fa fa-edit"></i>', ['action' => 'update_expense',
                            $expenses_type->id], ['escape' => false])?>&nbsp;
                        </td>
                        <td><?=$this->Number->format($expenses_type->id)?></td>
                        <td><?=h($expenses_type->name)?></td>
                        <td><?=$expenses_type->has('expenses_type') ?
                            $this->Html->link(h($expenses_type->expenses_type->name),
                            ['prefix' =>
                            'admin', 'controller' => 'Expenses', 'action' => 'expenses_types']) : ''?>
                        </td>
                        <td><?=$this->Number->format($expenses_type->amount)?></td>
                        <td><?=$expenses_type->has('vendor') ? $this->Html->link(h($expenses_type->vendor->name),
                            ['prefix' =>
                            'admin', 'controller' => 'Expenses', 'action' => 'vendors']) : ''?>
                        </td>
                        <td><?=$expenses_type->reference_no?></td>
                        <td><?=h($expenses_type->expense_date)?></td>
                        <td><?=h($expenses_type->created)?></td>
                        <td><?=h($expenses_type->status)?></td>
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
