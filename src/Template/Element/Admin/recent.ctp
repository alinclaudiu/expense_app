<table class="table">
    <?
        $i = 1;
        if($reqs->count() == 0){
    ?>
    <tr>
        <td colspan="8" class="text-center"><?= __('No recent requests')?></td>
    </tr>
    <?
        }else{
    ?>
    <tr class="thead-inverse">
        <td>S/N</td>
        <td>Title</td>
        <td>Amount</td>
        <td>Date</td>
    </tr>
    <?
                foreach($reqs as $req){
    ?>
    <tr>
        <td><?= $i++ ?></td>
        <td><?= $req->name ?></td>
        <td>&#8358;<?= number_format($req->amount, 2) ?></td>
        <td><?= $req->created ?></td>
    </tr>
    <?
        }}
    ?>
</table>