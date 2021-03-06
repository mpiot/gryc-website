<?php

/*
 * Copyright 2015-2018 Mathieu Piot.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Form\Type;

use App\Entity\User;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactUsType extends AbstractType
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, [
                'placeholder' => 'Choose a category',
                'choices' => [
                    'Account' => 'Account',
                    'Bug report' => 'Bug report',
                    'Informations request' => 'Informations request',
                    'Other (not listed)' => 'Other (not listed)',
                ],
            ])
            ->add('subject', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'rows' => 20,
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('recaptcha', EWZRecaptchaType::class, [
                'label' => false,
                'mapped' => false,
                'constraints' => [
                    new RecaptchaTrue(),
                ],
                'attr' => [
                    'options' => [
                        'theme' => 'light',
                        'type' => 'image',
                        'size' => 'invisible',
                        'defer' => true,
                        'async' => true,
                        'bind' => 'contact_us_submit', // this is the id of the form submit button
                    ],
                ],
            ])
        ;

        $formModifier = function (FormInterface $form, $user = null) {
            $form
                ->add('firstName', TextType::class, [
                    'data' => is_a($user, User::class) ? $user->getFirstName() : null,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ])
                ->add('lastName', TextType::class, [
                    'data' => is_a($user, User::class) ? $user->getLastName() : null,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ])
                ->add('email', EmailType::class, [
                    'data' => is_a($user, User::class) ? $user->getEmail() : null,
                    'constraints' => [
                        new Email([
                            'checkMX' => true,
                        ]),
                    ],
                ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
            $user = $this->tokenStorage->getToken()->getUser();
            $form = $event->getForm();

            $formModifier($form, $user);
        });
    }
}
