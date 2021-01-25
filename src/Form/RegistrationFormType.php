<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{
    private $groupRepository;
    private $translator;

    public function __construct(TranslatorInterface $translator, GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->translator=$translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => $this->translator->trans('You should agree to our terms.'),
                    ]),
                ],
            ])
            ->add('group', EntityType::class, [
                'class' => Group::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('b')
                        ->where('b.id !=4');
                },
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('Please enter a password'),
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('Your password should be at least {{ limit }} characters'),
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    /**
     * @return array
     */
    public function renderChooseOptions()
    {
        $groups = $this->groupRepository->findAll();
        $choose = [];
        foreach ($groups as $item) {
            $choose[$item->getName()] = $item->getId();
        }
        return $choose;
    }
}
