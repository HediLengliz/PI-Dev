<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Service\DatabaseService;
use App\Controller\UserController;

class UpdateUserType extends AbstractType
{
    private DatabaseService $databaseService;
    private UserController $userController;

    public function __construct(DatabaseService $databaseService, UserController $userController)
    {
        $this->databaseService = $databaseService;
        $this->userController = $userController;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('cin')
        ->add('firstName')
        ->add('lastName')
        ->add('email')
        ->add('address')
        ->add('phoneNumber')
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Expert' => 'ROLE_EXPERT',
                'Pharmacist' => 'ROLE_PHARMACIST',
                'Doctor' => 'ROLE_DOCTOR',
                'Agent' => 'ROLE_AGENT',
            
                'Admin' => 'ROLE_ADMIN',
                'Super Admin' => 'ROLE_SUPER_ADMIN',

            ],
            'multiple' => true,
            'expanded' => false,
            'required' => true,

        ])
        ->add('claims', ChoiceType::class, [
            'choices' => $this->getClaimsChoices(),
            'multiple' => true,
            'expanded' => false,
            'attr' => [
                'class' => 'form-control js-example-basic-multiple',
            ],
            'required' => false,
            'data' => $options['data']->getClaims(), // Set the initial claims data


        ]);
}


private function getClaimsChoices(): array
    {
        $response = $this->userController->listTables($this->databaseService);
        $tablesData = json_decode($response->getContent(), true);

        $choices = [];

        foreach ($tablesData as $tableData) {
            foreach ($tableData as $tableName => $permissions) {
                if ($tableName!='reset_password_request') {
                    
                
                    $choices[$tableName] = [
                        'All ' . $tableName .' priviliges' => 'all_' . $tableName, // Add a "Select All" option
                        'Can Create ' . $tableName => 'c_' . $tableName,
                        'Can Read ' . $tableName => 'r_' . $tableName,
                        'Can Update ' . $tableName => 'u_' . $tableName,
                        'Can Delete ' . $tableName => 'd_' . $tableName,
                    ];
                }
            }
        }

        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
