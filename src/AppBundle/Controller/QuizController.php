<?php

namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Quiz;

class QuizController extends FOSRestController
{
    /**
     * @Rest\Get("/api/quiz")
     */
    public function getAction()
    {
        $result = $this->getDoctrine()->getRepository('AppBundle:Quiz')->findAll();
        if (!$result) {
            return new View("There are no quizzes", Response::HTTP_NOT_FOUND);
        }
        return $result;
    }

    /**
     * @Rest\Get("/api/quiz/{id}")
     */
    public function idAction($id)
    {
        $quizEntity = $this->getDoctrine()->getRepository('AppBundle:Quiz')->find($id);
        if (!$quizEntity) {
            return new View("Quiz not found", Response::HTTP_NOT_FOUND);
        }

        return array(
            'data' => $quizEntity,
            'success' => Response::HTTP_OK
        );
    }

    /**
     * @Rest\Post("/api/quiz")
     */
    public function postAction(Request $request)
    {
        $quizEntity = new Quiz;
        $name = $request->get('name');
        $title = $request->get('title');
        $description = $request->get('description');
        $action = $request->get('action');
        $publish = $request->get('publish');
        $image = $request->get('image');

        if (empty($name) || empty($title) || empty($description) || empty($action)) {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }

        $quizEntity->setName($name);
        $quizEntity->setTitle($title);
        $quizEntity->setDescription($description);
        $quizEntity->setAction($action);
        $quizEntity->setPublish($publish);
        $quizEntity->setCreatedAt(new \DateTime());
        $quizEntity->setModifiedAt(new \DateTime());

        $base64 = $this->checkBase64($image);

        if ($base64) {
            $imagePath = 'uploads/quiz/quiz' . '-' . uniqid() . '.' . $base64['type'];
            file_put_contents($imagePath, $base64['data']);

            if (file_put_contents($imagePath, $base64['data'])) {
                $quizEntity->setImage($imagePath);
            }
        }

        // Get entity manager
        $em = $this->getDoctrine()->getManager();
        $em->persist($quizEntity);
        $em->flush();

        return array(
            'data' => $quizEntity,
            'success' => Response::HTTP_OK
        );
    }

    /**
     * @Rest\Put("/api/quiz/{id}")
     */
    public function updateAction($id, Request $request)
    {
        $name = $request->get('name');
        $title = $request->get('title');
        $description = $request->get('description');
        $action = $request->get('action');
        $publish = $request->get('publish');
        $image = $request->get('image');

        // Get entity manager
        $em = $this->getDoctrine()->getManager();
        $quizEntity = $this->getDoctrine()->getRepository('AppBundle:Quiz')->find($id);

        if (!$quizEntity) {
            return new View("Quiz not found", Response::HTTP_NOT_FOUND);
        } else if (!empty($name) && !empty($title)) {
            $quizEntity->setName($name);
            $quizEntity->setTitle($title);
            $quizEntity->setDescription($description);
            $quizEntity->setAction($action);
            $quizEntity->setPublish($publish);
            $quizEntity->setModifiedAt(new \DateTime());

            $base64 = $this->checkBase64($image);

            if ($base64) {
                $imagePath = 'uploads/quiz/quiz' . '-' . uniqid() . '.' . $base64['type'];
                file_put_contents($imagePath, $base64['data']);

                if (file_put_contents($imagePath, $base64['data'])) {
                    $quizEntity->setImage($imagePath);
                }
            }

            $em->flush();
            return array(
                'data' => $quizEntity,
                'success' => Response::HTTP_OK
            );
        } else
            return new View("Quiz name or title cannot be empty", Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @Rest\Delete("/api/quiz/{id}")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $quizEntity = $this->getDoctrine()->getRepository('AppBundle:Quiz')->find($id);

        if (!$quizEntity) {
            return new View("Quiz not found", Response::HTTP_NOT_FOUND);
        } else {
            $em->remove($quizEntity);
            $em->flush();
        }
    }

    protected function checkBase64($data)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                throw new \Exception('Invalid image type');
            }
            $data = base64_decode($data);
            if ($data === false) {
                throw new \Exception('Base64_decode failed');
            }
            return ["data" => $data, "type" => $type];
        } else {
            return;
        }
    }
}
