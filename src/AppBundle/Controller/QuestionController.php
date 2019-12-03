<?php

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Question;

class QuestionController extends FOSRestController
{
    /**
     * @Rest\Get("/api/question")
     */
    public function getAction()
    {
        $result = $this->getDoctrine()->getRepository('AppBundle:Question')->findAll();
        if (!$result) {
            return new View("there are no users exist", Response::HTTP_NOT_FOUND);
        }
        return $result;
    }

    /**
     * @Rest\Get("/api/question/{id}")
     */
    public function idAction($id)
    {
        $questionEntity = $this->getDoctrine()->getRepository('AppBundle:Question')->find($id);
        if (!$questionEntity) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }

        return array(
            'data' => $questionEntity,
            'success' => Response::HTTP_OK
        );
    }

    /**
     * @Rest\Post("/api/question")
     */
    public function postAction(Request $request)
    {
        $questionEntity  = new Question;
        $name = $request->get('name');
        $type = $request->get('type');
        $answers = $request->get('answers');
        $quiz = $request->get('quiz');

        if (empty($name) || empty($type) || empty($answers)) {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }

        // Get quiz entity
        $quizEntity = $this->getDoctrine()->getRepository('AppBundle:Quiz')->find($quiz);

        if (!$quizEntity) {
            return new View("Quiz not found", Response::HTTP_NOT_FOUND);
        }

        $questionEntity->setName($name);
        $questionEntity->setType($type);
        $questionEntity->setAnswers($answers);
        $questionEntity->setQuiz($quizEntity);
        $questionEntity->setCreatedAt(new \DateTime());
        $questionEntity->setModifiedAt(new \DateTime());

        // Get entity manager
        $em = $this->getDoctrine()->getManager();
        $em->persist($questionEntity);
        $em->flush();

        return array(
            'data' => $questionEntity,
            'success' => Response::HTTP_OK
        );
    }

    /**
     * @Rest\Put("/api/question/{id}")
     */
    public function updateAction($id, Request $request)
    {
        $name = $request->get('name');
        $type = $request->get('type');
        $answers = $request->get('answers');
        $quiz = $request->get('quiz');

        // Get entity manager
        $em = $this->getDoctrine()->getManager();
        $questionEntity = $this->getDoctrine()->getRepository('AppBundle:Question')->find($id);

        // Get quiz entity
        $quizEntity = $this->getDoctrine()->getRepository('AppBundle:Quiz')->find($quiz);

        if (!$quizEntity) {
            return new View("Quiz not found", Response::HTTP_NOT_FOUND);
        }

        if (!$questionEntity) {
            return new View("Question not found", Response::HTTP_NOT_FOUND);
        } else if (!empty($name) && !empty($type)) {
            $questionEntity->setName($name);
            $questionEntity->setType($type);
            $questionEntity->setAnswers($answers);
            $questionEntity->setQuiz($quizEntity);
            $questionEntity->setModifiedAt(new \DateTime());

            $em->flush();

            return array(
                'data' => $questionEntity,
                'success' => Response::HTTP_OK
            );
        } else
            return new View("Question name or title cannot be empty", Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Rest\Delete("/api/question/{id}")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $questionEntity = $this->getDoctrine()->getRepository('AppBundle:Question')->find($id);

        if (!$questionEntity) {
            return new View("Question not found", Response::HTTP_NOT_FOUND);
        } else {
            $em->remove($questionEntity);
            $em->flush();
        }
    }
}
