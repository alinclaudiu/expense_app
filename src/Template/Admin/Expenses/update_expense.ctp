<?php
$this->assign('title', __('Expenses/Add Expense'));
$this->Html->addCrumb(__('Expenses'), ['controller' => 'Expenses', 'action' => 'expenses']);
$this->Html->addCrumb(__('Add Expense'));
?>
<div class="row">
    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-xs-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="title"><?= __('Add Expense') ?></div>
                </div>
            </div>
            <div class="card-body">
                <?= $this->Form->create($expense, ['templates' => 'template_form_1_column']) ?>
                    <?php
                        echo $this->Form->input('expenses_type_id', ['options' => $types]);
                        echo $this->Form->input('name');
                        echo $this->Form->input('reference_no', array('readonly'=>'readonly'));
                        echo $this->Form->input('description');
                        echo $this->Form->input('vendor_id');
                        echo $this->Form->input('expense_date');
                        echo $this->Form->input('amount');
                        echo $this->Form->input('id');
                    ?>
                <?= $this->Form->button(__('Add Expense'), ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
