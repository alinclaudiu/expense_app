<?php
namespace App\Model\Table;

use App\Model\Entity\Role;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Roles Model
 *
 * @property \Cake\ORM\Association\HasMany $Users
 */
class VendorsTable extends Table
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

        $this->table('vendors');
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
            ->add('alias', 'Search.Like', [
                'before' => true,
                'after' => true,
                'field' => [$this->aliasField('alias')]
            ])
            ->add('name', 'Search.Like', [
                'before' => true,
                'after' => true,
                'field' => [$this->aliasField('name')]
            ])
            ->add('created', 'Search.Callback', [
                'callback' => function ($query, $args, $manager) {
                    return $query->andWhere(["DATE(created) >=" => $args['created']]);
                }
            ])
            ->add('modified', 'Search.Callback', [
                'callback' => function ($query, $args, $manager) {
                    return $query->andWhere(["DATE(updated) <=" => $args['modified']]);
                }
            ]);

        $this->hasMany('Expenses', [
            'foreignKey' => 'expenses_type_id',
            'className' => 'App.Vendors',
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
            ->notEmpty('name');

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

        return $rules;
    }
}
