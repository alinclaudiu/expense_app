<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Dashboard Controller
 *
 */
class DashboardController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Search.Prg', [
            'actions' => ['expenses'],
        ]);
    }

    /**
     * index function
     * @return void
     */
    public function index()
    {
        $recent_requests = $this->Expenses->find()->where(['status' => 1]);
        $recent_approvals = $this->Expenses->find()->where(['status' => 2]);
        $year = date('Y');
        $month = date('m');
        $total = $this->Expenses->totalExpensesForTheYear($year);
        $month = $this->Expenses->totalExpensesForTheMonth($month);
        $this->set('reqs', $this->paginate($recent_requests, [
            'limit' => 5,
            'order' => [
                'Expenses.id' => 'ASC'
            ]
        ]));
        $this->set('approvals', $this->paginate($recent_approvals));
        $this->set('total', $total);
        $this->set('month', $month);
    }
}
