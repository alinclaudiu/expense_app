<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Core\Setting;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class ExpensesController extends AppController
{

    /**
     * initialize
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Search.Prg', [
            'actions' => ['expenses'],
        ]);
    }

    /**
     * Index method
     *
     * @return void
     */
    public function expensesTypes()
    {
        $expenses_types = $this->paginate('ExpensesTypes');
        $this->set(compact('expenses_types'));
        $this->set('_serialize', ['expenses_types']);
    }

    public function vendors()
    {
        $vendors = $this->paginate('Vendors');
        $this->set(compact('vendors'));
        $this->set('_serialize', ['vendors']);
    }

    public function expenses()
    {
        $this->paginate = [
            'contain' => ['Vendors', 'ExpensesTypes'],
        ];
        $expenses = $this->paginate($this->Expenses->find('search', $this->Expenses->filterParams($this->request->query)));
        $vendors = $this->Expenses->Vendors->find('list');
        $expenses_type = $this->Expenses->ExpensesTypes->find('list');
        $this->set(compact('expenses', 'vendors', 'expenses_type'));
        $this->set('_serialize', ['expenses']);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $expense = $this->Expenses->get($id, ['contain' => ['Vendors', 'ExpensesTypes', 'Users']]);
        $this->set('expense', $expense);
        $this->set('_serialize', ['expense']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|void Redirects on successful add, renders view otherwise.
     */
    public function addExpense()
    {
        $user_role = $this->Auth->user('role_id');
        $permission = $this->checkUserPermission($user_role, 2, 'C');
        $expense = $this->Expenses->newEntity();
        if ($permission === true) {
            if ($this->request->is('post')) {
                $this->request->data['user_id'] = $this->Auth->user('id');
                $type = $this->request->data['expenses_type_id'];
                $title = $this->request->data['name'];
                $desc = $this->request->data['description'];
                $amt = $this->request->data['amount'];
                $exp_date = $this->request->data['expense_date'];
                $vendor = $this->request->data['vendor_id'];
                $expense = $this->Expenses->patchEntity($expense, $this->request->data);
                $saved_expense = $this->Expenses->save($expense);
                $admin_users = $this->Users->getAdminUsers();
                if ($saved_expense) {
                    $log_details = $this->Auth->user('full_name').' added a new expense '.$title.'. on '.date('Y-m-d H:i:s');
                    $resource_id = 2;
                    $this->writeLog($resource_id, $log_details);
                    $vendor_name = $this->Vendors->getVendor($vendor);
                    $type = $this->ExpensesTypes->getType($type);
                    $msg = "Dear Admin,\n\n";
                    $msg .= "A new expense request has been made and requires your approval. Please find details
                    below:\n\n";
                    $msg .= "Request Made By: " . $this->Auth->user('full_name') . "\n";
                    $msg .= "Request Type: " . $type['name'] . "\n";
                    $msg .= "Request Title: " . $title . "\n";
                    $msg .= "Request Description: " . $desc . "\n";
                    $msg .= "Request Amount: =N=" . number_format($amt, 2) . "\n";
                    $msg .= "Request Vendor: " . $vendor_name['name'] . "\n";
                    $msg .= "Request Date: " . $exp_date . "\n\n";
                    $msg .= "Best Regards \n";
                    $msg .= "My Expenses App!";
                    foreach ($admin_users as $admin) {
                        $this->sendNotification($admin['email'], 'New Expense Request', $msg);
                    }
                    $this->Flash->success(__('The expenses has been saved.'));
                    return $this->redirect(['action' => 'expenses']);
                } else {
                    $this->Flash->error(__('The expenses type could not be saved. Please, try again.'));
                }
            }
        } else {
            $this->Flash->success(__('Sorry you are not allowed to use that resource!.'));
            return $this->redirect(['action' => 'expenses_types']);
        }
        $types = $this->Expenses->ExpensesTypes->find('list');
        $vendors = $this->Expenses->Vendors->find('list');
        $ref_no = $this->portalNo(10);
        $this->set(compact('expense', 'types', 'vendors', 'ref_no'));
        $this->set('_serialize', ['expenses']);
    }

    public function updateExpense($id = null)
    {
        $expense = $this->Expenses->get($id);
        $status = $expense['status'];
        if ($status == 1) {
            if ($this->request->is(['patch', 'post', 'put'])) {
                $this->request->data['ExpensesTypes']['updated'] = date('Y-m-d H:i:s');
                $expense = $this->Expenses->patchEntity($expense, $this->request->data);
                if ($this->Expenses->save($expense)) {
                    $log_details = $this->Auth->user('full_name').' updated expense '.$expense['name'].'. on '.date('Y-m-d
                H:i:s');
                    $resource_id = 2;
                    $this->writeLog($resource_id, $log_details);
                    $this->Flash->success(__('The Expense type has been updated.'));
                    return $this->redirect(['action' => 'expenses']);
                } else {
                    $this->Flash->error(__('The Expense could not be saved. Please, try again.'));
                }
            }
        }else{
            $this->Flash->error(__('Sorry! you cannot update this expense again.'));
            return $this->redirect(['action' => 'expenses']);
        }
        $types = $this->Expenses->ExpensesTypes->find('list');
        $vendors = $this->Expenses->Vendors->find('list');
        $this->set(compact('expense', 'types', 'vendors'));
        $this->set('_serialize', ['expense']);
    }


    public function addExpenseType()
    {
        $user_role = $this->Auth->user('role_id');
        $permission = $this->checkUserPermission($user_role, 3, 'C');
        if ($permission === true) {
            $expense = $this->ExpensesTypes->newEntity();
            if ($this->request->is('post')) {
                $expense = $this->ExpensesTypes->patchEntity($expense, $this->request->data);
                if ($this->ExpensesTypes->save($expense)) {
                    $log_details = $this->Auth->user('full_name').' Added expense type '.$this->request->data['name'].'. on '.date
                        ('Y-m-d
                H:i:s');
                    $resource_id = 2;
                    $this->writeLog($resource_id, $log_details);
                    $this->Flash->success(__('The expenses type has been saved.'));
                    return $this->redirect(['action' => 'expenses_types']);
                } else {
                    $this->Flash->error(__('The expenses type could not be saved. Please, try again.'));
                }
            }
            $this->set(compact('expense'));
            $this->set('_serialize', ['expenses']);
        } else {
            $this->Flash->success(__('Sorry you are not allowed to use that resource!.'));
            return $this->redirect(['action' => 'expenses_types']);
        }
    }

    public function addVendor()
    {
        $user_role = $this->Auth->user('role_id');
        $permission = $this->checkUserPermission($user_role, 4, 'C');
        if ($permission === true) {
            $vendors = $this->Vendors->newEntity();
            if ($this->request->is('post')) {
                $vendors = $this->Vendors->patchEntity($vendors, $this->request->data);
                if ($this->Vendors->save($vendors)) {
                    $log_details = $this->Auth->user('full_name').' Added vendor '.$this->request->data['name'].'. on '
                        .date
                        ('Y-m-d
                H:i:s');
                    $resource_id = 2;
                    $this->writeLog($resource_id, $log_details);
                    $this->Flash->success(__('The vendor has been saved.'));
                    return $this->redirect(['action' => 'vendors']);
                } else {
                    $this->Flash->error(__('The Vendors could not be saved. Please, try again.'));
                }
            }
            $this->set(compact('vendors'));
            $this->set('_serialize', ['vendors']);
        } else {
            $this->Flash->success(__('Sorry you are not allowed to use that resource!.'));
            return $this->redirect(['action' => 'vendors']);
        }
    }

    public function changeStatus($id, $code)
    {
        if (!empty($id) && !empty($code)) {
            $expense = $this->Expenses->get($id);
            $this->request->data['Expenses']['id'] = $id;
            $this->request->data['Expenses']['status'] = $code;
            $expense = $this->Expenses->patchEntity($expense, $this->request->data);
            $user = $expense['user_id'];
            $email = $this->Users->find()->where('id', $user)->first();
            $msg_success = "Dear " . $email['full_name'] . ",\n\n";
            $msg_success .= "The expense, " . $expense['code'] . " with the title " . $expense['name'] . "\n";
            $msg_success .= "Has being approved\n\n";
            $msg_success .= "Best Regards \n";
            $msg_success .= "My Expenses App!";

            $msg_fail = "Dear " . $email['full_name'] . ",\n\n";
            $msg_fail .= "The expense, " . $expense['code'] . " with the title " . $expense['name'] . "\n";
            $msg_fail .= "Has being rejected \n\n";
            $msg_fail .= "Best Regards \n";
            $msg_fail .= "My Expenses App!";

            if ($this->Expenses->save($expense)) {
                switch ($code) {
                    case 2;
                        $this->sendNotification($email['email'], 'Expense Request Status Change', $msg_success);
                        $log_details = $this->Auth->user('full_name').' Updated expenses status of '
                            .$expense['name'].' to APPROVED on '.date
                            ('Y-m-d H:i:s');
                        $resource_id = 2;
                        $this->writeLog($resource_id, $log_details);
                        $this->Flash->success(__('The Expense has been approved.'));
                        break;
                    case 3;
                        $log_details = $this->Auth->user('full_name').' Updated expenses status of '
                            .$expense['name'].' to REJECTED on '.date
                            ('Y-m-d H:i:s');
                        $resource_id = 2;
                        $this->writeLog($resource_id, $log_details);
                        $this->sendNotification($email, 'Expense Request Status Change', $msg_fail);
                        $this->Flash->error(__('The Expense has been rejected.'));
                        break;
                    default;
                        $this->Flash->error(__('The Expense is still pending. Please try again!'));
                }
                return $this->redirect(['action' => 'expenses']);
            }
        }
    }

    public function updateExpenseType($id = null)
    {
        $expense = $this->ExpensesTypes->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $this->request->data['ExpensesTypes']['updated'] = date('Y-m-d H:i:s');
            $expense = $this->ExpensesTypes->patchEntity($expense, $this->request->data);
            if ($this->ExpensesTypes->save($expense)) {
                $this->Flash->success(__('The Expense type has been saved.'));
                return $this->redirect(['action' => 'expenses_types']);
            } else {
                $this->Flash->error(__('The Expense could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('expense'));
        $this->set('_serialize', ['expense']);
    }

    public function updateVendor($id = null)
    {
        $vendors = $this->Vendors->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $this->request->data['Vendors']['updated'] = date('Y-m-d H:i:s');
            $vendors = $this->Vendors->patchEntity($vendors, $this->request->data);
            if ($this->Vendors->save($vendors)) {
                $this->Flash->success(__('The vendor has been saved.'));
                return $this->redirect(['action' => 'vendors']);
            } else {
                $this->Flash->error(__('The vendors could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('vendors'));
        $this->set('_serialize', ['vendors']);
    }

    public function deleteExpenseType($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->ExpensesTypes->get($id);
        if ($this->ExpensesTypes->delete($user)) {
            $this->Flash->success(__('The expenses type has been deleted.'));
        } else {
            $this->Flash->error(__('The expenses type could not be deleted. Please, try again.'));
        }
        return $this->redirect($this->referer());
    }
}
