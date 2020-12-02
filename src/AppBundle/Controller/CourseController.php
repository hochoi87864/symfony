<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Course;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CourseController extends Controller
{
    /**
     * @Route("create-course")
     */
    public function createAction(Request $request)
    {
        $course = new Course();
        $form = $this->createFormBuilder($course)
            ->add('name', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'New Course'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $course = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($course);
            $em->flush();

            return $this->redirect('/view-course/' . $course->getId());
        }

        return $this->render(
            'course/edit.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/view-course/{id}")
     */
    public function viewAction($id)
    {

        $course = $this->getDoctrine()
            ->getRepository('AppBundle:Course')
            ->find($id);

        if (!$course) {
            throw $this->createNotFoundException(
                'There are no course with the following id: ' . $id
            );
        }

        return $this->render(
            'course/view.html.twig',
            array('course' => $course)
        );
    }

    /**
     * @Route("/show-course")
     */
    public function showAction()
    {

        $course = $this->getDoctrine()
            ->getRepository('AppBundle:Course')
            ->findAll();

        return $this->render(
            'course/show.html.twig',
            array('course' => $course)
        );
    }

    /**
     * @Route("/delete-course/{id}")
     */
    public function deleteAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $course = $em->getRepository('AppBundle:Course')->find($id);

        if (!$course) {
            throw $this->createNotFoundException(
                'There are no course with the following id: ' . $id
            );
        }

        $em->remove($course);
        $em->flush();

        return $this->redirect('/show-course');
    }

    /**
     * @Route("/update-course/{id}")
     */
    public function updateAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $course = $em->getRepository('AppBundle:Course')->find($id);

        if (!$course) {
            throw $this->createNotFoundException(
                'There are no course with the following id: ' . $id
            );
        }

        $form = $this->createFormBuilder($course)
            ->add('name', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Update'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $course = $form->getData();
            $em->flush();

            return $this->redirect('/view-course/' . $id);
        }

        return $this->render(
            'course/edit.html.twig',
            array('form' => $form->createView())
        );
    }
}
