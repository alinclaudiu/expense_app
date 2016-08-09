<?php
use App\Core\Setting;

$this->assign('title', __('Users/Profile'));
$this->Html->addCrumb(__('Users'), ['controller' => 'Users', 'action' => 'index']);
$this->Html->addCrumb(__('Profile'));
?>
<div class="row">
    <div class="col-lg-8 col-lg-offset-3 col-md-10 col-lg-offset-2 col-xs-12">
        <div class="card summary-inline">
            <div role="tabpanel">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <?= $this->Html->link(__('Profile'), '#!')?>
                    </li>
                    <li role="presentation">
                        <?= $this->Html->link(__('Change password'), ['action' => 'changePassword'])?>
                    </li>
                    <?php
                    if (Setting::read('Member.AnyoneCanDeactive')) {
                        echo '<li role="presentation">';
                        echo $this->Form->postLink(__('Deactive account'), ['action' => 'deactive'], ['confirm' => __('Are you sure you want to deactive your account?'), 'style' => 'background: #DC2A26; color: #fff']);
                        echo '</li>';
                    }
                    ?>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active card-body">
                        <?php
                            echo $this->Form->create($user, ['templates' => 'template_form_1_column']);
                            echo $this->Form->input('id', ['type' => 'hidden']);
                            echo $this->Form->input('email',['readonly' => true]);
                            echo $this->Form->input('full_name', ['autofocus' => true]);
                            echo $this->Form->button(__('Save'), ['class' => 'btn btn-primary', 'type' => 'submit']);
                            echo $this->Form->end();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /End card -->
</div>