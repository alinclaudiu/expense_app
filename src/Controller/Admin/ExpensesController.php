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
        $this->loadModel('Expenses');
        $this->loadModel('ExpensesTypes');
        $this->loadModel('Vendors');
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
            'contain' => ['Vendors','ExpensesTypes'],
        ];
        $expenses = $this->paginate($this->Expenses->find('search', $this->Expenses->filterParams($this->request->query)));
        $vendors = $this->Expenses->Vendors->find('list');
        $expenses_type = $this->Expenses->ExpensesTypes->find('list');
        $this->set(compact('expenses','vendors','expenses_type'));
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
        if($permission === true){
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
                    $vendor_name = $this->Vendors->getVendor($vendor);
                    $type = $this->ExpensesTypes->getType($type);
                    $msg = array(
                        'type' => $type['name'],
                        'title' => $title,
                        'desc' => $desc,
                        'amt' => $amt,
                        'vendor' => $vendor_name['name'],
                        'date' => $exp_date,
                        'user' => $this->Auth->user('full_name')
                    );
                    foreach($admin_users as $admin){
                        $this->sendNotification($admin['email'], 'New Expense Request', $msg, 'approved');
                    }
                    $this->Flash->success(__('The expenses has been saved.'));
                    return $this->redirect(['action' => 'expenses']);
                } else {
                    $this->Flash->error(__('The expenses type could not be saved. Please, try again.'));
                }
            }
        }else{
            $this->Flash->success(__('Sorry you are not allowed to use that resource!.'));
            return $this->redirect(['action' => 'expenses_types']);
        }
        $types = $this->Expenses->ExpensesTypes->find('list');
        $vendors = $this->Expenses->Vendors->find('list');
        $ref_no = $this->portalNo(10);
        $this->set(compact('expense','types','vendors','ref_no'));
        $this->set('_serialize', ['expenses']);
    }

    public function updateExpense($id = null)
    {
        $expense = $this->Expenses->get($id);
        $status = $expense['status'];
        if($status !== 1 || $status !== 3){
                $this->Flash->error(__('Sorry! you cannot update this expense again.'));
                return $this->redirect(['action' => 'expenses']);
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $this->request->data['ExpensesTypes']['updated'] = date('Y-m-d H:i:s');
            $expense = $this->Expenses->patchEntity($expense, $this->request->data);
            if ($this->Expenses->save($expense)) {
                $this->Flash->success(__('The Expense type has been updated.'));
                return $this->redirect(['action' => 'expenses']);
            } else {
                $this->Flash->error(__('The Expense could not be saved. Please, try again.'));
            }
        }
        $types = $this->Expenses->ExpensesTypes->find('list');
        $vendors = $this->Expenses->Vendors->find('list');
        $this->set(compact('expense','types','vendors'));
        $this->set('_serialize', ['expense']);
    }


    public function addExpenseType()
    {
        $user_role = $this->Auth->user('role_id');
        $permission = $this->checkUserPermission($user_role, 3, 'C');
        if($permission === true){
            $expense = $this->ExpensesTypes->newEntity();
            if ($this->request->is('post')) {
                $expense = $this->ExpensesTypes->patchEntity($expense, $this->request->data);
                if ($this->ExpensesTypes->save($expense)) {
                    $this->Flash->success(__('The expenses type has been saved.'));
                    return $this->redirect(['action' => 'expenses_types']);
                } else {
                    $this->Flash->error(__('The expenses type could not be saved. Please, try again.'));
                }
            }
            $this->set(compact('expense'));
            $this->set('_serialize', ['expenses']);
        }else{
            $this->Flash->success(__('Sorry you are not allowed to use that resource!.'));
            return $this->redirect(['action' => 'expenses_types']);
        }
    }

    public function addVendor()
    {
        $user_role = $this->Auth->user('role_id');
        $permission = $this->checkUserPermission($user_role, 4, 'C');
        if($permission === true){
            $vendors = $this->Vendors->newEntity();
            if ($this->request->is('post')) {
                $vendors = $this->Vendors->patchEntity($vendors, $this->request->data);
                if ($this->Vendors->save($vendors)) {
                    $this->Flash->success(__('The vendor has been saved.'));
                    return $this->redirect(['action' => 'vendors']);
                } else {
                    $this->Flash->error(__('The Vendors could not be saved. Please, try again.'));
                }
            }
            $this->set(compact('vendors'));
            $this->set('_serialize', ['vendors']);
        }else{
            $this->Flash->success(__('Sorry you are not allowed to use that resource!.'));
            return $this->redirect(['action' => 'vendors']);
        }
    }

    public function changeStatus($id, $code){
        if(!empty($id) && !empty($code)){
            $expense = $this->Expenses->get($id);
            $this->request->data['Expenses']['id'] = $id;
            $this->request->data['Expenses']['status'] = $code;
            $expense = $this->Expenses->patchEntity($expense, $this->request->data);
            $msg_success = 'Hello,'.'<br/>';
            $msg_success .= 'The expense, '.$expense['code'].' with the title '.$expense['name'];'<br/>';
            $msg_success .= 'Has being approved.';
            $msg_fail = 'Hello,'.'<br/>';
            $msg_fail .= 'The expense, '.$expense['code'].' with the title '.$expense['name'];'<br/>';
            $msg_fail .= 'Has being rejected.';
            $user = $expense['user_id'];
            $email = $this->Users->findById($user);
            if($this->Expenses->save($expense)){
                switch($code){
                    case 2;
                        $this->sendNotification($email, 'Expense Request Status Change', $msg_success);
                        $this->Flash->success(__('The Expense has been approved.'));
                    break;
                    case 3;
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

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, ['contain' => ['Roles']]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->data);
            if ($this->Users->save($user)) {
                if ($id == $this->Auth->user('id')) {
                    $userSession = $this->Users->get($user->id)->toArray();
                    unset($userSession['password']);
                    $this->Auth->setUser($userSession);
                }
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            }
        }
        $roles = $this->Users->Roles->find('list');
        $this->set(compact('user', 'roles'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to referer
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect($this->referer());
    }

    /**
     * Toggle method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to referer
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function toggle($id = null)
    {
        $this->request->allowMethod(['post', 'put']);
        if ($this->Auth->user('id') == $id) {
            $this->Flash->error(__('Cannot change status your self.'));

            return $this->redirect($this->referer());
        }
        $user = $this->Users->get($id);
        $statusText = $user->status ? __('Disabled') : __('Activated');
        $user->status = !$user->status;
        if ($this->Users->save($user)) {
            $this->Flash->success(__('The user has been changed status to {0}.', $statusText));
        } else {
            $this->Flash->error(__('The user could not change status to {0}. Please, try again.', $statusText));
        }

        return $this->redirect($this->referer());
    }

    /**
     * Profile method
     *
     * @return \Cake\Network\Response|null Redirects to referer
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function profile()
    {
        $user = $this->Users->get($this->Auth->user('id'), [
            'contain' => ['Roles'],
        ]);
        unset($user->password);
        if ($this->request->is(['post', 'put', 'patch'])) {
            $allowedToChange = ['full_name'];
            $data = array_intersect_key($this->request->data, array_flip($allowedToChange));
            $user = $this->Users->patchEntity($user, $data, ['validate' => 'EditProfile']);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Your profile has been updated'));
                $userSession = $this->Users->get($user->id)->toArray();
                unset($userSession['password']);
                $this->Auth->setUser($userSession);

                return $this->redirect([]);
            } else {
                $this->Flash->error(__('The profile could not update. Please, try again.'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * changePassword method
     *
     * @return \Cake\Network\Response|null Redirects
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function changePassword()
    {
        $user = $this->Users->get($this->Auth->user('id'), [
            'contain' => ['Roles'],
        ]);
        unset($user->password);
        if ($this->request->is(['post', 'put', 'patch'])) {
            $allowedToChange = ['password', 'current_password', 're_password'];
            $data = array_intersect_key($this->request->data, array_flip($allowedToChange));
            $user = $this->Users->patchEntity($user, $data, ['validate' => 'EditPassword']);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Your password has been updated'));
                if (is_array($this->Cookie->read('CookieAuth'))) {
                    $this->Cookie->configKey('CookieAuth', [
                        'expired' => Setting::readOrFail('Member.RememberCookieExpired'),
                        'domain' => $this->request->host(),
                        'httpOnly' => !$this->request->is('ssl'),
                        'secure' => $this->request->is('ssl')]);
                    $this->Cookie->write('CookieAuth', [
                        'password' => $this->request->data['re_password'],
                    ]);
                }

                return $this->redirect([]);
            } else {
                $this->Flash->error(__('The password could not be change'));
            }
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Deactive method
     *
     * @return \Cake\Network\Response|null Redirects to referer
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deactive()
    {
        $this->request->allowMethod(['post', 'put']);
        if (!Setting::read('Member.AnyoneCanDeactive')) {
            throw new NotFoundException(__('Page not found'));
        }
        if ($this->Users->deactive($this->Auth->user('id'))) {
            $url = Router::url([
                    'prefix' => 'admin',
                    'controller' => 'Users',
                    'action' => 'login',
                    '_full' => true,
                ]);
            TableRegistry::get('EmailQueue')
                ->enqueue($this->Auth->user('email'), [
                    'user' => $this->Auth->user(),
                    'url' => $url
                ], [
                    'subject' => __('Your account has been deactivated'),
                    'template' => 'Users/deactivated',
                    'format' => 'html',
                    'layout' => 'default']);

                return $this->redirect(['action' => 'logout']);
        }
        $this->Flash->error(__('Unable to deactive your account. Please try again or contact to your administrator'));

        return $this->redirect($this->referer());
    }

    /**
     * Login function
     * @return Cake\Network\Response
     */
    public function login()
    {
        if ($this->Auth->user()) {
            return $this->redirect($this->Auth->redirectUrl());
        }
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                $this->Flash->success(__('Login successful'));

                return $this->redirect($this->Auth->redirectUrl());
            }
        }
    }

    /**
     * Logout function
     * @return Cake\Network\Response
     */
    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Password recovery
     * $token hash from $user->email . $user->token_created . $user->id
     * @return Cake\Network\Response
     */
    public function lostPassword()
    {
        if ($this->request->is('post')) {
            if ($this->Recaptcha->verify()) {
                $tokenData = $this->Users->createToken($this->request->data['email'], true, Setting::readOrFail('Member.ResetPasswordTokenExpired'));
                if ($tokenData) {
                    $url = Router::url([
                            'prefix' => 'admin',
                            'controller' => 'Users',
                            'action' => 'resetPassword',
                            $tokenData['token'],
                            $tokenData['user']->email,
                            '_full' => true,
                        ]);
                    TableRegistry::get('EmailQueue')
                        ->enqueue($tokenData['user']->email, [
                                'user' => $tokenData['user'],
                                'expired' => $tokenData['expired']->i18nFormat(),
                                'url' => $url
                            ], [
                                'subject' => __('Reset Password'),
                                'template' => 'Users/reset_password',
                                'format' => 'html',
                                'layout' => 'default'
                            ]);
                    $this->Flash->success(__('Please check your email to create a new password.'));

                    return $this->redirect(['action' => 'login']);
                }
                $this->Flash->error(__('Email does not exist/inactive. Please try again!'));
            } else {
                $this->Flash->error(__('Please pass Google Recaptcha first'));
            }
        }
    }

    /**
     * Reset password
     * @param string $token hash from $user->email . $user->token_created . $user->id
     * @param string $email email
     * @return Cake\Network\Response
     */
    public function resetPassword($token = null, $email = null)
    {
        if (!$token || !$email) {
            throw new NotFoundException(__('Missing required information. Please read email carefully and try again!'));
        }
        $user = $this->Users->findByEmailAndStatus($email, true)->first();
        if (!$user) {
            throw new RecordNotFoundException(__('Account not found. Please read email carefully and try again!'));
        }
        if ($token != Security::hash($user->email . $user->token_created . $user->id, 'sha1', true)) {
            throw new ForbiddenException(__('Invalid token. Please read email carefully and try again!'));
        }

        if (!$user->token_created->wasWithinLast(Setting::readOrFail('Member.ResetPasswordTokenExpired'))) {
            $this->Flash->error(__('Your request has been expired. Please create a new request!'));

            return $this->redirect(['action' => 'lostPassword']);
        }
        unset($user->password);
        if ($this->request->is('put')) {
            $allowedToChange = ['password', 're_password'];
            $data = array_intersect_key($this->request->data, array_flip($allowedToChange));
            $user = $this->Users->patchEntity($user, $data, ['validate' => 'ResetPassword']);
            $user->token_created = null;
            if ($this->Users->save($user)) {
                unset($user->password);
                $url = Router::url([
                        'prefix' => 'admin',
                        'controller' => 'Users',
                        'action' => 'login',
                        '_full' => true,
                    ]);
                TableRegistry::get('EmailQueue')
                    ->enqueue($user->email, [
                        'user' => $user,
                        'url' => $url
                    ], [
                        'subject' => __('Your password has been recovered'),
                        'template' => 'Users/password_recovered',
                        'layout' => 'default',
                        'format' => 'html'
                    ]);
                $this->Flash->success(__('Your password has been recovered. You can login right now!'));

                return $this->redirect(['action' => 'login']);
            }
        }
        $this->set(compact('user'));
    }

    /**
     * Register account
     * $token hash from $user->email . $user->token_created . $user->id
     * @return Cake\Network\Response
     */
    public function register()
    {
        if (!Setting::readOrFail('Member.AnyoneCanRegister')) {
            throw new ForbiddenException(__('Register function was disabled. Please contact to your administrator.'));
        }
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            if ($this->Recaptcha->verify()) {
                $role = $this->Users->Roles->findByAlias('member')->first();
                if (!$role) {
                    throw new RecordNotFoundException(__('Default role for register account not found. Please contact to your administrator immediately'));
                }
                $user->role_id = $role->id;
                $user->status = false;
                $user = $this->Users->patchEntity($user, $this->request->data, ['validate' => 'Register']);
                if ($this->Users->save($user)) {
                    $tokenData = $this->Users->createToken($user->email, $user->status, Setting::readOrFail('Member.RegisterTokenExpired'));
                    $url = Router::url([
                            'prefix' => 'admin',
                            'controller' => 'Users',
                            'action' => 'activeAccount',
                            $tokenData['token'],
                            $user->email,
                            '_full' => true,
                        ]);
                    TableRegistry::get('EmailQueue')
                        ->enqueue($user->email, [
                            'user' => $user,
                            'expired' => $tokenData['expired']->i18nFormat(),
                            'url' => $url
                        ], [
                            'subject' => __('Create account'),
                            'template' => 'Users/register',
                            'format' => 'html',
                            'layout' => 'default'
                        ]);
                    $this->Flash->success(__('Please check your email to verify account.'));

                    return $this->redirect(['action' => 'login']);
                }
            } else {
                $this->Flash->error(__('Please pass Google Recaptcha first'));
            }
        }
        $this->set(compact('user'));
    }

    /**
     * Active Account
     * @param string $token hash from $user->email . $user->token_created . $user->id
     * @param string $email email
     * @return Cake\Network\Response
     */
    public function activeAccount($token = null, $email = null)
    {
        if (!$token || !$email) {
            throw new NotFoundException(__('Missing required information. Please read email carefully and try again.'));
        }
        $user = $this->Users->findByEmailAndStatus($email, false)->first();
        if (!$user) {
            throw new RecordNotFoundException(__('Account not found or already activated. Please read email carefully and try again.'));
        }
        if ($token != Security::hash($user->email . $user->token_created . $user->id, 'sha1', true)) {
            throw new ForbiddenException(__('Invalid token. Please read email carefully and try again.'));
        }
        if (!$user->token_created->wasWithinLast(Setting::readOrFail('Member.RegisterTokenExpired'))) {
            throw new ForbiddenException(__('Your request has been expired. Please contact to your administrator.'));
        }
        unset($user->password);
        if ($this->request->is('put')) {
            $allowedToChange = ['password', 're_password', 'full_name'];
            $data = array_intersect_key($this->request->data, array_flip($allowedToChange));
            $user = $this->Users->patchEntity($user, $data, ['validate' => 'ActiveAccount']);
            $user->status = true;
            if ($this->Users->save($user)) {
                unset($user->password);
                $url = Router::url([
                        'prefix' => 'admin',
                        'controller' => 'Users',
                        'action' => 'login',
                        '_full' => true,
                    ]);
                TableRegistry::get('EmailQueue')
                    ->enqueue($user->email, [
                        'user' => $user,
                        'url' => $url,
                        ], [
                            'subject' => __('Your account has been activated'),
                            'template' => 'Users/account_verified',
                            'layout' => 'default',
                            'format' => 'html'
                        ]);
                $this->Flash->success(__('Your account has been activated. You can login right now'));

                return $this->redirect(['action' => 'login']);
            }
        }
        $this->set(compact('user'));
    }
}
