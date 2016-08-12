<?php
$this->assign('title', __('Dashboard'));
?>
<div class="row">
    <div class="col-sm-10">
        <div class="panel panel-default">
            <div class="panel-body">
                Welcome! Today's date is <?php echo date('Y-m-d H:i:s'); ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Recent Approvals</h3>
            </div>
            <div class="panel-body">
                <?= $this->element('Admin/approved')?>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Recent Requests</h3>
            </div>
            <div class="panel-body">
                <?= $this->element('Admin/recent')?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Total Expenses for the Month to Date</h3>
            </div>
            <div class="panel-body">
                <h3 class="panel-title">&#8358;<?= number_format($month, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Total Expenses for the Year to Date</h3>
            </div>
            <div class="panel-body">
                <p style="align-content: center">
                    <h3 class="panel-title">&#8358;<?= number_format($total, 2); ?></h3>
                </p>
            </div>
        </div>
    </div>
</div>
