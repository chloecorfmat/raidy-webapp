<?php
/**
 * Created by PhpStorm.
 * User: chloecorfmat
 * Date: 03/01/2019
 * Time: 17:49.
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ColorpickerType extends ColorType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'colorpicker';
    }

    /**
     * {@inheritdoc}
     */
    public function transform($data)
    {
        // Model data should not be transformed
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
    {
        return null === $data ? '' : $data;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type'] = 'colorpicker';
    }
}
