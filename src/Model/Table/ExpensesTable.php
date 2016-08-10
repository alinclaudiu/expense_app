<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\FrozenTime;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Security;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Roles
 * @property \Cake\ORM\Association\HasMany $Posts
 */
class ExpensesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('expenses');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);
        $this->addBehavior('Search.Search');
        $this->searchManager()
            ->add('id', 'Search.Value', [
                'field' => $this->aliasField('id')
            ])
            ->add('reference_no', 'Search.Value')
            ->add('status', 'Search.Value')
            ->add('name', 'Search.Like', [
                'before' => true,
                'after' => true,
                'field' => [$this->aliasField('full_name')]
            ])
            ->add('created', 'Search.Callback', [
                'callback' => function ($query, $args, $manager) {
                    return $query->andWhere(["DATE(created) >=" => $args['created']]);
                }
            ])
            ->add('updated', 'Search.Callback', [
                'callback' => function ($query, $args, $manager) {
                    return $query->andWhere(["DATE(updated) <=" => $args['updated']]);
                }
            ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ExpensesTypes', [
            'foreignKey' => 'expenses_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Vendors', [
            'foreignKey' => 'vendor_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('name', 'create')
            ->notEmpty('name')
            ->add('name', 'notBlank', [
                'rule' => 'notBlank',
                'message' => __('Email cannot be blank')])
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => __('Title already in use. Please choose another one')]);

        $validator
            ->requirePresence('description', 'create')
            ->notEmpty('description')
            ->add('description', 'notBlank', [
                'rule' => 'notBlank',
                'message' => __('Description cannot be blank')]);

        $validator
            ->requirePresence('amount', 'create')
            ->notEmpty('amount')
            ->add('amount', 'notBlank', [
                'rule' => 'notBlank',
                'message' => __('Amount cannot be blank')]);

        $validator
            ->requirePresence('expense_date', 'create')
            ->notEmpty('expense_date');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['name']));
        $rules->add($rules->existsIn(['expense_type_id'], 'ExpensesTypes'));
        $rules->add($rules->existsIn(['vendor_id'], 'Vendors'));
        return $rules;
    }

    /**
     * Register validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationRegister(Validator $validator)
    {
        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email')
            ->add('email', 'notBlank', [
                'rule' => 'notBlank',
                'message' => __('Email cannot be blank')])
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => __('Email already in use. Please choose another one')
            ]);
        return $validator;
    }


}
