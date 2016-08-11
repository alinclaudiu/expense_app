<?php
$this->assign('title', __('Expense Approval Request'));
?>
<table>
    <tr>
        <td>
            <p><?= __('Hello,')?></p>
            <h1><?= __('A new expense request has been made and requires your approval. Please find details
                    below:')?></h1>
            <ol>
                <ul>
                    <li>Request Made by: <?= $msg['user'] ?></li>
                    <li>Expense Title: <?= $msg['title'] ?></li>
                    <li>Expense Type: <?= $msg['type'] ?></li>
                    <li>Expense Description: <?= $msg['desc'] ?></li>
                    <li>Expense Amount: <?= $msg['amt'] ?></li>
                    <li>Expense Vendor: <?= $msg['vendor'] ?></li>
                    <li>Expense Date: <?= $msg['date'] ?></li>
                </ul>
            </ol>
            <p><?= __('Thanks')?></p>
            <!-- button -->

            <!-- /button -->
        </td>
    </tr>
</table>
