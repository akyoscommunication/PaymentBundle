<?php

namespace Akyos\PaymentBundle\Form\Type;

use Akyos\PaymentBundle\Entity\PaymentOptions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentOptionsType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('activateStripeLive', CheckboxType::class, [
				'label' => 'Stripe: Activer le mode Live',
				'help' => 'Une fois le site en prod, si vous êtes sûr que tout est bien configuré et que le site utilise le paiement Stripe, activez cette option pour passer au mode Live.',
				'required' => false
			])
		;
	}
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => PaymentOptions::class,
		]);
	}
}
