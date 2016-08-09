<?php
$this->assign('title', __('Users/Add'));
$this->Html->addCrumb(__('Users'), ['controller' => 'Users', 'action' => 'index']);
$this->Html->addCrumb(__('Add'));
?>
<div class="row">
    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-xs-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="title"><?= __('Add User') ?></div>
                </div>
            </div>
            <div class="card-body">
                <?= $this->Form->create($user, ['templates' => 'template_form_1_column']) ?>
                    <?php
                        echo $this->Form->input('expense_type_id', ['options' => $types]);
                        echo $this->Form->input('name');
                        echo $this->Form->input('description');
                        echo $this->Form->input('date');
                        echo $this->Form->input('vendor_id');
                        echo $this->Form->input('expense_date');
                    ?>
                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
