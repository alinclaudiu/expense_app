<?php
$this->assign('title', __('Expenses/Add Vendor'));
$this->Html->addCrumb(__('Expenses'), ['controller' => 'Expenses', 'action' => 'list_expenses']);
$this->Html->addCrumb(__('Add Vendor'));
?>
<div class="row">
    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-xs-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="title"><?= __('Add Vendor') ?></div>
                </div>
            </div>
            <div class="card-body">
                <?= $this->Form->create($vendors, ['templates' => 'template_form_1_column']) ?>
                    <?php
                        echo $this->Form->input('name');
                    ?>
                <?= $this->Form->button(__('Add Vendor'), ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
