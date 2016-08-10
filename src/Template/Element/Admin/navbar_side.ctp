<div class="side-menu sidebar-inverse">
    <nav class="navbar navbar-default" role="navigation">
        <div class="side-menu-container">
            <div class="navbar-header">
                <?= $this->Html->link('<div class="icon fa fa-paper-plane"></div><div class="title">' . h(App\Core\Setting::read('App.Name')) . '</div>', ['controller' => 'Dashboard', 'action' => 'index'], ['escape' => false, 'class' => 'navbar-brand'])?>
                <button type="button" class="navbar-expand-toggle pull-right visible-xs">
                    <i class="fa fa-times icon"></i>
                </button>
            </div>
            <ul class="nav navbar-nav">
            <?php
                echo $this->Menu->link('<span class="icon fa fa-tachometer"></span><span class="title">' . __('Dashboard') . '</span>', 
                    ['controller' => 'Dashboard', 'action' => 'index'],
                    ['escape' => false]
                );
                echo $this->Menu->groupLink('<span class="icon fa fa-gears"></span><span class="title">' .
                __('Parameters') . '</span>', [
                        [__('Users'), ['admin'=>true, 'controller' => 'Users', 'action' => 'index']],
                        [__('Settings'), ['admin'=>true,'controller' => 'Setting', 'action' => 'index']],
                        [__('Expenses Types'), ['admin'=>true,'controller' => 'Expenses', 'action' => 'expenses_types']],
                        [__('Vendors'), ['admin'=>true,'controller' => 'Expenses', 'action' => 'vendors']],
                ]
                );
                echo $this->Menu->groupLink('<span class="icon fa fa-gears"></span><span class="title">' .
                __('Expenses Management') . '</span>', [
                [__('Add An Expense'), ['admin'=>true,'controller' => 'Expenses', 'action' => 'add_expense']],
                [__('View All Expenses'), ['admin'=>true,'controller' => 'Expenses', 'action' => 'expenses']],
                [__('View Pending Expenses'), ['admin'=>true,'controller' => 'Expenses', 'action' => 'expenses']],
                [__('View Approved Expenses'), ['admin'=>true,'controller' => 'Expenses', 'action' => 'expenses']],
                [__('View Rejected Expenses'), ['admin'=>true,'controller' => 'Expenses', 'action' => 'expenses']],
                ]
                );
                echo $this->Menu->groupLink('<span class="icon fa fa-gears"></span><span class="title">' .
                __('Profile Management') . '</span>', [
                [__('Manage My Profile'), ['admin'=>true,'controller' => 'Users', 'action' => 'profile']],
                [__('Logout'), ['admin'=>true,'controller' => 'Users', 'action' => 'logout']],
                ]
                );
                // $navbar_sides = App\Core\Setting::read('Menu.Side');
                // foreach ($navbar_sides as $k => $v) {
                //     if (isset($v[1][0]) && is_array($v[1][0])) {
                //         echo $this->Menu->groupLink($v[0], $v[1], ['escape' => false]);
                //     } else {
                //         echo $this->Menu->link($v[0], $v[1], ['escape' => false]);
                //     }
                // }
            ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>
</div>
