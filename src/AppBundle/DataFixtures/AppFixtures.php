<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Quiz;
use AppBundle\Entity\User;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class AppFixtures extends Fixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Sets the container.
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        // Create user
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user = new User;
        $username = 'admin';
        $email = 'admin@example.com';
        $password = $this->encoder->encodePassword($user, 'password');
        $firstName = 'Francisco';
        $lastName = 'Bueno';

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEnabled(true);

        $manager->persist($user);
        $manager->flush();

        // Create quizzes
        $generator = Factory::create();
        $populator = new Populator($generator, $manager);

        $populator->addEntity(Quiz::class, 7, [
            'name'     => function () {
                $names = [
                    'Adrenal Fatigue Quiz',
                    'What Is Your Business Strength Persona',
                    'Metabolism Type Quiz',
                    'Employee Evaluation Form',
                    'How Many Of These Words Do You Actually Know',
                    'Do You Have A Good Work/Life Balance',
                    'Which US Region Should You Visit',
                ];
                return $names[array_rand($names)];
            },
            'title'     => function () {
                $titles = [
                    'Adrenal Fatigue Quiz?',
                    'What Is Your Business Strength Persona?',
                    'Metabolism Type Quiz?',
                    'Employee Evaluation Form?',
                    'How Many Of These Words Do You Actually Know?',
                    'Do You Have A Good Work/Life Balance?',
                    'Which US Region Should You Visit?',
                ];
                return $titles[array_rand($titles)];
            },
            'description' => function () {
                return 'Lorem fistrum apetecan no te digo trigo por no llamarte Rodrigor ut fistro ese pedazo de.';
            },
            'action'      => function () {
                return 'Start Quiz';
            },
            'image'      => function () {
                $images = [
                    '/uploads/quiz/quiz-5de2a958c10a1.jpg',
                    '/uploads/quiz/quiz-5de2a958c10a2.jpg',
                    '/uploads/quiz/quiz-5de2aaa5e3be5.jpg',
                    '/uploads/quiz/quiz-5de2aab58702d.jpg',
                    '/uploads/quiz/quiz-5de2afbe55d0a.jpg',
                    '/uploads/quiz/quiz-5de2afbe55d0v.jpg',
                    '/uploads/quiz/quiz-5de2afbe55d21.jpg',
                ];
                return $images[array_rand($images)];
            },
            'createdAt'      => function () {
                return new \DateTime();
            },
            'modifiedAt'      => function () {
                return new \DateTime();
            }
        ]);

        $populator->execute();
    }
}
